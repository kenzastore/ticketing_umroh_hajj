<?php
require_once __DIR__ . '/../includes/db_connect.php';

try {
    $pdo->exec("DELETE FROM movements");
    
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $in2Days = date('Y-m-d', strtotime('+2 days'));
    $in5Days = date('Y-m-d', strtotime('+5 days'));

    $movements = [
        // Ticketing
        ['pnr' => 'TICK-PAST', 'agent_name' => 'Agent A', 'ticketing_deadline' => $yesterday, 'ticketing_done' => 0],
        ['pnr' => 'TICK-TMRW', 'agent_name' => 'Agent B', 'ticketing_deadline' => $tomorrow, 'ticketing_done' => 0],
        ['pnr' => 'TICK-DONE', 'agent_name' => 'Agent C', 'ticketing_deadline' => $tomorrow, 'ticketing_done' => 1],
        
        // DP1
        ['pnr' => 'DP1-AIR-PAST', 'agent_name' => 'Agent D', 'deposit1_airlines_date' => $yesterday, 'dp1_status' => 'PENDING'],
        ['pnr' => 'DP1-EEMW-TMRW', 'agent_name' => 'Agent E', 'deposit1_eemw_date' => $tomorrow, 'dp1_status' => 'PENDING'],
        ['pnr' => 'DP1-PAID', 'agent_name' => 'Agent F', 'deposit1_airlines_date' => $tomorrow, 'dp1_status' => 'PAID'],

        // DP2
        ['pnr' => 'DP2-AIR-TMRW', 'agent_name' => 'Agent G', 'deposit2_airlines_date' => $tomorrow, 'dp2_status' => 'PENDING'],
        ['pnr' => 'DP2-FAR', 'agent_name' => 'Agent H', 'deposit2_airlines_date' => $in5Days, 'dp2_status' => 'PENDING'],

        // FP
        ['pnr' => 'FP-EEMW-TMRW', 'agent_name' => 'Agent I', 'fullpay_eemw_date' => $tomorrow, 'fp_status' => 'PENDING'],
        ['pnr' => 'FP-PAST', 'agent_name' => 'Agent J', 'fullpay_airlines_date' => $yesterday, 'fp_status' => 'PENDING'],
    ];

    foreach ($movements as $m) {
        $fields = array_keys($m);
        $placeholders = array_fill(0, count($fields), '?');
        $sql = "INSERT INTO movements (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($m));
    }

    echo "Seeding completed successfully.\n";

} catch (PDOException $e) {
    die("Seeding failed: " . $e->getMessage() . "\n");
}
