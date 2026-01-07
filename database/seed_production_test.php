<?php
/**
 * PRODUCTION-READY TEST SEEDER (Indonesian Dataset)
 * Generates 15 records across the full lifecycle.
 */

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/Agent.php';
require_once __DIR__ . '/../app/models/Corporate.php';
require_once __DIR__ . '/../app/models/BookingRequest.php';
require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/Invoice.php';
require_once __DIR__ . '/../app/models/Payment.php';

echo "Starting Production Test Seeding (30 Indonesian Records)...\n";

// Cleanup existing (optional, but good for clean start)
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$tables = ['audit_logs', 'payments', 'invoice_fare_lines', 'invoice_flight_lines', 'invoices', 'flight_legs', 'movements', 'booking_request_legs', 'booking_requests', 'agents', 'corporates', 'notifications'];
foreach ($tables as $table) { $pdo->exec("TRUNCATE TABLE $table"); }
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

$userId = 1;

// 1. Indonesian Entities
$agentNames = [
    "SAHABAT JANNAH TRAVEL", "AMANAH UMROH & HAJI", "ELANG EMAS WISATA", 
    "CAHAYA HARAMAIN", "AL-MUBAROK WISATA", "MADANI TOUR"
];
$corpNames = [
    "KOPERASI PEGAWAI TELKOM", "MAJELIS TAKLIM AL-IKHLAS", "PT SUMBER MAKMUR SEJAHTERA", 
    "PAGUYUBAN HAJI JAWA TIMUR", "BANK SYARIAH INDONESIA", "PT DIRGANTARA INDONESIA"
];

$agents = [];
foreach($agentNames as $name) {
    $agents[] = Agent::create(['name' => $name, 'skyagent_id' => 'SA-' . rand(100, 999)], $userId);
}
$corps = [];
foreach($corpNames as $name) {
    $corps[] = Corporate::create(['name' => $name, 'address' => 'Jakarta/Surabaya'], $userId);
}

// 2. Generate 30 Records
for ($i = 1; $i <= 30; $i++) {
    $isHajji = ($i > 20); // 10 Hajji, 20 Umrah
    $cat = $isHajji ? 'HAJJI' : 'UMRAH';
    $pax = rand(15, 45);
    $agentId = $agents[array_rand($agents)];
    $corpId = $corps[array_rand($corps)];
    
    $agentName = $pdo->query("SELECT name FROM agents WHERE id = $agentId")->fetchColumn();
    $corpName = $pdo->query("SELECT name FROM corporates WHERE id = $corpId")->fetchColumn();

    // Random Stage
    $stage = rand(1, 4); // 1: Request Only, 2: Converted/Movement, 3: Invoiced, 4: Paid

    // A. Create Booking Request (Foundation)
    $fare = rand(12000000, 18000000);
    $reqId = BookingRequest::create([
        'request_no' => 2000 + $i,
        'corporate_id' => $corpId,
        'corporate_name' => $corpName,
        'agent_id' => $agentId,
        'agent_name' => $agentName,
        'group_size' => $pax,
        'selling_fare' => $fare,
        'nett_fare' => $fare - 1000000,
        'duration_days' => 12,
        'notes' => "Testing Indonesian Dataset - Group $i"
    ], [
        ['leg_no' => 1, 'flight_date' => date('Y-m-d', strtotime("+$i weeks")), 'flight_no' => 'SQ' . rand(100, 999), 'sector' => 'CGK-SIN'],
        ['leg_no' => 2, 'flight_date' => date('Y-m-d', strtotime("+$i weeks +1 day")), 'flight_no' => 'SQ' . rand(100, 999), 'sector' => 'SIN-JED']
    ], $userId);

    // B. Convert to Movement (Stages 2, 3, 4)
    if ($stage >= 2) {
        $pnr = strtoupper(substr(md5(uniqid()), 0, 6));
        $mvId = Movement::create([
            'category' => $cat,
            'agent_id' => $agentId,
            'agent_name' => $agentName,
            'created_date' => date('Y-m-d'),
            'pnr' => $pnr,
            'tour_code' => ($isHajji ? 'HAJ' : 'UMR') . "-$pax-" . date('My'),
            'carrier' => 'SQ',
            'passenger_count' => $pax,
            'selling_fare' => $fare,
            'total_selling' => $pax * $fare,
            'ticketing_deadline' => date('Y-m-d', strtotime("+ ".rand(1, 10)." days")),
            'dp1_status' => ($stage >= 4) ? 'PAID' : 'UNPAID'
        ], $userId);

        // C. Create Invoice (Stages 3, 4)
        if ($stage >= 3) {
            $invId = Invoice::create([
                'invoice_no' => "INV/2026/00$i",
                'invoice_date' => date('Y-m-d'),
                'corporate_name' => $corpName,
                'pnr' => $pnr,
                'total_pax' => $pax,
                'amount_idr' => $pax * $fare,
                'status' => ($stage == 4) ? 'PAID' : 'UNPAID'
            ], [], [
                ['description' => "Tiket $cat Group", 'total_pax' => $pax, 'fare_amount' => $fare, 'amount_idr' => $pax * $fare]
            ], $userId);

            // D. Record Payment (Stage 4)
            if ($stage == 4) {
                Payment::create([
                    'invoice_id' => $invId,
                    'amount_paid' => $pax * $fare,
                    'payment_date' => date('Y-m-d'),
                    'payment_method' => 'Transfer Bank Syariah',
                    'notes' => 'Full Payment - Lunas'
                ]);
            }
        }
    }
}

echo "Seeding Complete: 30 records created across different lifecycle stages.\n";

