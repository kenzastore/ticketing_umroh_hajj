<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/Notification.php';

echo "Running Reminder Service...\n";

try {
    // 1. Check Ticketing Deadlines (Alert 3 days before)
    $sql = "SELECT id, pnr, tour_code, ticketing_deadline FROM movements 
            WHERE ticketing_deadline IS NOT NULL 
            AND ticketing_done = 0
            AND ticketing_deadline <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
    
    $stmt = $pdo->query($sql);
    $deadlineGroups = $stmt->fetchAll();

    foreach ($deadlineGroups as $group) {
        $msg = "URGENT: Ticketing deadline for PNR {$group['pnr']} ({$group['tour_code']}) is on {$group['ticketing_deadline']}.";
        
        // Check if notification already exists to avoid spam
        $check = $pdo->prepare("SELECT id FROM notifications WHERE entity_id = ? AND entity_type = 'movement' AND message LIKE ? AND is_read = 0");
        $check->execute([$group['id'], 'URGENT: Ticketing deadline%']);
        
        if (!$check->fetch()) {
            Notification::create([
                'entity_type' => 'movement',
                'entity_id' => $group['id'],
                'message' => $msg,
                'alert_type' => 'DEADLINE'
            ]);
            echo "Created notification for PNR {$group['pnr']}\n";
        }
    }

    // 2. Check Deposit Airline Dates (Alert 2 days before)
    // DP1
    $sqlDP1 = "SELECT id, pnr, tour_code, deposit1_airlines_date FROM movements 
               WHERE deposit1_airlines_date IS NOT NULL 
               AND dp1_status != 'PAID'
               AND deposit1_airlines_date <= DATE_ADD(CURDATE(), INTERVAL 2 DAY)";
    $stmtDP1 = $pdo->query($sqlDP1);
    foreach ($stmtDP1->fetchAll() as $group) {
        $msg = "PAYMENT: DP1 Airline for PNR {$group['pnr']} is due on {$group['deposit1_airlines_date']}.";
        Notification::create(['entity_type' => 'movement', 'entity_id' => $group['id'], 'message' => $msg, 'alert_type' => 'PAYMENT']);
    }

    echo "Reminder Service finished.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
