<?php
// cron/cron_sync_flights.php
date_default_timezone_set('UTC');

// Adjust path to config and db connection
$config = require __DIR__ . '/../config/flight_api.php';
require_once __DIR__ . '/../includes/db_connect.php';

echo "[getcwd()]".date('Y-m-d H:i:s')."] Starting Flight Sync...\n";

// 1) Auto-seed dummy flights if empty (For MVP testing)
$forceSeed = isset($argv) && in_array('--force', $argv);
ensureDummyData($pdo, $forceSeed);

// 2) Prevent concurrent runs (MariaDB GET_LOCK)
$lockName = 'cron_sync_flights_lock';
$stmt = $pdo->query("SELECT GET_LOCK(" . $pdo->quote($lockName) . ", 1) AS l");
$lock = $stmt->fetch();
if (empty($lock['l'])) {
    die("Lock busy\n");
}

try {
    // 3) Select legs due for polling (and not final)
    $stmt = $pdo->prepare(
        "SELECT * FROM flight_legs
        WHERE (next_poll_at IS NULL OR next_poll_at <= UTC_TIMESTAMP())
          AND is_final = 0
        ORDER BY next_poll_at ASC
    ");
    $stmt->execute();
    $legs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($legs) . " flights to update.\n";

    foreach ($legs as $leg) {
        $pdo->beginTransaction();

        // 4) Fetch latest status
        $data = fetchFlightStatus($config['flight_api'], $leg);

        if (!$data) {
            echo "Failed to fetch: {$leg['flight_ident']}\n";
            // Backoff
            $pdo->prepare("UPDATE flight_legs SET fail_count=fail_count+1, next_poll_at=DATE_ADD(UTC_TIMESTAMP(), INTERVAL 10 MINUTE) WHERE id=?")
                ->execute([$leg['id']]);
            $pdo->commit();
            continue;
        }

        // 5) Insert snapshot
        $snapId = insertSnapshot($pdo, $leg['id'], $data);

        // 6) Load previous snapshot for diff
        $prev = null;
        if (!empty($leg['last_snapshot_id'])) {
            $ps = $pdo->prepare("SELECT * FROM flight_status_snapshots WHERE id=?");
            $ps->execute([$leg['last_snapshot_id']]);
            $prev = $ps->fetch(PDO::FETCH_ASSOC);
        }

        // 7) Create events based on diff
        createEventsFromDiff($pdo, $leg['id'], $snapId, $prev, $data);

        // 8) Update flight_legs
        $normalized = $data['status_normalized'];
        $isFinal = in_array($normalized, ['ARRIVED', 'CANCELED'], true) ? 1 : 0;
        $nextPollAt = computeNextPollAtUTC($data);

        $pdo->prepare(
            "UPDATE flight_legs
            SET last_snapshot_id=?, last_fetched_at=UTC_TIMESTAMP(),
                status_normalized=?, is_final=?,
                next_poll_at=?, poll_interval_min=?, fail_count=0
            WHERE id=?
        ")->execute([
            $snapId,
            $normalized,
            $isFinal,
            $nextPollAt,
            $data['poll_interval_min'],
            $leg['id']
        ]);

        $pdo->commit();
        echo "Updated: {$leg['flight_ident']} -> $normalized\n";
    }

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    $pdo->query("SELECT RELEASE_LOCK(" . $pdo->quote($lockName) . ")");
}

// --- Functions ---

