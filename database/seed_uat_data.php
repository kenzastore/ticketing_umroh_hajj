<?php
/**
 * UAT Data Seeding Script
 * Generates 100 Indonesian dummy records for testing.
 */

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/Agent.php';
require_once __DIR__ . '/../app/models/Corporate.php';
require_once __DIR__ . '/../app/models/BookingRequest.php';
require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/Invoice.php';
require_once __DIR__ . '/../app/models/Payment.php';

// Data Constants
define('INDONESIAN_NAMES', [
    'Budi Santoso', 'Siti Aminah', 'Agus Prayitno', 'Dewi Lestari', 'Eko Saputro',
    'Rina Wijaya', 'Hendra Kusuma', 'Sri Wahyuni', 'Andi Pratama', 'Maya Sari',
    'Joko Susilo', 'Ani Rahayu', 'Dedi Kurniawan', 'Lilik Setiawan', 'Siska Amelia',
    'Rully Hidayat', 'Yuni Kartika', 'Fajar Ramadhan', 'Indah Permata', 'Zulkifli Mansyur'
]);

define('INDONESIAN_AGENTS', [
    'Mutiara Tour & Travel',
    'Amanah Wisata Umroh',
    'Barokah Haji Services',
    'Cahaya Iman Jakarta',
    'Duta Mulia Surabaya'
]);

define('INDONESIAN_CORPORATES', [
    'PT Maju Jaya Abadi',
    'CV Sumber Makmur',
    'Yayasan Amal Sholeh',
    'Koperasi Karyawan Sejahtera',
    'Bank Syariah Indonesia (Cabang)'
]);

define('INDONESIAN_AIRPORTS', ['SUB', 'CGK', 'JOG', 'DPS', 'KNO']);
define('DESTINATION_AIRPORTS', ['JED', 'MED', 'SIN']);
define('AIRLINE_CARRIERS', ['Garuda Indonesia', 'Saudia Airlines', 'Lion Air', 'Citilink', 'Scoot']);

// Ensure global $pdo is available for models
if (isset($pdo)) {
    Agent::init($pdo);
    Corporate::init($pdo);
    BookingRequest::init($pdo);
    Movement::init($pdo);
    Invoice::init($pdo);
    Payment::init($pdo);
}

