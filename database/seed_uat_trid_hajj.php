<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/PaymentReport.php';
require_once __DIR__ . '/../app/models/BookingRequest.php';
require_once __DIR__ . '/../app/models/AuditLog.php';

echo "Seeding End-to-End TRID-HAJJ 2026 Storyline...\n";

try {
    $pdo->beginTransaction();

    // --- Phase 1: Master Data Setup ---
    
    // 1. Ensure Agent exists
    $stmt = $pdo->prepare("SELECT id FROM agents WHERE name = 'EBAD WISATA' LIMIT 1");
    $stmt->execute();
    $agentId = $stmt->fetchColumn();
    if (!$agentId) {
        $pdo->prepare("INSERT INTO agents (name, skyagent_id) VALUES (?, ?)")->execute(['EBAD WISATA', 'SKY-EBAD-001']);
        $agentId = $pdo->lastInsertId();
    }

    // 2. Ensure Corporate exists
    $stmt = $pdo->prepare("SELECT id FROM corporates WHERE name = 'EBAD WISATA' LIMIT 1");
    $stmt->execute();
    $corporateId = $stmt->fetchColumn();
    if (!$corporateId) {
        $pdo->prepare("INSERT INTO corporates (name) VALUES (?)")->execute(['EBAD WISATA']);
        $corporateId = $pdo->lastInsertId();
    }

    // 3. Ensure a System User exists for Audit Logs
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin' LIMIT 1");
    $stmt->execute();
    $userId = $stmt->fetchColumn();
    if (!$userId) {
        $pdo->prepare("INSERT INTO users (username, password, role_id, full_name) VALUES (?, ?, ?, ?)")
            ->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT), 1, 'System Administrator']);
        $userId = $pdo->lastInsertId();
    }

    // --- Phase 2: Stage 1 - Demand Intake (Booking Request) ---
    
    $pnr = 'VFUQ8X';
    $tourCode = '11MAY-45S-26D-TRID';

    // Cleanup old test data
    $pdo->prepare("DELETE FROM movements WHERE pnr = ?")->execute([$pnr]);
    $pdo->prepare("DELETE FROM booking_requests WHERE agent_name = 'EBAD WISATA' AND group_size = 45")->execute();
    $pdo->prepare("DELETE FROM payment_report_lines WHERE reference_id = ?")->execute([$pnr]);
    $pdo->prepare("DELETE FROM audit_logs WHERE entity_id IN (SELECT id FROM movements WHERE pnr = ?)")->execute([$pnr]);

    // Create Booking Request
    $pdo->prepare("INSERT INTO booking_requests (
        request_no, corporate_id, corporate_name, agent_id, agent_name, 
        group_size, duration_days, is_converted, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")->execute([
        101, $corporateId, 'EBAD WISATA', $agentId, 'EBAD WISATA', 45, 26, 1, '2025-12-01 10:00:00'
    ]);
    $requestId = $pdo->lastInsertId();

    // Audit Log: Request Created
    $pdo->prepare("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, created_at) VALUES (?, ?, ?, ?, ?)")
        ->execute([$userId, 'CREATE', 'booking_request', $requestId, '2025-12-01 10:00:05']);

    // --- Phase 3: Stage 2 - Operational Execution (Movement) ---

    // Create Movement
    $movementData = [
        'category' => 'HAJJI',
        'movement_no' => 1,
        'agent_id' => $agentId,
        'agent_name' => 'EBAD WISATA',
        'pnr' => $pnr,
        'tour_code' => $tourCode,
        'carrier' => 'SCOOT',
        'passenger_count' => 45,
        'pattern_code' => '26 26DAYS',
        'incentive_amount' => 4500000,
        'discount_amount' => 0,
        'ticketing_done' => 1,
        'deposit1_eemw_date' => '2026-01-03',
        'deposit2_eemw_date' => '2026-01-23',
        'fullpay_eemw_date' => '2025-04-06',
        'created_at' => '2025-12-31 14:00:00',
        'created_date' => '2025-12-31'
    ];

    $cols = implode(', ', array_keys($movementData));
    $placeholders = implode(', ', array_fill(0, count($movementData), '?'));
    $pdo->prepare("INSERT INTO movements ($cols) VALUES ($placeholders)")->execute(array_values($movementData));
    $movementId = $pdo->lastInsertId();

    // Flight Legs
    $legs = [
        ['leg_no' => 1, 'direction' => 'OUT', 'flight_no' => 'TR297', 'sector' => 'SUB-SIN', 'scheduled_departure' => '2026-05-11', 'time_range' => '1035-14:00'],
        ['leg_no' => 2, 'direction' => 'OUT', 'flight_no' => 'TR596', 'sector' => 'SIN-JED', 'scheduled_departure' => '2026-05-11', 'time_range' => '1605-2035'],
        ['leg_no' => 3, 'direction' => 'IN',  'flight_no' => 'TR597', 'sector' => 'JED-SIN', 'scheduled_departure' => '2026-06-04', 'time_range' => '2150-1235+1'],
        ['leg_no' => 4, 'direction' => 'IN',  'flight_no' => 'TR266', 'sector' => 'SIN-SUB', 'scheduled_departure' => '2026-06-05', 'time_range' => '1715-1835'],
    ];
    $stmtLeg = $pdo->prepare("INSERT INTO flight_legs (movement_id, leg_no, direction, flight_no, sector, scheduled_departure, time_range) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach($legs as $leg) {
        $stmtLeg->execute([$movementId, $leg['leg_no'], $leg['direction'], $leg['flight_no'], $leg['sector'], $leg['scheduled_departure'], $leg['time_range']]);
    }

    // Audit Log: Converted to Movement
    $pdo->prepare("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, created_at) VALUES (?, ?, ?, ?, ?)")
        ->execute([$userId, 'CONVERT_TO_MOVEMENT', 'booking_request', $requestId, '2025-12-31 14:05:00']);

    // --- Phase 4: Stage 3 - Financial Settlement ---

    // Create Invoice (Selling)
    $pdo->prepare("INSERT INTO invoices (invoice_no, invoice_date, corporate_id, corporate_name, pnr, tour_code, total_pax, fare_per_pax, amount_idr, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
        ->execute(['INV-2026-001', '2026-01-05', $corporateId, 'EBAD WISATA', $pnr, $tourCode, 45, 10000000, 450000000, 'PARTIALLY_PAID']);
    $invoiceId = $pdo->lastInsertId();

    // Create Payment for Deposit 1 (Sales)
    $pdo->prepare("INSERT INTO payments (invoice_id, amount_paid, payment_date, payment_method, reference_number) VALUES (?, ?, ?, ?, ?)")
        ->execute([$invoiceId, 90000000, '2026-01-09', 'BANK_TRANSFER', 'REF-SALES-DEP1']);

    // Payment Report Lines (SALES)
    $salesLines = [
        ['SALES', 'SELLING FARE - TRTR', 45, 10000000, 450000000, null, null, null, null, null, null, null, null],
        ['SALES', 'DEPOSIT-T 1 ( 20%) TRID', 45, -2000000, -90000000, '2026-01-09', '2026-01-03', 'ANGIN RIBUT', null, null, 'PT ELANG', 'BANK MANDIRI', '9328274774i44'],
        ['SALES', 'DEPOSIT-T 2 ( 30%) TRID', 45, -3000000, -135000000, null, '2026-01-23', null, null, null, null, null, null],
        ['SALES', 'DEPOSIT-T 3 ( 50%) TRID', 45, -5000000, -225000000, null, '2025-04-06', null, null, null, null, null, null],
    ];

    // Payment Report Lines (COST)
    $costLines = [
        ['COST', 'NETT FARES', 45, 9900000, 445500000, null, null, null, null, null, null, null, null],
        ['COST', 'DEPOSIT - 1 - TO TRID', 45, -1980000, -89100000, '2026-01-12', '2026-01-06', 'ABDUL HARIS NIRA', 'BANK MANDIRI PAHLAWAN', '94857698', 'SCOOT', 'STANDARD CHARETERD BANK', '9328274774i44'],
        ['COST', 'DEPOSIT - 2 - TO TRID', 45, -2970000, -133650000, null, '2026-01-29', null, null, null, null, null, null],
        ['COST', 'DEPOSIT - 3 - TO TRID', 45, -4950000, -222750000, null, '2025-04-11', null, null, null, null, null, null],
    ];

    $stmtLine = $pdo->prepare("INSERT INTO payment_report_lines (reference_id, table_type, remarks, total_pax, fare_per_pax, debit_amount, payment_date, time_limit_date, bank_from, bank_from_name, bank_from_number, bank_to, bank_to_name, bank_to_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach(array_merge($salesLines, $costLines) as $l) {
        $stmtLine->execute([$pnr, $l[0], $l[1], $l[2], $l[3], $l[4], $l[5], $l[6], $l[7], $l[8], $l[9], $l[10], $l[11], $l[12]]);
    }

    // Audit Log: Financials Done
    $pdo->prepare("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, created_at) VALUES (?, ?, ?, ?, ?)")
        ->execute([$userId, 'RECORD_PAYMENT', 'movement', $movementId, '2026-01-12 10:00:00']);

    $pdo->commit();
    echo "Seed successful! End-to-end TRID-HAJJ story is live.\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Seeding failed: " . $e->getMessage() . "\n");
}
