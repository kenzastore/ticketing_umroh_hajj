<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/PaymentReport.php';
require_once __DIR__ . '/../app/models/AuditLog.php';
require_once __DIR__ . '/../app/models/BookingRequest.php';

class TridHajjSeederTest extends Tests\TestCase
{
    public function testSeederProducesSynchronizedData()
    {
        $seederPath = __DIR__ . '/../database/seed_uat_trid_hajj.php';
        if (!file_exists($seederPath)) {
            $this->markTestSkipped('Seeder script not yet created.');
        }

        // Execute seeder
        exec("php " . escapeshellarg($seederPath), $output, $returnVar);
        $this->assertEquals(0, $returnVar, "Seeder script failed to execute.");

        // 1. Verify Booking Request (Stage 1)
        $stmt = self::$pdo->prepare("SELECT * FROM booking_requests WHERE agent_name = 'EBAD WISATA' AND group_size = 45 LIMIT 1");
        $stmt->execute();
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotFalse($request, "Booking Request should exist.");
        $this->assertEquals(1, $request['is_converted']);

        // 2. Verify Movement (Stage 2)
        $stmt = self::$pdo->prepare("SELECT * FROM movements WHERE pnr = 'VFUQ8X' LIMIT 1");
        $stmt->execute();
        $movement = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotFalse($movement, "Movement with PNR VFUQ8X should exist.");
        $this->assertEquals('11MAY-45S-26D-TRID', $movement['tour_code']);
        $this->assertEquals(1, $movement['ticketing_done']);

        // 3. Verify Flight Legs (4 segments)
        $stmtLegs = self::$pdo->prepare("SELECT COUNT(*) FROM flight_legs WHERE movement_id = ?");
        $stmtLegs->execute([$movement['id']]);
        $this->assertEquals(4, $stmtLegs->fetchColumn());

        // 4. Verify Financials (Stage 3)
        // Sales Lines
        $stmtSales = self::$pdo->prepare("SELECT COUNT(*) FROM payment_report_lines WHERE reference_id = 'VFUQ8X' AND table_type = 'SALES'");
        $stmtSales->execute();
        $this->assertGreaterThanOrEqual(4, $stmtSales->fetchColumn());

        // Cost Lines
        $stmtCost = self::$pdo->prepare("SELECT COUNT(*) FROM payment_report_lines WHERE reference_id = 'VFUQ8X' AND table_type = 'COST'");
        $stmtCost->execute();
        $this->assertGreaterThanOrEqual(4, $stmtCost->fetchColumn());

        // Incentive Check
        $this->assertEquals(4500000, $movement['incentive_amount']);

        // 5. Verify Audit Logs (Stage 4)
        $stmtLogs = self::$pdo->prepare("SELECT COUNT(*) FROM audit_logs WHERE entity_id = ? OR old_value LIKE ? OR new_value LIKE ?");
        $stmtLogs->execute([$movement['id'], '%VFUQ8X%', '%VFUQ8X%']);
        $this->assertGreaterThan(0, $stmtLogs->fetchColumn(), "Should have audit logs for this movement/PNR.");
    }
}
