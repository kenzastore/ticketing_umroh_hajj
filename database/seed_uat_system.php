<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/BookingRequest.php';
require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/Invoice.php';
require_once __DIR__ . '/../app/models/Payment.php';
require_once __DIR__ . '/../app/models/PaymentReport.php';
require_once __DIR__ . '/../app/models/PaymentAdvise.php';
require_once __DIR__ . '/../app/models/AuditLog.php';

echo "--- UAT DATA GENERATION SYSTEM ---
";
echo "Starting generation of 100 synchronized UAT records...
";

try {
    $pdo->beginTransaction();

    // 1. DATA CLEANUP (Wipe and Replace)
    echo "Purging existing operational data...
";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE payments");
    $pdo->exec("TRUNCATE TABLE payment_advises");
    $pdo->exec("TRUNCATE TABLE invoice_fare_lines");
    $pdo->exec("TRUNCATE TABLE invoice_flight_lines");
    $pdo->exec("TRUNCATE TABLE invoices");
    $pdo->exec("TRUNCATE TABLE flight_status_events");
    $pdo->exec("TRUNCATE TABLE flight_status_snapshots");
    $pdo->exec("TRUNCATE TABLE flight_legs");
    $pdo->exec("TRUNCATE TABLE movements");
    $pdo->exec("TRUNCATE TABLE booking_request_legs");
    $pdo->exec("TRUNCATE TABLE booking_requests");
    $pdo->exec("TRUNCATE TABLE audit_logs");
    $pdo->exec("TRUNCATE TABLE agents");
    $pdo->exec("TRUNCATE TABLE corporates");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // 2. MASTER DATA SETUP
    echo "Seeding Roles and Admin User...\n";
    $pdo->exec("INSERT IGNORE INTO roles (id, name) VALUES (1, 'admin')");
    $pdo->prepare("INSERT IGNORE INTO users (id, username, password, role_id, full_name) VALUES (?, ?, ?, ?, ?)")
        ->execute([1, 'admin', password_hash('admin123', PASSWORD_DEFAULT), 1, 'System Administrator']);

    echo "Seeding Master Data (Agents & Corporates)...\n";
    $agents = [];
    for ($i = 1; $i <= 10; $i++) {
        $name = "UAT Agent " . str_pad($i, 2, '0', STR_PAD_LEFT);
        $pdo->prepare("INSERT INTO agents (name, skyagent_id) VALUES (?, ?)")->execute([$name, "SKY-UAT-".$i]);
        $agents[] = ['id' => $pdo->lastInsertId(), 'name' => $name];
    }

    $corporates = [];
    for ($i = 1; $i <= 5; $i++) {
        $name = "UAT Corporate " . chr(64 + $i);
        $pdo->prepare("INSERT INTO corporates (name) VALUES (?)")->execute([$name]);
        $corporates[] = ['id' => $pdo->lastInsertId(), 'name' => $name];
    }

    // Admin User for logs
    $stmt = $pdo->query("SELECT id FROM users WHERE role_id = 1 LIMIT 1");
    $adminId = $stmt->fetchColumn() ?: 1;

    // 3. GENERATE 100 WORKFLOW RECORDS
    echo "Generating 100 synchronized records...