// Cleanup existing data if requested
if (isset($argv) && in_array('--cleanup', $argv)) {
    echo "Cleaning up existing data...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $tables = ['payments', 'invoice_fare_lines', 'invoice_flight_lines', 'invoices', 'flight_legs', 'movements', 'booking_request_legs', 'booking_requests', 'agents', 'corporates', 'audit_logs', 'notifications'];
    foreach ($tables as $table) {
        $pdo->exec("TRUNCATE TABLE $table");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Cleanup complete.\n";
}

echo "UAT Seeding Script Initialized.\n";

// Phase 2: Data Generation Logic

// Task 4: Master Data Seeding
echo "Seeding Master Data (Agents & Corporates)...\n";
try {
    foreach (INDONESIAN_AGENTS as $index => $name) {
        Agent::create([
            'name' => $name,
            'skyagent_id' => 'SA-' . (1000 + $index),
            'phone' => '0812' . rand(10000000, 99999999),
            'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com'
        ]);
    }

    foreach (INDONESIAN_CORPORATES as $name) {
        Corporate::create([
            'name' => $name,
            'address' => 'Jl. Kebon Sirih No. ' . rand(1, 100) . ', Jakarta'
        ]);
    }
    echo "Master Data seeded successfully.\n";
} catch (Exception $e) {
    echo "Error seeding Master Data: " . $e->getMessage() . "\n";
}

// Task 5: Booking Request Seeding
echo "Seeding Booking Requests (30 records)...\n";
try {
    $agents = Agent::readAll();
    $corporates = Corporate::readAll();

    for ($i = 1; $i <= 30; $i++) {
        $agent = $agents[array_rand($agents)];
        $corp = $corporates[array_rand($corporates)];
        $groupSize = rand(5, 120);
        
        $legs = [];
        $numLegs = rand(1, 4);
        for ($j = 1; $j <= $numLegs; $j++) {
            $legs[] = [
                'leg_no' => $j,
                'flight_date' => date('Y-m-d', strtotime('+' . rand(10, 60) . ' days')),
                'flight_no' => 'GA' . rand(100, 999),
                'sector' => INDONESIAN_AIRPORTS[array_rand(INDONESIAN_AIRPORTS)] . '-' . DESTINATION_AIRPORTS[array_rand(DESTINATION_AIRPORTS)]
            ];
        }

        BookingRequest::create([
            'request_no' => 2026000 + $i,
            'corporate_id' => $corp['id'],
            'corporate_name' => $corp['name'],
            'agent_id' => $agent['id'],
            'agent_name' => $agent['name'],
            'skyagent_id' => $agent['skyagent_id'],
            'group_size' => $groupSize,
            'tcp' => $groupSize * 15000000,
            'selling_fare' => 16000000,
            'nett_fare' => 14000000,
            'duration_days' => rand(9, 15),
            'notes' => 'UAT Dummy Request #' . $i
        ], $legs);
    }
    echo "Booking Requests seeded successfully.\n";
} catch (Exception $e) {
    echo "Error seeding Booking Requests: " . $e->getMessage() . "\n";
}

// Task 6: Movement Seeding
echo "Seeding Movements (40 records, including H-3/Past Due)...\n";
try {
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $tmrw = date('Y-m-d', strtotime('+1 day'));
    $h3 = date('Y-m-d', strtotime('+3 days'));

    for ($i = 1; $i <= 40; $i++) {
        $agent = $agents[array_rand($agents)];
        $pax = rand(5, 50);
        
        $data = [
            'pnr' => strtoupper(substr(md5(uniqid()), 0, 6)),
            'tour_code' => 'TC-' . date('My') . '-' . sprintf('%03d', $i),
            'agent_id' => $agent['id'],
            'agent_name' => $agent['name'],
            'passenger_count' => $pax,
            'carrier' => AIRLINE_CARRIERS[array_rand(AIRLINE_CARRIERS)],
            'created_date' => $today,
            'dp1_status' => 'PENDING',
            'dp2_status' => 'PENDING',
            'fp_status' => 'PENDING',
            'ticketing_done' => 0
        ];

        // Specific Scenarios
        if ($i <= 5) { // Past Due Ticketing
            $data['ticketing_deadline'] = $yesterday;
        } elseif ($i <= 10) { // H-3 Ticketing
            $data['ticketing_deadline'] = $h3;
        } elseif ($i <= 15) { // H-3 DP1
            $data['deposit1_airlines_date'] = $h3;
            $data['deposit1_eemw_date'] = $h3;
        } elseif ($i <= 20) { // H-3 FP
            $data['fullpay_airlines_date'] = $h3;
            $data['fullpay_eemw_date'] = $h3;
        } else { // Future/Mixed
            $data['ticketing_deadline'] = date('Y-m-d', strtotime('+' . rand(5, 20) . ' days'));
            $data['dp1_status'] = (rand(0, 1) ? 'PAID' : 'PENDING');
        }

        $legs = [[
            'leg_no' => 1,
            'direction' => 'OUT',
            'flight_no' => 'GA' . rand(100, 999),
            'sector' => 'SUB-CGK',
            'scheduled_departure' => $tmrw
        ]];

        Movement::create($data, $legs);
    }
    echo "Movements seeded successfully.\n";
} catch (Exception $e) {
    echo "Error seeding Movements: " . $e->getMessage() . "\n";
}

// Task 7: Invoice & Payment Seeding
echo "Seeding Invoices & Payments (20 records)...\n";
try {
    $movements = Movement::readAll();
    $corporates = Corporate::readAll();

    for ($i = 1; $i <= 20; $i++) {
        $mv = $movements[array_rand($movements)];
        $corp = $corporates[array_rand($corporates)];
        
        $header = [
            'invoice_no' => 'INV/2026/' . sprintf('%04d', $i),
            'invoice_date' => date('Y-m-d'),
            'corporate_id' => $corp['id'],
            'corporate_name' => $corp['name'],
            'pnr' => $mv['pnr'],
            'tour_code' => $mv['tour_code'],
            'total_pax' => $mv['passenger_count'],
            'fare_per_pax' => 16000000,
            'amount_idr' => $mv['passenger_count'] * 16000000
        ];

        $flightLines = []; // Optional for seeding simple UAT data
        $fareLines = [[
            'line_no' => 1,
            'description' => 'Ticket Fare',
            'total_pax' => $mv['passenger_count'],
            'fare_amount' => 16000000,
            'amount_idr' => $mv['passenger_count'] * 16000000
        ]];

        $invoiceId = Invoice::create($header, $flightLines, $fareLines);

        if ($invoiceId) {
            // Random payment for some
            if ($i % 2 == 0) {
                $amount = ($i % 4 == 0) ? ($mv['passenger_count'] * 16000000) : 5000000;
                Payment::create([
                    'invoice_id' => $invoiceId,
                    'amount_paid' => $amount,
                    'payment_date' => date('Y-m-d'),
                    'payment_method' => 'BANK TRANSFER',
                    'reference_number' => 'UAT-' . $i,
                    'notes' => 'UAT Dummy Payment'
                ]);
            }
        }
    }
    echo "Invoices & Payments seeded successfully.\n";
} catch (Exception $e) {
    echo "Error seeding Invoices: " . $e->getMessage() . "\n";
}

echo "UAT Seeding Completed.\n";

