<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/models/BookingRequest.php';
require_once __DIR__ . '/../app/models/Movement.php';

class UatSeederLogicTest extends Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // The seeder itself will handle cleaning, but for unit testing logic 
        // we might want a clean state for the test case transaction.
    }

    public function testSeederScriptExists()
    {
        $this->assertFileExists(__DIR__ . '/../database/seed_uat_system.php');
    }

    public function testSeederProducesCorrectTotalRecords()
    {
        // 1. Execute seeder
        exec("php " . __DIR__ . '/../database/seed_uat_system.php', $output, $returnVar);
        $this->assertEquals(0, $returnVar, "Seeder failed to execute");

        // 2. Verify Booking Requests count
        $stmt = self::$pdo->query("SELECT COUNT(*) FROM booking_requests");
        $count = $stmt->fetchColumn();
        $this->assertEquals(100, $count, "Should have 100 booking requests total");

        // 3. Verify conversion distribution (approx 75% converted)
        $stmtConverted = self::$pdo->query("SELECT COUNT(*) FROM booking_requests WHERE is_converted = 1");
        $convertedCount = $stmtConverted->fetchColumn();
        $this->assertEquals(75, $convertedCount, "Should have 75 converted requests");

        // 4. Verify movements count matches converted requests
        $stmtMovements = self::$pdo->query("SELECT COUNT(*) FROM movements");
        $mvCount = $stmtMovements->fetchColumn();
        $this->assertEquals(75, $mvCount, "Should have 75 movements");
    }

    public function testDataSynchronizationIntegrity()
    {
        // Execute seeder
        exec("php " . __DIR__ . '/../database/seed_uat_system.php');

        // Pick a random converted request
        $stmt = self::$pdo->query("SELECT id, agent_name, group_size FROM booking_requests WHERE is_converted = 1 LIMIT 1");
        $br = $stmt->fetch(PDO::FETCH_ASSOC);

        // Find linked movement
        $stmtMv = self::$pdo->prepare("SELECT agent_name, passenger_count FROM movements WHERE booking_request_id = ?");
        $stmtMv->execute([$br['id']]);
        $mv = $stmtMv->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($mv, "Movement should exist for converted request");
        $this->assertEquals($br['agent_name'], $mv['agent_name'], "Agent name must sync");
        $this->assertEquals($br['group_size'], $mv['passenger_count'], "Pax count must sync");
    }
}
