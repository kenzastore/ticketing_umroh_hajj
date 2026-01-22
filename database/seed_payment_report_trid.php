<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/PaymentReport.php';

echo "Seeding TRID-HAJJ Dummy Data...\n";

try {
    $pdo->beginTransaction();

    // 1. Create/Update Movement
    $pnr = 'VFUQ8X';
    $tourCode = '11MAY-45S-26D-TRID';
    
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM movements WHERE pnr = ?");
    $stmt->execute([$pnr]);
    $movementId = $stmt->fetchColumn();

    $movementData = [
        'category' => 'HAJJI',
        'movement_no' => 1,
        'agent_name' => 'EBAD WISATA',
        'pnr' => $pnr,
        'tour_code' => $tourCode,
        'carrier' => 'SCOOT',
        'passenger_count' => 45,
        'pattern_code' => '26 26DAYS',
        'incentive_amount' => 4500000,
        'discount_amount' => 0,
        'deposit1_eemw_date' => '2026-01-03',
        'deposit2_eemw_date' => '2026-01-23',
        'fullpay_eemw_date' => '2025-04-06', // As per PDF Sunday, 06 April 2025
        'created_at' => '2025-12-01 00:00:00', // Date of Request
        'created_date' => '2026-12-31' // Date of Confirmed (weird year in PDF but ok)
    ];

    if ($movementId) {
        $cols = [];
        foreach($movementData as $k => $v) $cols[] = "$k = ?";
        $stmt = $pdo->prepare("UPDATE movements SET " . implode(', ', $cols) . " WHERE id = ?");
        $stmt->execute(array_merge(array_values($movementData), [$movementId]));
    } else {
        $cols = implode(', ', array_keys($movementData));
        $placeholders = implode(', ', array_fill(0, count($movementData), '?'));
        $stmt = $pdo->prepare("INSERT INTO movements ($cols) VALUES ($placeholders)");
        $stmt->execute(array_values($movementData));
        $movementId = $pdo->lastInsertId();
    }

    // 2. Flight Legs
    $pdo->prepare("DELETE FROM flight_legs WHERE movement_id = ?")->execute([$movementId]);
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

    // 3. Payment Report Lines
    $pdo->prepare("DELETE FROM payment_report_lines WHERE reference_id = ?")->execute([$pnr]);
    
    // SALES LINES (Table 1)
    $salesLines = [
        [
            'table_type' => 'SALES', 'remarks' => 'SELLING FARE - TRTR', 'total_pax' => 45, 'fare_per_pax' => 10000000, 'debit_amount' => 450000000,
            'payment_date' => null, 'time_limit_date' => null
        ],
        [
            'table_type' => 'SALES', 'remarks' => 'DEPOSIT-T 1 ( 20%) TRID', 'total_pax' => 45, 'fare_per_pax' => -2000000, 'debit_amount' => -90000000,
            'payment_date' => '2026-01-09', 'time_limit_date' => '2026-01-03', 'bank_from' => 'ANGIN RIBUT', 'bank_to' => 'PT ELANG', 'bank_to_name' => 'BANK MANDIRI', 'bank_to_number' => '9328274774i44'
        ],
        [
            'table_type' => 'SALES', 'remarks' => 'DEPOSIT-T 2 ( 30%) TRID', 'total_pax' => 45, 'fare_per_pax' => -3000000, 'debit_amount' => -135000000,
            'payment_date' => null, 'time_limit_date' => '2026-01-23'
        ],
        [
            'table_type' => 'SALES', 'remarks' => 'DEPOSIT-T 3 ( 50%) TRID', 'total_pax' => 45, 'fare_per_pax' => -5000000, 'debit_amount' => -225000000,
            'payment_date' => null, 'time_limit_date' => '2025-04-06'
        ],
    ];

    // COST LINES (Table 2)
    $costLines = [
        [
            'table_type' => 'COST', 'remarks' => 'NETT FARES', 'total_pax' => 45, 'fare_per_pax' => 9900000, 'debit_amount' => 445500000,
            'payment_date' => null, 'time_limit_date' => null
        ],
        [
            'table_type' => 'COST', 'remarks' => 'DEPOSIT - 1 - TO TRID', 'total_pax' => 45, 'fare_per_pax' => -1980000, 'debit_amount' => -89100000,
            'payment_date' => '2026-01-12', 'time_limit_date' => '2026-01-06', 'bank_from' => 'ABDUL HARIS NIRA', 'bank_from_name' => 'BANK MANDIRI PAHLAWAN', 'bank_from_number' => '94857698', 'bank_to' => 'SCOOT', 'bank_to_name' => 'STANDARD CHARETERD BANK', 'bank_to_number' => '9328274774i44'
        ],
        [
            'table_type' => 'COST', 'remarks' => 'DEPOSIT - 2 - TO TRID', 'total_pax' => 45, 'fare_per_pax' => -2970000, 'debit_amount' => -133650000,
            'payment_date' => null, 'time_limit_date' => '2026-01-29'
        ],
        [
            'table_type' => 'COST', 'remarks' => 'DEPOSIT - 3 - TO TRID', 'total_pax' => 45, 'fare_per_pax' => -4950000, 'debit_amount' => -222750000,
            'payment_date' => null, 'time_limit_date' => '2025-04-11'
        ],
    ];

    $stmtLine = $pdo->prepare("INSERT INTO payment_report_lines (reference_id, table_type, remarks, total_pax, fare_per_pax, debit_amount, payment_date, time_limit_date, bank_from, bank_from_name, bank_from_number, bank_to, bank_to_name, bank_to_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach(array_merge($salesLines, $costLines) as $l) {
        $stmtLine->execute([
            $pnr, $l['table_type'], $l['remarks'], $l['total_pax'], $l['fare_per_pax'], $l['debit_amount'], 
            $l['payment_date'], $l['time_limit_date'], 
            $l['bank_from'] ?? null, $l['bank_from_name'] ?? null, $l['bank_from_number'] ?? null,
            $l['bank_to'] ?? null, $l['bank_to_name'] ?? null, $l['bank_to_number'] ?? null
        ]);
    }

    $pdo->commit();
    echo "Seed successful! PNR VFUQ8X is ready for testing.\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Seed failed: " . $e->getMessage() . "\n");
}
