<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/db_connect.php';

echo "<h2>Debugging Flight Statuses</h2>";

try {
    // Get distinct status from flight_legs
    echo "<h3>Flight_legs Status Distribution:</h3>";
    $stmt = $pdo->query("SELECT status_normalized, COUNT(*) as count FROM flight_legs GROUP BY status_normalized");
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";

    // Get a sample of flight_legs, especially those with UNKNOWN
    echo "<h3>Sample Flight_legs (first 10, or with UNKNOWN):</h3>";
    $stmt = $pdo->query("SELECT id, flight_ident, dep_date, status_normalized, last_fetched_at, next_poll_at FROM flight_legs WHERE status_normalized = 'UNKNOWN' LIMIT 10");
    $unknown_flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($unknown_flights)) {
        $stmt = $pdo->query("SELECT id, flight_ident, dep_date, status_normalized, last_fetched_at, next_poll_at FROM flight_legs LIMIT 10");
        $sample_flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $sample_flights = $unknown_flights;
    }
    echo "<pre>";
    print_r($sample_flights);
    echo "</pre>";

    // Get a sample of flight_status_snapshots for one of the sample flights
    if (!empty($sample_flights)) {
        $sample_leg_id = $sample_flights[0]['id'];
        echo "<h3>Latest Snapshot for Leg ID {$sample_leg_id}:</h3>";
        $stmt = $pdo->prepare("SELECT * FROM flight_status_snapshots WHERE leg_id = ? ORDER BY fetched_at DESC LIMIT 1");
        $stmt->execute([$sample_leg_id]);
        echo "<pre>";
        print_r($stmt->fetch(PDO::FETCH_ASSOC));
        echo "</pre>";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