function fetchFlightStatus(array $api, array $leg): ?array {
    // MOCK MODE for MVP
    if ($api['provider'] === 'mock' || empty($api['api_key'])) {
        return generateMockData($leg);
    }

    // Real API implementation (FlightAware example)
    $ident = $leg['flight_ident'];
    $url = rtrim($api['base_url'], '/') . "/flights/{$ident}";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "x-apikey: {$api['api_key']}",
            "Accept: application/json",
        ],
        CURLOPT_TIMEOUT => 20,
    ]);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http < 200 || $http >= 300 || !$resp) return null;

    $json = json_decode($resp, true);
    if (!is_array($json)) return null;
    
    // Simplification: take the first flight in the list
    $f = $json['flights'][0] ?? null; 
    if (!$f) return null;

    return [
        'provider_status' => $f['status'] ?? null,
        'status_normalized' => normalizeStatus($f['status'] ?? 'UNKNOWN'),
        'sched_dep_utc' => $f['scheduled_out'] ?? null,
        'sched_arr_utc' => $f['scheduled_in'] ?? null,
        'est_dep_utc'   => $f['estimated_out'] ?? null,
        'est_arr_utc'   => $f['estimated_in'] ?? null,
        'act_dep_utc'   => $f['actual_out'] ?? null,
        'act_arr_utc'   => $f['actual_in'] ?? null,
        'dep_terminal' => $f['terminal_origin'] ?? null,
        'dep_gate'     => $f['gate_origin'] ?? null,
        'arr_terminal' => $f['terminal_dest'] ?? null,
        'arr_gate'     => $f['gate_dest'] ?? null,
        'aircraft_type' => $f['aircraft_type'] ?? null,
        'delay_minutes' => 0, // Calculate from sched vs est
        'is_canceled' => !empty($f['cancelled']) ? 1 : 0,
        'is_diverted' => !empty($f['diverted']) ? 1 : 0,
        'raw_json' => $resp,
        'raw_hash' => hash('sha256', $resp),
        'poll_interval_min' => 60,
    ];
}

function generateMockData($leg) {
    // Determine status based on current time vs scheduled dep
    $now_ts = time();
    $dep_ts = strtotime($leg['dep_date'] . ' 10:00:00'); // Assume 10 AM departure

    $status = 'SCHEDULED'; // Initialize to SCHEDULED, then override if conditions met
    $act_dep = null;
    $est_dep = date('Y-m-d H:i:s', $dep_ts);
    $delay_minutes = 0;

    // Flights far in the past (more than 24 hours ago)
    if ($dep_ts < ($now_ts - (24 * 3600))) {
        $status = 'ARRIVED';
        $act_dep = date('Y-m-d H:i:s', $dep_ts + rand(3600*4, 3600*6)); // Arrived 4-6 hours after departure
        $est_dep = $act_dep; // Estimated also set to actual for past flights
    }
    // Flights around the current day (within ~24 hours of scheduled departure)
    elseif (abs($dep_ts - $now_ts) <= (24 * 3600)) {
        $diff_from_dep = $now_ts - $dep_ts; // Difference from scheduled departure to now

        if ($diff_from_dep < -3600) { // More than 1 hour before scheduled departure
            $status = 'SCHEDULED';
        } elseif ($diff_from_dep >= -3600 && $diff_from_dep < 0) { // Within 1 hour before scheduled departure
            $status = 'SCHEDULED';
            // Optionally, simulate slight delays close to departure
            if (rand(0, 100) < 20) { // 20% chance of small delay
                $delay_minutes = rand(10, 30);
                $est_dep = date('Y-m-d H:i:s', $dep_ts + ($delay_minutes * 60));
                $status = 'DELAYED';
            }
        } elseif ($diff_from_dep >= 0 && $diff_from_dep < 18000) { // Departed, now airborne (0 to 5 hours duration)
            $status = 'AIRBORNE';
            $act_dep = date('Y-m-d H:i:s', $dep_ts + rand(0, $delay_minutes * 60 + 120)); // Act dep slightly after sched or est
            if ($delay_minutes > 0) $est_dep = date('Y-m-d H:i:s', $dep_ts + ($delay_minutes * 60));
        } elseif ($diff_from_dep >= 18000) { // Arrived (after 5 hours flight duration)
            $status = 'ARRIVED';
            $act_dep = date('Y-m-d H:i:s', $dep_ts + 18000 + rand(0, 600)); // Arrived ~5 hours after dep, with slight variation
            if ($delay_minutes > 0) $est_dep = date('Y-m-d H:i:s', $dep_ts + ($delay_minutes * 60));
        }
    }
    // Else (far future), status remains SCHEDULED from initialization.

    // Randomly delay 1 specific flight (XX999) and ensure it keeps its status
    if ($leg['flight_number'] == '999') {
        $status = 'DELAYED';
        $est_dep = date('Y-m-d H:i:s', $dep_ts + 7200); // 2 hours delay
        $delay_minutes = 120;
    }


    return [
        'provider_status' => $status,
        'status_normalized' => $status,
        'sched_dep_utc' => date('Y-m-d H:i:s', $dep_ts),
        'sched_arr_utc' => date('Y-m-d H:i:s', $dep_ts + 18000), // +5 hours (example flight duration)
        'est_dep_utc'   => $est_dep,
        'est_arr_utc'   => date('Y-m-d H:i:s', strtotime($est_dep) + 18000),
        'act_dep_utc'   => $act_dep,
        'act_arr_utc'   => ($status === 'ARRIVED' && $act_dep) ? date('Y-m-d H:i:s', strtotime($est_dep) + 18000) : null,
        'dep_terminal' => 'T3',
        'dep_gate'     => 'A' . rand(1, 20),
        'arr_terminal' => 'Hajj',
        'arr_gate'     => 'B' . rand(1, 10),
        'aircraft_type' => 'B777-300ER',
        'delay_minutes' => $delay_minutes,
        'is_canceled' => 0, // Mock will not set canceled for now
        'is_diverted' => 0, // Mock will not set diverted for now
        'raw_json' => json_encode(['mock' => true, 'status_calc_diff' => $diff_from_dep ?? 'N/A']),
        'raw_hash' => md5(uniqid()),
        'poll_interval_min' => 60,
    ];
} // <--- Missing closing brace added here

