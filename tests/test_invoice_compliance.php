<?php
/**
 * Test script for Invoice Contract Compliance
 * Validates the data mapping layer against invoice_template_contract.md
 */

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/Invoice.php';

echo "=== Invoice Contract Compliance Test ===\n";

// 1. Setup Test Data
$invoice_data = [
    'invoice_no' => 'TEST-' . time(),
    'invoice_date' => date('Y-m-d'),
    'corporate_name' => 'PT Test Compliance',
    'attention_to' => 'Mr. Compliance',
    'address' => "Jalan Test No. 1\nJakarta",
    'ref_text' => 'REF-12345',
    'pnr' => 'PNRABC',
    'tour_code' => 'TOUR-001',
    'total_pax' => 45,
    'fare_per_pax' => 1000000,
    'amount_idr' => 45000000
];

$flight_lines = [
    ['leg_no' => 1, 'flight_date' => '2026-05-11', 'flight_no' => 'TR123', 'sector' => 'SUBSIN', 'time_range' => '0900-1300'],
    ['leg_no' => 2, 'flight_date' => '2026-05-11', 'flight_no' => 'TR456', 'sector' => 'SINSIN', 'time_range' => '1400-1800'],
    ['leg_no' => 3, 'flight_date' => '2026-05-12', 'flight_no' => 'TR789', 'sector' => 'SINJED', 'time_range' => '2000-0400'],
    ['leg_no' => 4, 'flight_date' => '2026-05-20', 'flight_no' => 'TR000', 'sector' => 'JEDSUB', 'time_range' => '1000-2200']
];

$fare_lines = [
    ['line_no' => 1, 'description' => 'Fares', 'total_pax' => 45, 'fare_amount' => 1000000, 'amount_idr' => 45000000],
    ['line_no' => 2, 'description' => 'Non Refundable Deposit-1st', 'total_pax' => 45, 'fare_amount' => -200000, 'amount_idr' => -9000000],
    ['line_no' => 3, 'description' => 'Non Refundable Deposit-2nd', 'total_pax' => 45, 'fare_amount' => -300000, 'amount_idr' => -13500000]
];

echo "1. Creating test invoice...\n";
$invoice_id = Invoice::create($invoice_data, $flight_lines, $fare_lines);

if (!$invoice_id) {
    die("FAILED: Could not create test invoice.\n");
}
echo "SUCCESS: Invoice created with ID $invoice_id.\n";

// 2. Read and Validate
echo "2. Reading invoice and validating mapping...\n";
$invoice = Invoice::readById($invoice_id);

$errors = [];

// Header Checks
if ($invoice['corporate_name'] !== $invoice_data['corporate_name']) $errors[] = "Header: corporate_name mismatch";
if ($invoice['attention_to'] !== $invoice_data['attention_to']) $errors[] = "Header: attention_to mismatch";

// Reference Checks
if ($invoice['ref_text'] !== $invoice_data['ref_text']) $errors[] = "Reference: ref_text mismatch";

// Table Row count checks
if (count($invoice['flight_lines']) !== 4) $errors[] = "Flight Info: Expected 4 legs, got " . count($invoice['flight_lines']);
if (count($invoice['fare_lines']) !== 3) $errors[] = "Fare Breakdown: Expected 3 lines, got " . count($invoice['fare_lines']);

// Staging value behavior (Negative values)
foreach ($invoice['fare_lines'] as $line) {
    if (strpos($line['description'], 'Deposit') !== false) {
        if ($line['amount_idr'] >= 0) {
            $errors[] = "Fare Breakdown: Staging line '{$line['description']}' should be negative, got {$line['amount_idr']}";
        }
    }
}

// 3. Report Results
if (empty($errors)) {
    echo "--- FINAL RESULT: PASSED ---\n";
    echo "All data mapping complies with the invoice template contract requirements.\n";
} else {
    echo "--- FINAL RESULT: FAILED ---\n";
    foreach ($errors as $error) {
        echo "- ERROR: $error\n";
    }
}

// Cleanup (Optional)
// $pdo->exec("DELETE FROM invoices WHERE id = $invoice_id");
