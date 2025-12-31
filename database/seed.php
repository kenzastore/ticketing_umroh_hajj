<?php
// Include the database connection
require_once __DIR__ . '/../includes/db_connect.php';

echo "Starting database seeding...\n";

// --- Helper Functions for Data Generation ---

function generateRandomName($gender = null) {
    $firstNamesMale = ['Budi', 'Joko', 'Andi', 'Rizky', 'Fajar', 'Aditya', 'Eko', 'Agus', 'Teguh', 'Dimas'];
    $firstNamesFemale = ['Siti', 'Dewi', 'Ayu', 'Nurul', 'Indah', 'Fitri', 'Dina', 'Rina', 'Wati', 'Lisa'];
    $lastNames = ['Santoso', 'Pratama', 'Wijaya', 'Nugroho', 'Hidayat', 'Putra', 'Sari', 'Lestari', 'Puspita', 'Susanto'];

    $firstName = '';
    if ($gender === 'male') {
        $firstName = $firstNamesMale[array_rand($firstNamesMale)];
    } elseif ($gender === 'female') {
        $firstName = $firstNamesFemale[array_rand($firstNamesFemale)];
    } else {
        $allFirstNames = array_merge($firstNamesMale, $firstNamesFemale);
        $firstName = $allFirstNames[array_rand($allFirstNames)];
    }

    $lastName = $lastNames[array_rand($lastNames)];
    return $firstName . ' ' . $lastName;
}

function generateRandomPhoneNumber() {
    $prefix = ['0812', '0813', '0821', '0852', '0853', '0857', '0858', '0877', '0878', '0819'];
    return $prefix[array_rand($prefix)] . mt_rand(10000000, 99999999);
}

function generateRandomEmail($name) {
    $domains = ['example.com', 'mail.co.id', 'domain.net'];
    $name = str_replace(' ', '.', strtolower($name));
    return $name . '@' . $domains[array_rand($domains)];
}

function generateRandomAddress() {
    $streets = ['Jalan Sudirman', 'Jalan Thamrin', 'Jalan Gatot Subroto', 'Jalan Asia Afrika', 'Jalan Merdeka', 'Jalan Diponegoro', 'Jalan Pahlawan'];
    $cities = ['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Yogyakarta', 'Semarang', 'Denpasar', 'Makassar', 'Palembang', 'Balikpapan'];
    return $streets[array_rand($streets)] . ' No. ' . mt_rand(1, 200) . ', ' . $cities[array_rand($cities)];
}

