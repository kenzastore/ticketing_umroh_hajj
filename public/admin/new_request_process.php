<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
check_auth('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agent_id = !empty($_POST['agent_id']) ? $_POST['agent_id'] : null;
    $pax_count = $_POST['pax_count'];
    $travel_date_start = $_POST['travel_date_start'];
    $travel_date_end = $_POST['travel_date_end'];
    $airline_preference = $_POST['airline_preference'];
    $notes = $_POST['notes'];

    try {
        $pdo->beginTransaction();

        // Insert into requests
        $stmt = $pdo->prepare("INSERT INTO requests (agent_id, pax_count, travel_date_start, travel_date_end, airline_preference, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$agent_id, $pax_count, $travel_date_start, $travel_date_end, $airline_preference, $notes]);
        $request_id = $pdo->lastInsertId();

        // Insert into bookings (Initial status NEW)
        $stmt = $pdo->prepare("INSERT INTO bookings (request_id, status) VALUES (?, 'NEW')");
        $stmt->execute([$request_id]);

        $pdo->commit();
        header('Location: dashboard.php?success=request_created');
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error creating request: " . $e->getMessage());
    }
}