function insertSnapshot(PDO $pdo, int $legId, array $d): int {
    $stmt = $pdo->prepare(
        "INSERT INTO flight_status_snapshots
    (leg_id, provider_status, status_normalized,
     sched_dep_utc, sched_arr_utc, est_dep_utc, est_arr_utc, act_dep_utc, act_arr_utc,
     dep_terminal, dep_gate, arr_terminal, arr_gate,
     aircraft_type, delay_minutes, is_canceled, is_diverted, raw_json, raw_hash)
    VALUES
    (:leg_id, :provider_status, :status_normalized,
     :sched_dep_utc, :sched_arr_utc, :est_dep_utc, :est_arr_utc, :act_dep_utc, :act_arr_utc,
     :dep_terminal, :dep_gate, :arr_terminal, :arr_gate,
     :aircraft_type, :delay_minutes, :is_canceled, :is_diverted, :raw_json, :raw_hash)
  ");
    $stmt->execute([
        ':leg_id' => $legId,
        ':provider_status' => $d['provider_status'],
        ':status_normalized' => $d['status_normalized'],
        ':sched_dep_utc' => $d['sched_dep_utc'],
        ':sched_arr_utc' => $d['sched_arr_utc'],
        ':est_dep_utc' => $d['est_dep_utc'],
        ':est_arr_utc' => $d['est_arr_utc'],
        ':act_dep_utc' => $d['act_dep_utc'],
        ':act_arr_utc' => $d['act_arr_utc'],
        ':dep_terminal' => $d['dep_terminal'],
        ':dep_gate' => $d['dep_gate'],
        ':arr_terminal' => $d['arr_terminal'],
        ':arr_gate' => $d['arr_gate'],
        ':aircraft_type' => $d['aircraft_type'],
        ':delay_minutes' => $d['delay_minutes'],
        ':is_canceled' => (int)$d['is_canceled'],
        ':is_diverted' => (int)$d['is_diverted'],
        ':raw_json' => $d['raw_json'],
        ':raw_hash' => $d['raw_hash'],
    ]);
    return (int)$pdo->lastInsertId();
}

function createEventsFromDiff(PDO $pdo, int $legId, int $snapId, ?array $prev, array $cur): void {
    $pairs = [
        ['STATUS_CHANGED', 'status_normalized'],
        ['EST_DEP_CHANGED', 'est_dep_utc'],
        ['EST_ARR_CHANGED', 'est_arr_utc'],
        ['GATE_DEP_CHANGED', 'dep_gate'],
        ['GATE_ARR_CHANGED', 'arr_gate'],
    ];

    foreach ($pairs as [$type, $key]) {
        $old = $prev[$key] ?? null;
        $new = $cur[$key] ?? null;
        if ((string)$old !== (string)$new && !($old === null && $new === null)) {
            $pdo->prepare(
                "INSERT INTO flight_status_events (leg_id, snapshot_id, event_type, old_value, new_value)
        VALUES (?, ?, ?, ?, ?)
      ")->execute([$legId, $snapId, $type, (string)$old, (string)$new]);
        }
    }
}

function normalizeStatus(string $s): string {
    $s = strtoupper(trim($s));
    if (strpos($s, 'CANCEL') !== false) return 'CANCELED';
    if (strpos($s, 'ARRIV') !== false) return 'ARRIVED';
    if (strpos($s, 'AIR') !== false) return 'AIRBORNE';
    if (strpos($s, 'DELAY') !== false) return 'DELAYED';
    if (strpos($s, 'SCHED') !== false) return 'SCHEDULED';
    return 'UNKNOWN';
}

function computeNextPollAtUTC(array &$d): ?string {
    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    $dep = !empty($d['sched_dep_utc']) ? new DateTimeImmutable($d['sched_dep_utc'], new DateTimeZone('UTC')) : null;

    if (!$dep) {
        $d['poll_interval_min'] = 120;
        return $now->modify('+120 minutes')->format('Y-m-d H:i:s');
    }

    $minutesToDep = (int)(($dep->getTimestamp() - $now->getTimestamp()) / 60);

    if ($minutesToDep > 48 * 60)      $d['poll_interval_min'] = 360; // 6 hours
    else if ($minutesToDep > 6 * 60)  $d['poll_interval_min'] = 60;  // 1 hour
    else if ($minutesToDep > -180)    $d['poll_interval_min'] = 1;   // Near/During flight (Fast poll)
    else                               $d['poll_interval_min'] = 180;

    return $now->modify('+' . $d['poll_interval_min'] . ' minutes')->format('Y-m-d H:i:s');
}

function ensureDummyData(PDO $pdo, bool $force = false) {
    // Check if data exists
    if (!$force) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM flight_legs");
        if ($stmt->fetchColumn() > 0) {
            // echo "Data already exists. Skipping seed (use --force to re-seed).\n";
            return;
        }
    }

    // Aggressively clear and re-seed for testing
    echo "Clearing and re-seeding dummy flight legs for +/- 6 Months...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE flight_legs;");
    $pdo->exec("TRUNCATE TABLE flight_status_snapshots;");
    $pdo->exec("TRUNCATE TABLE flight_status_events;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    $start_date = strtotime('-6 months');
    $end_date = strtotime('+6 months');
    $allFlights = [];

    for ($i = $start_date; $i <= $end_date; $i = strtotime('+1 day', $i)) {
        $dep_date = date('Y-m-d', $i);
        $flights = [
            ['SQ', '328', 'SQ328', 'SIN', 'JED', $dep_date],
            ['SV', '819', 'SV819', 'CGK', 'JED', $dep_date],
            ['GA', '980', 'GA980', 'CGK', 'JED', $dep_date],
            ['TK', '56',  'TK56',  'IST', 'CGK', $dep_date],
            ['XX', '999', 'XX999', 'CGK', 'JED', $dep_date], // Will be DELAYED
        ];
        $allFlights = array_merge($allFlights, $flights);
    }

    $stmt = $pdo->prepare("INSERT INTO flight_legs (airline_iata, flight_number, flight_ident, origin_iata, dest_iata, dep_date, next_poll_at) VALUES (?, ?, ?, ?, ?, ?, UTC_TIMESTAMP())");
    foreach($allFlights as $f) {
        $stmt->execute($f);
    }
    echo "Seeded " . count($allFlights) . " dummy flight legs.\n";
}