";
    for ($i = 1; $i <= 100; $i++) {
        $agent = $agents[array_rand($agents)];
        $corp = $corporates[array_rand($corporates)];
        $pax = rand(15, 50);
        $requestDate = date('Y-m-d H:i:s', strtotime("-" . rand(30, 60) . " days"));
        
        // --- STAGE 1: Booking Request ---
        $requestId = BookingRequest::create([
            'request_no' => 1000 + $i,
            'corporate_id' => $corp['id'],
            'corporate_name' => $corp['name'],
            'agent_id' => $agent['id'],
            'agent_name' => $agent['name'],
            'group_size' => $pax,
            'duration_days' => 12,
            'selling_fare' => 15000000,
            'nett_fare' => 14000000,
            'created_at' => $requestDate
        ], [
            ['leg_no' => 1, 'flight_no' => 'SQ' . rand(100, 999), 'sector' => 'SUB-SIN', 'flight_date' => date('Y-m-d', strtotime("+3 months"))],
            ['leg_no' => 2, 'flight_no' => 'SQ' . rand(100, 999), 'sector' => 'SIN-JED', 'flight_date' => date('Y-m-d', strtotime("+3 months"))]
        ], $adminId);

        // Update audit log timestamp manually to match request date
        $pdo->prepare("UPDATE audit_logs SET created_at = ? WHERE entity_type = 'booking_request' AND entity_id = ?")->execute([$requestDate, $requestId]);

        // DETERMINE STAGE (25/25/25/25 split approx)
        if ($i <= 25) {
            // Stage 1 ONLY (New Request)
            continue; 
        }

        // --- STAGE 2: Active Movement ---
        $pnr = "PNR" . strtoupper(substr(md5($i), 0, 3)) . $i;
        $conversionDate = date('Y-m-d H:i:s', strtotime($requestDate . " +5 days"));
        
        $movementData = [
            'booking_request_id' => $requestId,
            'movement_no' => 1000 + $i,
            'agent_id' => $agent['id'],
            'agent_name' => $agent['name'],
            'pnr' => $pnr,
            'tour_code' => "TC-" . $pnr,
            'passenger_count' => $pax,
            'selling_fare' => 15000000,
            'nett_selling' => 14000000,
            'total_selling' => $pax * 15000000,
            'created_at' => $conversionDate,
            'created_date' => date('Y-m-d', strtotime($conversionDate))
        ];

        // Determine Movement Status
        if ($i > 75) {
            // OVERDUE/URGENT SCENARIO
            $movementData['ticketing_deadline'] = date('Y-m-d', strtotime("+1 day"));
            $movementData['deposit1_eemw_date'] = date('Y-m-d', strtotime("-1 day"));
            $movementData['dp1_status'] = 'UNPAID';
        } else if ($i > 50) {
            // TICKETING DONE / FINALIZED
            $movementData['ticketing_done'] = 1;
            $movementData['dp1_status'] = 'PAID';
            $movementData['dp2_status'] = 'PAID';
            $movementData['fp_status'] = 'PAID';
        } else {
            // ACTIVE PARTIAL
            $movementData['dp1_status'] = 'PAID';
            $movementData['dp2_status'] = 'PENDING';
        }

        $movementId = Movement::create($movementData, [], $adminId);
        $pdo->prepare("UPDATE audit_logs SET created_at = ? WHERE entity_type = 'movement' AND entity_id = ?")->execute([$conversionDate, $movementId]);
        $pdo->prepare("UPDATE booking_requests SET is_converted = 1 WHERE id = ?")->execute([$requestId]);

        // --- STAGE 3: Financials (Invoices/Payments) ---
        if ($i > 50) {
            $invoiceDate = date('Y-m-d', strtotime($conversionDate . " +2 days"));
            $invoiceId = Invoice::create([
                'invoice_no' => "INV-".$i."- " . date('Y'),
                'invoice_date' => $invoiceDate,
                'corporate_id' => $corp['id'],
                'corporate_name' => $corp['name'],
                'pnr' => $pnr,
                'tour_code' => "TC-" . $pnr,
                'total_pax' => $pax,
                'fare_per_pax' => 15000000,
                'amount_idr' => $pax * 15000000
            ], [], [], $adminId);

            // Record full payment for finalized group
            if ($i <= 75) {
                Payment::create([
                    'invoice_id' => $invoiceId,
                    'amount_paid' => $pax * 15000000,
                    'payment_date' => date('Y-m-d', strtotime($invoiceDate . " +1 day")),
                    'payment_method' => 'BANK_TRANSFER',
                    'payment_stage' => 'Full Payment'
                ]);
                
                // Seed Payment Advise
                PaymentAdvise::create([
                    'movement_id' => $movementId,
                    'pnr' => $pnr,
                    'total_amount' => $pax * 14000000,
                    'transfer_amount' => $pax * 14000000,
                    'status' => 'TRANSFERRED'
                ], $adminId);
            }
        }
    }

    $pdo->commit();
    echo "Successfully generated 100 UAT records.
";

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Generation failed: " . $e->getMessage() . "
");
}
