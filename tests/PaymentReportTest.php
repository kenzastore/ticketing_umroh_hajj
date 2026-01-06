<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/models/PaymentReport.php';
require_once __DIR__ . '/../includes/db_connect.php';

class PaymentReportTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->pdo->exec("DELETE FROM movements");
        $this->pdo->exec("DELETE FROM payment_report_lines");
    }

    public function testGetReportByMovementId()
    {
        // 1. Create a movement
        $this->pdo->exec("INSERT INTO movements (id, pnr, tour_code, agent_name) VALUES (999, 'ME822K', '10MAR-35-18D-TRID', 'JALAN JALAN HOLIDAYS')");
        
        // 2. Create some report lines
        $this->pdo->exec("INSERT INTO payment_report_lines (reference_id, remarks, fare_per_pax, debit_amount) VALUES ('ME822K', 'SELLING FARES', 10000000, 350000000)");

        $report = PaymentReport::getReportByMovementId(999);

        $this->assertNotFalse($report);
        $this->assertEquals('ME822K', $report['pnr']);
        $this->assertNotEmpty($report['report_lines']);
        $this->assertEquals('SELLING FARES', $report['report_lines'][0]['remarks']);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM movements");
        $this->pdo->exec("DELETE FROM payment_report_lines");
    }
}
