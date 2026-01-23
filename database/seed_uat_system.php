<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/BookingRequest.php';
require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/Invoice.php';
require_once __DIR__ . '/../app/models/Payment.php';
require_once __DIR__ . '/../app/models/PaymentReport.php';
require_once __DIR__ . '/../app/models/PaymentAdvise.php';
require_once __DIR__ . '/../app/models/AuditLog.php';

echo "--- UAT DATA GENERATION SYSTEM (INDONESIAN LOCALIZED) ---\n";
echo "Starting generation of 100 synchronized UAT records...\n";

function getRandomIndonesianName() {
    $first = ['Budi', 'Siti', 'Andi', 'Agus', 'Dewi', 'Eko', 'Iwan', 'Lestari', 'Maya', 'Rina', 'Sutrisno', 'Tri', 'Wahyu', 'Yanto', 'Zulkifli', 'Aditya', 'Bambang', 'Cahyo', 'Dian', 'Endang'];
    $last = ['Santoso', 'Aminah', 'Kurniawan', 'Hidayat', 'Putri', 'Saputra', 'Wijaya', 'Sari', 'Utami', 'Pratama', 'Nugroho', 'Wulandari', 'Setiawan', 'Ramadhani', 'Larasati'];
    return $first[array_rand($first)] . ' ' . $last[array_rand($last)];
}

function getRandomIndonesianCompany() {
    $prefix = ['PT', 'CV', 'UD'];
    $name = ['Sukses Makmur', 'Jaya Abadi', 'Maju Terus', 'Sumber Rejeki', 'Cahaya Baru', 'Berlian Sejahtera', 'Global Mandiri', 'Nusantara Indah', 'Sinar Harapan', 'Karya Kita'];
    return $prefix[array_rand($prefix)] . ' ' . $name[array_rand($name)];
}

function getRandomIndonesianAddress() {
    $streets = ['Jl. Merdeka', 'Jl. Sudirman', 'Jl. Gajah Mada', 'Jl. Thamrin', 'Jl. Pemuda', 'Jl. Diponegoro', 'Jl. Kartini', 'Jl. Hayam Wuruk'];
    $cities = [
        ['Jakarta Pusat', 'DKI Jakarta'],
        ['Jakarta Selatan', 'DKI Jakarta'],
        ['Surabaya', 'Jawa Timur'],
        ['Bandung', 'Jawa Barat'],
        ['Medan', 'Sumatera Utara'],
        ['Semarang', 'Jawa Tengah'],
        ['Makassar', 'Sulawesi Selatan'],
        ['Palembang', 'Sumatera Selatan']
    ];
    $city = $cities[array_rand($cities)];
    return $streets[array_rand($streets)] . ' No. ' . rand(1, 150) . ', ' . $city[0] . ', ' . $city[1];
}

function generateNIK() {
    return '32' . rand(10, 75) . rand(10, 50) . rand(100000, 999999) . '0001';
}

function generateNPWP() {
    return rand(10, 99) . '.' . rand(100, 999) . '.' . rand(100, 999) . '.' . rand(1, 9) . '-' . rand(100, 999) . '.000';
}

function generateIndonesianPhone() {
    $prefixes = ['0811', '0812', '0813', '0821', '0852', '0857', '0878', '0896'];
    return $prefixes[array_rand($prefixes)] . rand(1000000, 99999999);
}

