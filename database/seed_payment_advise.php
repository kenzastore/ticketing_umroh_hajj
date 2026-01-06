<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/PaymentAdvise.php';

// 1. Ensure a movement exists
$mvId = Movement::create([
    'pnr' => 'TRV12JT',
    'agent_name' => 'PT JALAN JALAN',
    'tour_code' => '5FEB-40-13D-TRID',
    'passenger_count' => 35,
    'selling_fare' => 10000000,
    'dep_seg1_date' => '2026-02-04'
]);

// 2. Create Payment Advise
PaymentAdvise::create([
    'movement_id' => $mvId,
    'agent_name' => 'PT JALAN JALAN',
    'tour_code' => '5FEB-40-13D-TRID',
    'pnr' => 'TRV12JT',
    'date_created' => '2025-12-30',
    'grp_depart_date' => '2026-02-04',
    'total_seats_confirmed' => 35,
    'total_seats_used_percent' => 80,
    'approved_fare' => 10000000,
    'total_amount' => 320000000,
    'deposit_amount' => 102800000,
    'balance_payment_amount' => 217200000,
    'top_up_amount' => 217200000,
    'transfer_amount' => 217200000,
    'reference_number' => 'A125018',
    'company_name' => 'SCOOT PTE. LTD.',
    'company_account_no' => '306-1009-1430',
    'company_bank_name' => 'STANDARD CHARTERED BANK',
    'company_address' => 'JAKARTA',
    'remitter_name' => 'ABDUL HARIS NIRA',
    'remitter_account_no' => '141 0002525020',
    'remitter_bank_name' => 'MANDIRI CAPEM SIDOARJO PAHLAWAN'
]);

echo "Payment Advise seeded successfully.\n";

