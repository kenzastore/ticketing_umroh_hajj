<?php
// public/api/movement.php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';

// Ensure user is logged in
check_auth();

try {
    $date = $_GET['date'] ?? null;
    $agent = $_GET['agent'] ?? '';
    $pnr = $_GET['pnr'] ?? '';
    $q = $_GET['q'] ?? '';

    $sql = "
        SELECT 
            m.*,
            (SELECT status FROM flight_status_snapshots fs 
             JOIN flight_legs fl ON fl.id = fs.flight_leg_id 
             WHERE fl.movement_id = m.id 
             ORDER BY fs.last_seen_at DESC LIMIT 1) as live_status
        FROM movements m
        WHERE 1=1
    ";

    $params = [];

    if ($date) {
        $sql .= " AND m.created_date = :date";
        $params[':date'] = $date;
    }
    if (!empty($agent)) {
        $sql .= " AND m.agent_name LIKE :agent";
        $params[':agent'] = "%$agent%";
    }
    if (!empty($pnr)) {
        $sql .= " AND m.pnr = :pnr";
        $params[':pnr'] = $pnr;
    }
    if (!empty($q)) {
        $sql .= " AND (m.pnr LIKE :q OR m.tour_code LIKE :q OR m.agent_name LIKE :q)";
        $params[':q'] = "%$q%";
    }

    $sql .= " ORDER BY m.created_date DESC, m.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by category and calculate KPIs
    $umrah = [];
    $hajji = [];
    $kpi = [
        'total' => count($rows),
        'paid' => 0,
        'partial' => 0,
        'unpaid' => 0
    ];

    foreach ($rows as $row) {
        if ($row['category'] === 'HAJJI') {
            $hajji[] = $row;
        } else {
            $umrah[] = $row;
        }

        if ($row['fp_status'] === 'PAID') $kpi['paid']++;
        elseif ($row['dp1_status'] === 'PAID' || $row['dp2_status'] === 'PAID') $kpi['partial']++;
        else $kpi['unpaid']++;
    }

    header('Content-Type: application/json');
    echo json_encode([
        'kpi' => $kpi,
        'data' => [
            'umrah' => $umrah,
            'hajji' => $hajji
        ]
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}