try {
    $pdo->beginTransaction();

    // 1. DATA CLEANUP (Wipe and Replace)
    echo "Purging existing operational data...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $tables = ['payments', 'payment_advises', 'invoice_fare_lines', 'invoice_flight_lines', 'invoices', 
              'flight_status_events', 'flight_status_snapshots', 'flight_legs', 'movements', 
              'booking_request_legs', 'booking_requests', 'audit_logs', 'agents', 'corporates'];
    foreach($tables as $table) {
        $pdo->exec("TRUNCATE TABLE $table");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // 2. MASTER DATA SETUP
    echo "Seeding Roles and Admin User...\n";
    $pdo->exec("INSERT IGNORE INTO roles (id, name) VALUES (1, 'admin'), (2, 'finance'), (3, 'monitor'), (4, 'operational')");
    $pdo->prepare("INSERT IGNORE INTO users (id, username, password, role_id, full_name) VALUES (?, ?, ?, ?, ?)")
        ->execute([1, 'admin', password_hash('admin123', PASSWORD_DEFAULT), 1, 'Administrator Sistem']);

    echo "Seeding Master Data (Agents & Corporates)...\n";
    $agents = [];
    for ($i = 1; $i <= 10; $i++) {
        $name = "Agen UAT " . getRandomIndonesianName();
        $pdo->prepare("INSERT INTO agents (name, skyagent_id, phone, email) VALUES (?, ?, ?, ?)")
            ->execute([$name, "SKY-" . rand(1000, 9999), generateIndonesianPhone(), strtolower(str_replace(' ', '', $name)) . "@mail.id"]);
        $agents[] = ['id' => $pdo->lastInsertId(), 'name' => $name];
    }

    $corporates = [];
    for ($i = 1; $i <= 5; $i++) {
        $name = getRandomIndonesianCompany();
        $address = getRandomIndonesianAddress();
        $pdo->prepare("INSERT INTO corporates (name, address) VALUES (?, ?)")
            ->execute([$name, $address]);
        $corporates[] = ['id' => $pdo->lastInsertId(), 'name' => $name, 'address' => $address];
    }

    $adminId = 1;

    // 3. GENERATE 100 WORKFLOW RECORDS
    echo "Generating 100 synchronized Indonesian records...\n";
    for ($i = 1; $i <= 100; $i++) {
        $agent = $agents[array_rand($agents)];
        $corp = $corporates[array_rand($corporates)];
        $pax = rand(15, 50);
        $requestDate = date('Y-m-d H:i:s', strtotime("-" . rand(30, 60) . " days"));
        
        // --- STAGE 1: Booking Request ---
        $requestId = BookingRequest::create([
            'request_no' => 2000 + $i,
            'corporate_id' => $corp['id'],
            'corporate_name' => $corp['name'],
            'agent_id' => $agent['id'],
            'agent_name' => $agent['name'],
            'group_size' => $pax,
            'duration_days' => 12,
            'selling_fare' => 15000000,
            'nett_fare' => 14000000,
            'created_at' => $requestDate,
            'notes' => "Permintaan UAT untuk " . $pax . " jamaah. NIK PIC: " . generateNIK()
        ], [
            ['leg_no' => 1, 'flight_no' => 'GA' . rand(100, 999), 'sector' => 'SUB-SIN', 'flight_date' => date('Y-m-d', strtotime("+3 months"))],
            ['leg_no' => 2, 'flight_no' => 'GA' . rand(100, 999), 'sector' => 'SIN-JED', 'flight_date' => date('Y-m-d', strtotime("+3 months"))]
        ], $adminId);

        $pdo->prepare("UPDATE audit_logs SET created_at = ? WHERE entity_type = 'booking_request' AND entity_id = ?")->execute([$requestDate, $requestId]);

        // Workflow split
        if ($i <= 25) continue; // Stage 1 ONLY

        // --- STAGE 2: Active Movement ---
        $pnr = strtoupper(substr(md5($i), 0, 6));
        $conversionDate = date('Y-m-d H:i:s', strtotime($requestDate . " +5 days"));
        
        $movementData = [
            'booking_request_id' => $requestId,
            'movement_no' => 2000 + $i,
            'agent_id' => $agent['id'],
            'agent_name' => $agent['name'],
            'pnr' => $pnr,
            'tour_code' => "ID-" . date('Y') . "-" . $pnr,
            'passenger_count' => $pax,
            'selling_fare' => 15000000,
            'nett_selling' => 14000000,
            'total_selling' => $pax * 15000000,
            'created_at' => $conversionDate,
            'created_date' => date('Y-m-d', strtotime($conversionDate)),
            'belonging_to' => 'STAF-UAT-' . rand(1, 5)
        ];

        if ($i > 75) {
            $movementData['ticketing_deadline'] = date('Y-m-d', strtotime("+1 day"));
            $movementData['deposit1_eemw_date'] = date('Y-m-d', strtotime("-1 day"));
            $movementData['dp1_status'] = 'BELUM BAYAR';
        } else if ($i > 50) {
            $movementData['ticketing_done'] = 1;
            $movementData['dp1_status'] = 'LUNAS';
            $movementData['dp2_status'] = 'LUNAS';
            $movementData['fp_status'] = 'LUNAS';
        } else {
            $movementData['dp1_status'] = 'LUNAS';
            $movementData['dp2_status'] = 'PROSES';
        }

        $movementId = Movement::create($movementData, [], $adminId);
        $pdo->prepare("UPDATE audit_logs SET created_at = ? WHERE entity_type = 'movement' AND entity_id = ?")->execute([$conversionDate, $movementId]);
        $pdo->prepare("UPDATE booking_requests SET is_converted = 1 WHERE id = ?")->execute([$requestId]);

        // --- STAGE 3: Financials ---
        if ($i > 50) {
            $invoiceDate = date('Y-m-d', strtotime($conversionDate . " +2 days"));
            $invoiceId = Invoice::create([
                'invoice_no' => "INV/EEMW/" . date('Y') . "/" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'invoice_date' => $invoiceDate,
                'corporate_id' => $corp['id'],
                'corporate_name' => $corp['name'],
                'pnr' => $pnr,
                'tour_code' => $movementData['tour_code'],
                'total_pax' => $pax,
                'fare_per_pax' => 15000000,
                'amount_idr' => $pax * 15000000,
                'address' => $corp['address'],
                'ref_text' => "NPWP: " . generateNPWP()
            ], [], [], $adminId);

            if ($i <= 75) {
                Payment::create([
                    'invoice_id' => $invoiceId,
                    'amount_paid' => $pax * 15000000,
                    'payment_date' => date('Y-m-d', strtotime($invoiceDate . " +1 day")),
                    'payment_method' => 'TRANSFER BANK',
                    'payment_stage' => 'Full Payment',
                    'notes' => 'Pembayaran lunas via Bank Mandiri'
                ]);
                
                PaymentAdvise::create([
                    'movement_id' => $movementId,
                    'pnr' => $pnr,
                    'agent_name' => $agent['name'],
                    'tour_code' => $movementData['tour_code'],
                    'total_amount' => $pax * 14000000,
                    'transfer_amount' => $pax * 14000000,
                    'status' => 'TERKIRIM',
                    'company_name' => 'Garuda Indonesia',
                    'date_created' => date('Y-m-d', strtotime($invoiceDate . " +2 days"))
                ], $adminId);
            }
        }
    }

    $pdo->commit();
    echo "Successfully generated 100 Indonesian UAT records.\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Generation failed: " . $e->getMessage() . "\n");
}