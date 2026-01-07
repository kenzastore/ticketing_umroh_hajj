<?php
/**
 * MASTER SYSTEM SEEDER
 * Purpose: Populate the entire database with a consistent dataset to test 
 * all modules from start to finish.
 */

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/Agent.php';
require_once __DIR__ . '/../app/models/Corporate.php';
require_once __DIR__ . '/../app/models/BookingRequest.php';
require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/Invoice.php';
require_once __DIR__ . '/../app/models/Payment.php';
require_once __DIR__ . '/../app/models/PaymentAdvise.php';

// 0. Cleanup
echo "Cleaning up tables...\n";
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$tables = [
    'audit_logs', 'payments', 'invoice_fare_lines', 'invoice_flight_lines', 
    'invoices', 'flight_status_events', 'flight_status_snapshots', 
    'flight_legs', 'movements', 'booking_request_legs', 'booking_requests', 
    'agents', 'corporates', 'payment_report_lines', 'payment_advises'
];
foreach ($tables as $table) {
    $pdo->exec("TRUNCATE TABLE $table");
}
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

// Mock User ID
$userId = 1; // Assuming 'admin' user exists with ID 1

// 1. Agents & Corporates
echo "Seeding Agents & Corporates...\n";
$agentId = Agent::create(['name' => 'PT JALAN JALAN HOLIDAYS', 'skyagent_id' => 'AID2546210'], $userId);
$corpId = Corporate::create(['name' => 'CORPORATE ALPHA', 'address' => 'Jakarta'], $userId);

// 2. Booking Requests
echo "Seeding Booking Requests...\n";
$reqId = BookingRequest::create([
    'request_no' => 1001,
    'corporate_id' => $corpId,
    'corporate_name' => 'CORPORATE ALPHA',
    'agent_id' => $agentId,
    'agent_name' => 'PT JALAN JALAN HOLIDAYS',
    'group_size' => 35,
    'tcp' => 10000000,
    'selling_fare' => 10000000,
    'duration_days' => 12,
    'ttl_days' => 13
], [
    ['leg_no' => 1, 'flight_date' => '2026-02-04', 'flight_no' => 'TR265', 'sector' => 'SUB-SIN'],
    ['leg_no' => 2, 'flight_date' => '2026-02-04', 'flight_no' => 'TR596', 'sector' => 'SIN-JED']
], $userId);

// 3. Movements (Umrah & Hajji)
echo "Seeding Movements...\n";
// Case 1: Umrah Movement with Urgent Deadline
$mv1Id = Movement::create([
    'category' => 'UMRAH',
    'agent_name' => 'PT JALAN JALAN HOLIDAYS',
    'pnr' => 'TRV12JT',
    'tour_code' => '5FEB-40-13D-TRID',
    'carrier' => 'TRID',
    'flight_no_out' => 'TR265',
    'sector_out' => 'SUB-SIN',
    'dep_seg1_date' => '2026-02-04',
    'passenger_count' => 35,
    'approved_fare' => 9000000,
    'selling_fare' => 10000000,
    'total_selling' => 350000000,
    'ticketing_done' => 0, // Set to 0 so it shows in "Time Limit"
    'ticketing_deadline' => date('Y-m-d', strtotime('+1 days')) // Due tomorrow
], $userId);

// Case 2: Pending Umrah Movement
$mv2Id = Movement::create([
    'category' => 'UMRAH',
    'agent_name' => 'ELANG EMAS WISATA',
    'pnr' => 'ACDFRG',
    'tour_code' => '02AUG-15-12D-BICGK',
    'passenger_count' => 15,
    'ticketing_done' => 0,
    'ticketing_deadline' => date('Y-m-d', strtotime('+3 days')) // Within the 3-day window
], $userId);

// Case 3: Hajji Movement
$mv3Id = Movement::create([
    'category' => 'HAJJI',
    'agent_name' => 'HAJJI TRAVEL ABC',
    'pnr' => 'HAJ001',
    'tour_code' => '09MAY-45S-25D-TRID',
    'passenger_count' => 45,
    'ticketing_done' => 0
], $userId);

// 4. Invoices
echo "Seeding Invoices...\n";
$invId = Invoice::create([
    'invoice_no' => 'INV/2026/001',
    'invoice_date' => date('Y-m-d'),
    'corporate_name' => 'CORPORATE ALPHA',
    'pnr' => 'TRV12JT',
    'tour_code' => '5FEB-40-13D-TRID',
    'total_pax' => 35,
    'amount_idr' => 350000000
], [
    ['flight_date' => '2026-02-04', 'flight_no' => 'TR265', 'sector' => 'SUB-SIN']
], [
    ['description' => 'Umrah Group Ticket', 'total_pax' => 35, 'fare_amount' => 10000000, 'amount_idr' => 350000000]
], $userId);

// 5. Payments (Transactions)
echo "Seeding Payments...\n";
Payment::create([
    'invoice_id' => $invId,
    'amount_paid' => 70000000,
    'payment_date' => date('Y-m-d'),
    'payment_method' => 'Transfer',
    'payment_stage' => 'DP1',
    'notes' => 'Initial Deposit'
]);

// 6. Payment Report Lines (Accounting)
echo "Seeding Report Lines...\n";
$pdo->exec("INSERT INTO payment_report_lines (reference_id, remarks, fare_per_pax, debit_amount, payment_date, bank_from, bank_from_name, bank_to, bank_to_name) 
            VALUES ('TRV12JT', 'SELLING FARES', 10000000, 350000000, '2025-12-30', 'MANDIRI', 'SUTRA HIDAYAH', 'MANDIRI', 'MANDIRI PAHLAWAN')");
$pdo->exec("INSERT INTO payment_report_lines (reference_id, remarks, fare_per_pax, debit_amount, payment_date) 
            VALUES ('TRV12JT', 'DEPOSIT.1', 2000000, -70000000, '2025-12-30')");

// 7. Payment Advises
echo "Seeding Payment Advises...\n";
PaymentAdvise::create([
    'movement_id' => $mv1Id,
    'agent_name' => 'PT JALAN JALAN HOLIDAYS',
    'pnr' => 'TRV12JT',
    'tour_code' => '5FEB-40-13D-TRID',
    'date_created' => date('Y-m-d'),
    'total_seats_confirmed' => 35,
    'approved_fare' => 9000000,
    'total_amount' => 315000000,
    'top_up_amount' => 217200000,
    'company_name' => 'SCOOT PTE. LTD.',
    'remitter_name' => 'ABDUL HARIS NIRA'
], $userId);

echo "\n==========================================\n";
echo "MASTER SEED COMPLETE!\n";
echo "You can now test all modules with PNR 'TRV12JT'.\n";
echo "==========================================\n";
