<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/Movement.php';

// Clear movements
$pdo->exec("DELETE FROM movements");

$umrahData = [
    [
        'category' => 'UMRAH',
        'agent_name' => 'JALAN JALAN HOLIDAY',
        'created_date' => '2025-07-18',
        'pnr' => 'ACDFRG',
        'dp1_status' => 'XXXXXX',
        'dp2_status' => 'XXXXX',
        'fp_status' => '21-Jul-25',
        'tour_code' => '02AUG-15-12D-BICGK',
        'carrier' => 'BICGK',
        'flight_no_out' => 'BI796',
        'sector_out' => 'SUBBWN',
        'dep_seg1_date' => '2025-08-02',
        'passenger_count' => 15,
        'approved_fare' => 9000000,
        'selling_fare' => 9100000,
        'total_selling' => 136500000,
        'ticketing_deadline' => '2025-07-21'
    ],
    [
        'category' => 'UMRAH',
        'agent_name' => 'ELANG EMAS WISATA',
        'created_date' => '2025-05-06',
        'pnr' => 'GHT3VT',
        'dp1_status' => 'PAID',
        'fp_status' => 'PAID',
        'tour_code' => '4AUG-80-12D-GASUB',
        'carrier' => 'GASUB',
        'passenger_count' => 80,
        'ticketing_done' => 1
    ]
];

$hajjiData = [
    [
        'category' => 'HAJJI',
        'agent_name' => 'HAJJI TRAVEL ABC',
        'created_date' => '2025-06-01',
        'pnr' => 'HAJ001',
        'tour_code' => '09MAY-45S-25D-TRID',
        'carrier' => 'TRID',
        'passenger_count' => 45,
        'approved_fare' => 60,
        'selling_fare' => 45,
        'ticketing_deadline' => '2026-05-09'
    ]
];

foreach ($umrahData as $d) Movement::create($d);
foreach ($hajjiData as $d) Movement::create($d);

echo "Seed data for Movement Dashboard completed.\n";

