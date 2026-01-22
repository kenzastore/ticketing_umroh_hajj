<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/PaymentReport.php';

class PaymentReportSeederTest extends Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // We don't delete here because we want to test if the seeder actually RAN and produced data.
        // Or we run the seeder IN the test.
    }

    public function testSeederProducesCorrectData()
    {
        // 1. Run the seeder
        $seederPath = __DIR__ . '/../database/seed_payment_report_trid.php';
        if (!file_exists($seederPath)) {
            $this->markTestSkipped('Seeder script not yet created.');
        }

        // Execute seeder in a sub-process or include it
        // Since it's a script that might exit or echo, let's execute via shell
        exec("php " . escapeshellarg($seederPath), $output, $returnVar);
        $this->assertEquals(0, $returnVar, "Seeder script failed to execute.");

        // 2. Verify Movement
        $stmt = self::$pdo->prepare("SELECT * FROM movements WHERE pnr = 'VFUQ8X' LIMIT 1");
        $stmt->execute();
        $movement = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($movement, "Movement with PNR VFUQ8X should exist.");
        $this->assertEquals('EBAD WISATA', $movement['agent_name']);
        $this->assertEquals('11MAY-45S-26D-TRID', $movement['tour_code']);
        $this->assertEquals(4500000, $movement['incentive_amount']);

        // 3. Verify Flight Legs
        $stmtLegs = self::$pdo->prepare("SELECT COUNT(*) FROM flight_legs WHERE movement_id = ?");
        $stmtLegs->execute([$movement['id']]);
        $legCount = $stmtLegs->fetchColumn();
        $this->assertEquals(4, $legCount, "Should have 4 flight legs.");

        // 4. Verify Payment Report Lines (SALES)
        $stmtSales = self::$pdo->prepare("SELECT COUNT(*) FROM payment_report_lines WHERE reference_id = 'VFUQ8X' AND table_type = 'SALES'");
        $stmtSales->execute();
        $salesCount = $stmtSales->fetchColumn();
        $this->assertGreaterThanOrEqual(4, $salesCount, "Should have at least 4 Sales lines (Fare + 3 Deposits).");

        // 5. Verify Payment Report Lines (COST)
        $stmtCost = self::$pdo->prepare("SELECT COUNT(*) FROM payment_report_lines WHERE reference_id = 'VFUQ8X' AND table_type = 'COST'");
        $stmtCost->execute();
        $costCount = $stmtCost->fetchColumn();
        $this->assertGreaterThanOrEqual(4, $costCount, "Should have at least 4 Cost lines (Fare + 3 Deposits).");
        
        // 6. Verify specific bank data
        $stmtBank = self::$pdo->prepare("SELECT * FROM payment_report_lines WHERE reference_id = 'VFUQ8X' AND table_type = 'SALES' AND remarks LIKE '%DEPOSIT-T 1%' LIMIT 1");
        $stmtBank->execute();
        $bankLine = $stmtBank->fetch(PDO::FETCH_ASSOC);
        $this->assertNotFalse($bankLine);
        $this->assertEquals('PT ELANG', $bankLine['bank_to']);
    }
}
