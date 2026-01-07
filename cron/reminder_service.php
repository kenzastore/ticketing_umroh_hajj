<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/Notification.php';

echo "Running Reminder Service...\n";

if (isset($pdo)) {
    Notification::init($pdo);
}

try {
    // Helper to process deadlines
    function checkDeadline($pdo, $sql, $msgTemplate, $alertType, $checkPrefix) {
        $stmt = $pdo->query($sql);
        $groups = $stmt->fetchAll();
        
        foreach ($groups as $group) {
            $date = $group['deadline_date'];
            $pnr = $group['pnr'] ?? 'N/A';
            $tourCode = $group['tour_code'] ?? 'N/A';
            
            $msg = sprintf($msgTemplate, $pnr, $tourCode, $date);
            
            // Use model method to check for duplicates
            if (!Notification::existsUnread('movement', $group['id'], $checkPrefix)) {
                Notification::create([
                    'entity_type' => 'movement',
                    'entity_id' => $group['id'],
                    'message' => $msg,
                    'alert_type' => $alertType
                ]);
                echo "Created notification: $msg\n";
            }
        }
    }

    // 1. Ticketing Deadlines (H-3)
    $sqlTicketing = "SELECT id, pnr, tour_code, ticketing_deadline as deadline_date 
            FROM movements 
            WHERE ticketing_deadline IS NOT NULL 
            AND ticketing_done = 0
            AND ticketing_deadline <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
    
    checkDeadline($pdo, $sqlTicketing, 
        "URGENT: Ticketing deadline for PNR %s (%s) is on %s.", 
        'DEADLINE', 
        'URGENT: Ticketing deadline'
    );

    // 2. Deposit 1 (H-3)
    // Airline
    $sqlDP1Air = "SELECT id, pnr, tour_code, deposit1_airlines_date as deadline_date 
               FROM movements 
               WHERE deposit1_airlines_date IS NOT NULL 
               AND dp1_status != 'PAID'
               AND deposit1_airlines_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
    checkDeadline($pdo, $sqlDP1Air, 
        "PAYMENT: DP1 Airline for PNR %s is due on %s.", 
        'PAYMENT',
        'PAYMENT: DP1 Airline'
    );

    // EEMW
    $sqlDP1Eemw = "SELECT id, pnr, tour_code, deposit1_eemw_date as deadline_date 
               FROM movements 
               WHERE deposit1_eemw_date IS NOT NULL 
               AND dp1_status != 'PAID'
               AND deposit1_eemw_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
    checkDeadline($pdo, $sqlDP1Eemw, 
        "PAYMENT: DP1 EEMW for PNR %s is due on %s.", 
        'PAYMENT',
        'PAYMENT: DP1 EEMW'
    );

    // 3. Deposit 2 (H-3)
    // Airline
    $sqlDP2Air = "SELECT id, pnr, tour_code, deposit2_airlines_date as deadline_date 
               FROM movements 
               WHERE deposit2_airlines_date IS NOT NULL 
               AND dp2_status != 'PAID'
               AND deposit2_airlines_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
    checkDeadline($pdo, $sqlDP2Air, 
        "PAYMENT: DP2 Airline for PNR %s is due on %s.", 
        'PAYMENT',
        'PAYMENT: DP2 Airline'
    );

    // EEMW
    $sqlDP2Eemw = "SELECT id, pnr, tour_code, deposit2_eemw_date as deadline_date 
               FROM movements 
               WHERE deposit2_eemw_date IS NOT NULL 
               AND dp2_status != 'PAID'
               AND deposit2_eemw_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
    checkDeadline($pdo, $sqlDP2Eemw, 
        "PAYMENT: DP2 EEMW for PNR %s is due on %s.", 
        'PAYMENT',
        'PAYMENT: DP2 EEMW'
    );

    // 4. Full Payment (H-3)
    // Airline
    $sqlFPAir = "SELECT id, pnr, tour_code, fullpay_airlines_date as deadline_date 
               FROM movements 
               WHERE fullpay_airlines_date IS NOT NULL 
               AND fp_status != 'PAID'
               AND fullpay_airlines_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
    checkDeadline($pdo, $sqlFPAir, 
        "PAYMENT: Full Payment Airline for PNR %s is due on %s.", 
        'PAYMENT',
        'PAYMENT: Full Payment Airline'
    );

    // EEMW
    $sqlFPEemw = "SELECT id, pnr, tour_code, fullpay_eemw_date as deadline_date 
               FROM movements 
               WHERE fullpay_eemw_date IS NOT NULL 
               AND fp_status != 'PAID'
               AND fullpay_eemw_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
    checkDeadline($pdo, $sqlFPEemw, 
        "PAYMENT: Full Payment EEMW for PNR %s is due on %s.", 
        'PAYMENT',
        'PAYMENT: Full Payment EEMW'
    );

    echo "Reminder Service finished.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}