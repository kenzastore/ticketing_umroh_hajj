<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/Invoice.php';

// Clear
$pdo->exec("DELETE FROM movements");
$pdo->exec("DELETE FROM invoices");
$pdo->exec("DELETE FROM payment_report_lines");

// 1. Create Movement ACDFRG (Umrah)
$mvId = Movement::create([
    'category' => 'UMRAH',
    'agent_name' => 'JALAN JALAN HOLIDAY',
    'created_date' => '2025-07-18',
    'pnr' => 'ACDFRG',
    'dp1_status' => 'PAID',
    'fp_status' => 'PAID',
    'tour_code' => '02AUG-15-12D-BICGK',
    'carrier' => 'BICGK',
    'passenger_count' => 15,
    'approved_fare' => 9000000,
    'selling_fare' => 9100000,
    'total_selling' => 136500000,
    'ticketing_done' => 1,
    'ticketing_deadline' => '2025-07-21'
]);

// 2. Create Invoice for ACDFRG
Invoice::create([
    'invoice_no' => 'INV/2026/ACDFRG',
    'invoice_date' => date('Y-m-d'),
    'corporate_name' => 'JALAN JALAN HOLIDAY',
    'pnr' => 'ACDFRG',
    'tour_code' => '02AUG-15-12D-BICGK',
    'total_pax' => 15,
    'amount_idr' => 136500000
], [], []); // Empty flights and fares for mock

// 3. Create Report Lines for ACDFRG
$pdo->exec("INSERT INTO payment_report_lines (reference_id, remarks, fare_per_pax, debit_amount, payment_date) 
            VALUES ('ACDFRG', 'SELLING FARES', 9100000, 136500000, '2025-07-21')");

echo "Seeded movement, invoice, and report data for ACDFRG.\n";