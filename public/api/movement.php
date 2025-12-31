<?php
// public/api/movement.php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';

// Ensure user is logged in
check_auth();

header('Content-Type: application/json');

try {
    $date = $_GET['date'] ?? date('Y-m-d');
    $airline = $_GET['airline'] ?? '';
    $origin = $_GET['origin'] ?? '';
    $dest = $_GET['dest'] ?? '';
    $status = $_GET['status'] ?? '';
    $q = $_GET['q'] ?? '';

    $sql = "
        SELECT
            l.id, l.ref_type, l.ref_id, l.flight_ident, l.origin_iata, l.dest_iata, l.dep_date,
            l.status_normalized as status, l.last_fetched_at,
            s.sched_dep_utc, s.sched_arr_utc, s.est_dep_utc, s.est_arr_utc, s.act_dep_utc, s.act_arr_utc,
            s.delay_minutes, s.dep_gate, s.arr_gate, s.fetched_at as snapshot_updated
        FROM flight_legs l
        LEFT JOIN flight_status_snapshots s ON s.id = l.last_snapshot_id
        WHERE l.dep_date = :date
    ";

    $params = [':date' => $date];

    if (!empty($airline)) {
        $sql .= " AND l.airline_iata = :airline";
        $params[':airline'] = $airline;
    }
    if (!empty($origin)) {
        $sql .= " AND l.origin_iata = :origin";
        $params[':origin'] = $origin;
    }
    if (!empty($dest)) {
        $sql .= " AND l.dest_iata = :dest";
        $params[':dest'] = $dest;
    }
    if (!empty($status)) {
        $sql .= " AND l.status_normalized = :status";
        $params[':status'] = $status;
    }
    if (!empty($q)) {
        $sql .= " AND (l.flight_ident LIKE :q OR l.ref_id LIKE :q)";
        $params[':q'] = "%$q%";
    }

    $sql .= " ORDER BY l.dep_date ASC, s.sched_dep_utc ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate KPIs
    $kpi = [
        'total' => 0,
        'ontime' => 0,
        'delayed' => 0,
        'canceled' => 0
    ];

    $data = [];
    foreach ($rows as $r) {
        $kpi['total']++;
        if ($r['status'] === 'DELAYED') $kpi['delayed']++;
        elseif ($r['status'] === 'CANCELED') $kpi['canceled']++;
        elseif ($r['status'] === 'SCHEDULED' || $r['status'] === 'AIRBORNE' || $r['status'] === 'ARRIVED') $kpi['ontime']++; // Simplified logic

        // Format dates for display (convert UTC to simplistic local or just show as is)
        // Ideally we would convert to user's local time or airport local time
        $formatDate = function($d) { return $d ? date('H:i', strtotime($d)) : '-'; };

        $data[] = [
            'id' => $r['id'],
            'ref' => $r['ref_type'] ? $r['ref_type'] . '#' . $r['ref_id'] : '-',
            'origin' => $r['origin_iata'],
            'dest' => $r['dest_iata'],
            'flight_ident' => $r['flight_ident'],
            'sched_dep' => $formatDate($r['sched_dep_utc']),
            'est_dep' => $formatDate($r['est_dep_utc']),
            'act_dep' => $formatDate($r['act_dep_utc']),
            'sched_arr' => $formatDate($r['sched_arr_utc']),
            'est_arr' => $formatDate($r['est_arr_utc']),
            'act_arr' => $formatDate($r['act_arr_utc']),
            'status' => $r['status'],
            'delay_minutes' => $r['delay_minutes'] > 0 ? $r['delay_minutes'] . 'm' : '',
            'dep_gate' => $r['dep_gate'],
            'arr_gate' => $r['arr_gate'],
            'updated_at' => $r['last_fetched_at'] ? date('H:i:s', strtotime($r['last_fetched_at'])) : '-'
        ];
    }

    echo json_encode([
        'kpi' => $kpi,
        'data' => $data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
