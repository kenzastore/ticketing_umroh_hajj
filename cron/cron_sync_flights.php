<?php
// cron/cron_sync_flights.php
date_default_timezone_set('UTC');

// Adjust path to config and db connection
$config = require __DIR__ . '/../config/flight_api.php';
require_once __DIR__ . '/../includes/db_connect.php';

echo "[getcwd()]".date('Y-m-d H:i:s')."] Starting Flight Sync...\n";

// 1) Auto-seed dummy flights if empty (For MVP testing)
ensureDummyData($pdo);

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
        LIMIT 50
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
    $now = time();
    $dep = strtotime($leg['dep_date'] . ' 10:00:00'); // Assume 10 AM departure
    
    // Simulate progression
    $diff = $now - $dep;
    
    $status = 'SCHEDULED';
    $act_dep = null;
    $est_dep = date('Y-m-d H:i:s', $dep);
    
    if ($diff > -3600 && $diff < 0) { // 1 hour before
        $status = 'SCHEDULED'; 
    } elseif ($diff >= 0 && $diff < 18000) { // 0 to 5 hours duration
        $status = 'AIRBORNE';
        $act_dep = date('Y-m-d H:i:s', $dep + 120); // 2 mins late
    } elseif ($diff >= 18000) {
        $status = 'ARRIVED';
        $act_dep = date('Y-m-d H:i:s', $dep + 120);
    }

    // Randomly delay 1 flight
    if ($leg['flight_number'] == '999') {
        $status = 'DELAYED';
        $est_dep = date('Y-m-d H:i:s', $dep + 7200); // 2 hours delay
    }

    return [
        'provider_status' => $status,
        'status_normalized' => $status,
        'sched_dep_utc' => date('Y-m-d H:i:s', $dep),
        'sched_arr_utc' => date('Y-m-d H:i:s', $dep + 18000), // +5 hours
        'est_dep_utc'   => $est_dep,
        'est_arr_utc'   => date('Y-m-d H:i:s', strtotime($est_dep) + 18000),
        'act_dep_utc'   => $act_dep,
        'act_arr_utc'   => $status === 'ARRIVED' ? date('Y-m-d H:i:s', strtotime($est_dep) + 18000) : null,
        'dep_terminal' => 'T3',
        'dep_gate'     => 'A' . rand(1, 20),
        'arr_terminal' => 'Hajj',
        'arr_gate'     => 'B' . rand(1, 10),
        'aircraft_type' => 'B777-300ER',
        'delay_minutes' => ($status === 'DELAYED') ? 120 : 0,
        'is_canceled' => 0,
        'is_diverted' => 0,
        'raw_json' => json_encode(['mock' => true]),
        'raw_hash' => md5(uniqid()),
        'poll_interval_min' => 60,
    ];
}

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

function ensureDummyData(PDO $pdo) {
    $check = $pdo->query("SELECT COUNT(*) FROM flight_legs")->fetchColumn();
    if ($check == 0) {
        echo "Seeding dummy flight legs...\n";
        $today = date('Y-m-d');
        $flights = [
            ['SQ', '328', 'SQ328', 'SIN', 'JED', $today],
            ['SV', '819', 'SV819', 'CGK', 'JED', $today],
            ['GA', '980', 'GA980', 'CGK', 'JED', $today],
            ['TK', '56',  'TK56',  'IST', 'CGK', $today],
            ['XX', '999', 'XX999', 'CGK', 'JED', $today], // Will be DELAYED
        ];

        $stmt = $pdo->prepare("INSERT INTO flight_legs (airline_iata, flight_number, flight_ident, origin_iata, dest_iata, dep_date, next_poll_at) VALUES (?, ?, ?, ?, ?, ?, UTC_TIMESTAMP())");
        foreach($flights as $f) {
            $stmt->execute($f);
        }
    }
}
