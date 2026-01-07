<?php
// public/api/movement.php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';

// Ensure user is logged in
check_auth();

try {
    $startDate = $_GET['start_date'] ?? null;
    $endDate = $_GET['end_date'] ?? null;
    $agent = $_GET['agent'] ?? '';
    $pnr = $_GET['pnr'] ?? '';
    $q = $_GET['q'] ?? '';
    
    // Separate pages for each category
    $page_umrah = isset($_GET['page_umrah']) ? (int)$_GET['page_umrah'] : 1;
    $page_hajji = isset($_GET['page_hajji']) ? (int)$_GET['page_hajji'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    
    $offset_umrah = ($page_umrah - 1) * $limit;
    $offset_hajji = ($page_hajji - 1) * $limit;

    $params = [];
    $filterSql = "";

    if ($startDate) {
        $filterSql .= " AND m.created_date >= :start_date";
        $params[':start_date'] = $startDate;
    }
    if ($endDate) {
        $filterSql .= " AND m.created_date <= :end_date";
        $params[':end_date'] = $endDate;
    }
    if (!empty($agent)) {
        $filterSql .= " AND m.agent_name LIKE :agent";
        $params[':agent'] = "%$agent%";
    }
    if (!empty($pnr)) {
        $filterSql .= " AND m.pnr = :pnr";
        $params[':pnr'] = $pnr;
    }
    if (!empty($q)) {
        $filterSql .= " AND (m.pnr LIKE :q OR m.tour_code LIKE :q OR m.agent_name LIKE :q)";
        $params[':q'] = "%$q%";
    }

    // Function to fetch paginated category
    $fetchCategory = function($category, $limit, $offset) use ($pdo, $filterSql, $params) {
        $catParams = $params;
        $catParams[':cat'] = $category;
        
        $countSql = "SELECT COUNT(*) FROM movements m WHERE category = :cat" . $filterSql;
        $stmtCount = $pdo->prepare($countSql);
        $stmtCount->execute($catParams);
        $totalItems = (int)$stmtCount->fetchColumn();

        $sql = "
            SELECT 
                m.*,
                (SELECT status FROM flight_status_snapshots fs 
                 JOIN flight_legs fl ON fl.id = fs.flight_leg_id 
                 WHERE fl.movement_id = m.id 
                 ORDER BY fs.last_seen_at DESC LIMIT 1) as live_status
            FROM movements m
            WHERE category = :cat " . $filterSql . "
            ORDER BY m.created_date DESC, m.id DESC
            LIMIT $limit OFFSET $offset
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($catParams);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $rows,
            'pagination' => [
                'totalItems' => $totalItems,
                'totalPages' => ceil($totalItems / $limit),
                'currentPage' => (int)($offset / $limit) + 1,
                'limit' => $limit
            ]
        ];
    };

    $umrahRes = $fetchCategory('UMRAH', $limit, $offset_umrah);
    $hajjiRes = $fetchCategory('HAJJI', $limit, $offset_hajji);

    // KPI still needs overall context or separate? Let's do a quick overall count for KPIs
    $stmtKpi = $pdo->prepare("SELECT dp1_status, dp2_status, fp_status, ticketing_done FROM movements m WHERE 1=1" . $filterSql);
    $stmtKpi->execute($params);
    $allRows = $stmtKpi->fetchAll(PDO::FETCH_ASSOC);

    $kpi = ['total' => count($allRows), 'paid' => 0, 'partial' => 0, 'unpaid' => 0, 'done' => 0];
    foreach ($allRows as $row) {
        if ($row['ticketing_done'] == 1) $kpi['done']++;
        
        if ($row['fp_status'] === 'PAID') $kpi['paid']++;
        elseif ($row['dp1_status'] === 'PAID' || $row['dp2_status'] === 'PAID') $kpi['partial']++;
        else $kpi['unpaid']++;
    }

    header('Content-Type: application/json');
    echo json_encode([
        'kpi' => $kpi,
        'umrah' => $umrahRes,
        'hajji' => $hajjiRes
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}