function generateRandomDate($start_date, $end_date) {
    $timestamp = mt_rand(strtotime($start_date), strtotime($end_date));
    return date('Y-m-d', $timestamp);
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// --- Seeding Logic ---

$pdo->beginTransaction();

try {
    // Clear existing data (optional, for fresh seeding)
    echo "Clearing existing data (from dependents first)...";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE bookings;");
    $pdo->exec("TRUNCATE TABLE requests;");
    $pdo->exec("TRUNCATE TABLE agents;");
    $pdo->exec("TRUNCATE TABLE users;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    echo "Existing data cleared.\n";

    // 1. Seed Agents
    echo "Seeding agents...\n";
    $agent_ids = [];
    $stmt = $pdo->prepare("INSERT INTO agents (name, contact_person, phone, email, address) VALUES (?, ?, ?, ?, ?)");
    for ($i = 0; $i < 50; $i++) {
        $name = generateRandomName();
        $contactPerson = generateRandomName();
        $phone = generateRandomPhoneNumber();
        $email = generateRandomEmail($name);
        $address = generateRandomAddress();
        $stmt->execute([$name, $contactPerson, $phone, $email, $address]);
        $agent_ids[] = $pdo->lastInsertId();
    }
    echo "Seeded " . count($agent_ids) . " agents.\n";

    // 2. Seed Users
    echo "Seeding users...\n";
    $role_map = []; // To map role names to IDs
    $roles_stmt = $pdo->query("SELECT id, name FROM roles");
    while ($row = $roles_stmt->fetch(PDO::FETCH_ASSOC)) {
        $role_map[$row['name']] = $row['id'];
    }

    $user_ids = [];
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role_id, full_name) VALUES (?, ?, ?, ?)");
    $password_hash = password_hash('password123', PASSWORD_BCRYPT); // Dummy password

    for ($i = 0; $i < 50; $i++) {
        $fullName = generateRandomName();
        $username = 'user' . ($i + 1); // Simple unique username
        $roleName = ['admin', 'finance', 'monitor'][array_rand(['admin', 'finance', 'monitor'])];
        $roleId = $role_map[$roleName];
        $stmt->execute([$username, $password_hash, $roleId, $fullName]);
        $user_ids[] = $pdo->lastInsertId();
    }
    // Add specific users if needed, e.g., for testing specific roles
    $stmt->execute(['admin', $password_hash, $role_map['admin'], 'Admin User']);
    $user_ids[] = $pdo->lastInsertId();
    $stmt->execute(['finance', $password_hash, $role_map['finance'], 'Finance User']);
    $user_ids[] = $pdo->lastInsertId();
    $stmt->execute(['monitor', $password_hash, $role_map['monitor'], 'Monitor User']);
    $user_ids[] = $pdo->lastInsertId();

    echo "Seeded " . count($user_ids) . " users.\n";

    // 3. Seed Requests
    echo "Seeding requests...\n";
    $request_ids = [];
    $airlines = ['Garuda Indonesia', 'Lion Air', 'Batik Air', 'Citilink', 'Sriwijaya Air', 'AirAsia', 'Saudia Airlines', 'Turkish Airlines'];
    $stmt = $pdo->prepare("INSERT INTO requests (agent_id, pax_count, travel_date_start, travel_date_end, duration_days, airline_preference, routing_preference, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    for ($i = 0; $i < 100; $i++) {
        $agentId = $agent_ids[array_rand($agent_ids)];
        $paxCount = mt_rand(1, 50);
        $startDate = generateRandomDate('2024-01-01', '2026-12-31');
        $endDate = date('Y-m-d', strtotime($startDate . ' +' . mt_rand(5, 30) . ' days'));
        $duration = (new DateTime($endDate))->diff(new DateTime($startDate))->days;
        $airlinePref = $airlines[array_rand($airlines)];
        $routingPref = 'CGK-JED-MED'; // Common Umroh/Hajj routing
        $notes = 'Dummy request notes for ' . generateRandomName();
        $stmt->execute([$agentId, $paxCount, $startDate, $endDate, $duration, $airlinePref, $routingPref, $notes]);
        $request_ids[] = $pdo->lastInsertId();
    }
    echo "Seeded " . count($request_ids) . " requests.\n";

    // 4. Seed Bookings
    echo "Seeding bookings...\n";
    $statuses = ['NEW', 'QUOTED', 'BLOCKED', 'PNR_ISSUED', 'IN_MOVEMENT', 'INVOICED', 'PAID_DEPOSIT', 'PAID_FULL', 'CONFIRMED_FP_MERAH', 'REPORTED', 'RECEIPTED', 'CHANGED', 'CANCELED'];
    $stmt = $pdo->prepare("INSERT INTO bookings (request_id, pnr_code, status) VALUES (?, ?, ?)");
    for ($i = 0; $i < 150; $i++) { // More bookings than requests
        $requestId = $request_ids[array_rand($request_ids)];
        $pnrCode = generateRandomString(6);
        $status = $statuses[array_rand($statuses)];
        $stmt->execute([$requestId, $pnrCode, $status]);
    }
    echo "Seeded 150 bookings.\n";

    $pdo->commit();
    echo "Database seeding completed successfully!\n";

} catch (PDOException $e) {
    $pdo->rollBack();
    die("Database seeding failed: " . $e->getMessage());
}
