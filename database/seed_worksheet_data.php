<?php
require_once __DIR__ . '/../includes/db_connect.php';

echo "Starting seed process for Worksheet-based schema...\n";

try {
    $pdo->beginTransaction();

    // 1. Clear existing data (in order of dependencies)
    echo "Cleaning up old data...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $tables = ['invoice_fare_lines', 'invoice_flight_lines', 'invoices', 'flight_legs', 'movements', 'booking_request_legs', 'booking_requests', 'agents', 'corporates', 'users', 'roles'];
    foreach ($tables as $table) {
        $pdo->exec("TRUNCATE TABLE $table");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // 2. Roles
    echo "Seeding Roles...\n";
    $stmt = $pdo->prepare("INSERT INTO roles (name, description) VALUES (?, ?)");
    $stmt->execute(['admin', 'Full access']);
    $adminRoleId = $pdo->lastInsertId();
    $stmt->execute(['finance', 'Finance access']);
    $financeRoleId = $pdo->lastInsertId();
    $stmt->execute(['monitor', 'Monitor access']);

    // 3. Users (Password: admin123)
    echo "Seeding Users...\n";
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role_id, full_name) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', $password, $adminRoleId, 'Super Admin']);
    $stmt->execute(['finance', $password, $financeRoleId, 'Finance User']);

    // 4. Corporates
    echo "Seeding Corporates...\n";
    $stmt = $pdo->prepare("INSERT INTO corporates (name, address) VALUES (?, ?)");
    $stmt->execute(['PT Elang Emas Wisata', 'Jakarta Selatan']);
    $corp1Id = $pdo->lastInsertId();
    $stmt->execute(['Kyai Haji Abdullah Group', 'Surabaya, Jawa Timur']);
    $corp2Id = $pdo->lastInsertId();

    // 5. Agents
    echo "Seeding Agents...\n";
    $stmt = $pdo->prepare("INSERT INTO agents (name, skyagent_id, phone, email) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Agent Amanah', 'SKY-001', '0812345678', 'amanah@travel.com']);
    $agent1Id = $pdo->lastInsertId();
    $stmt->execute(['Berkah Wisata', 'SKY-002', '0819876543', 'berkah@travel.com']);
    $agent2Id = $pdo->lastInsertId();

    // 6. Booking Requests & Legs
    echo "Seeding Booking Requests...\n";
    $stmtReq = $pdo->prepare("INSERT INTO booking_requests (request_no, corporate_id, corporate_name, agent_id, agent_name, skyagent_id, group_size, selling_fare, duration_days, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtReq->execute([101, $corp1Id, 'PT Elang Emas Wisata', $agent1Id, 'Agent Amanah', 'SKY-001', 45, 1200000000, 9, 'Urgent group for Ramadhan']);
    $req1Id = $pdo->lastInsertId();

    $stmtLeg = $pdo->prepare("INSERT INTO booking_request_legs (booking_request_id, leg_no, flight_date, flight_no, sector) VALUES (?, ?, ?, ?, ?)");
    $stmtLeg->execute([$req1Id, 1, '2026-03-10', 'TR596', 'SUB-SIN']);
    $stmtLeg->execute([$req1Id, 2, '2026-03-10', 'TR597', 'SIN-JED']);

    // 7. Movements (Active Workflows)
    echo "Seeding Movements...\n";
    $stmtMov = $pdo->prepare("INSERT INTO movements (movement_no, agent_name, pnr, tour_code, carrier, passenger_count, dp1_status, fp_status, ticketing_done, flight_no_out, sector_out, flight_no_in, sector_in) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Movement 1: Ticketing Done (Ready for Invoice)
    $stmtMov->execute([
        1, 'Agent Amanah', 'ABC123', 'EEMW-RAMADHAN-01', 'Scoot', 45, 'PAID', 'UNPAID', 1, 'TR596', 'SUB-JED', 'TR597', 'JED-SUB'
    ]);
    $mov1Id = $pdo->lastInsertId();

    // Movement 2: In Progress
    $stmtMov->execute([
        2, 'Berkah Wisata', 'PNR789', 'EEMW-HAJI-SPEC', 'Saudia', 50, 'UNPAID', 'UNPAID', 0, 'SV811', 'JKT-MED', 'SV812', 'JED-JKT'
    ]);
    $mov2Id = $pdo->lastInsertId();

    // 8. Flight Legs for Movement 1 (to test FullView)
    $stmtFL = $pdo->prepare("INSERT INTO flight_legs (movement_id, leg_no, direction, flight_no, sector, scheduled_departure) VALUES (?, ?, ?, ?, ?, ?)");
    $stmtFL->execute([$mov1Id, 1, 'OUT', 'TR596', 'SUB-SIN', '2026-03-10']);
    $stmtFL->execute([$mov1Id, 2, 'OUT', 'TR597', 'SIN-JED', '2026-03-10']);

    $pdo->commit();
    echo "Seeding completed successfully!\n";
    echo "Default Login: admin / admin123\n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error during seeding: " . $e->getMessage() . "\n";
}
