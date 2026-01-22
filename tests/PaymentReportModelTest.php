<?php
require_once __DIR__ . '/../app/models/PaymentReport.php';
use PHPUnit\Framework\TestCase;

class PaymentReportModelTest extends Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$pdo->exec("DELETE FROM payment_report_lines");
        self::$pdo->exec("DELETE FROM movements");
    }

    public function testGetReportSeparatesSalesAndCostLines()
    {
        // 1. Setup Movement
        self::$pdo->prepare("INSERT INTO movements (id, pnr, tour_code) VALUES (1, 'PNR123', 'TC123')")->execute();

        // 2. Setup Lines
        $stmt = self::$pdo->prepare("INSERT INTO payment_report_lines (reference_id, table_type, remarks) VALUES (?, ?, ?)");
        $stmt->execute(['PNR123', 'SALES', 'Sales line 1']);
        $stmt->execute(['PNR123', 'COST', 'Cost line 1']);
        $stmt->execute(['PNR123', 'COST', 'Cost line 2']);

        // 3. Fetch Report
        $report = \PaymentReport::getReportByMovementId(1);

        $this->assertNotFalse($report);
        $this->assertCount(1, $report['sales_lines']);
        $this->assertCount(2, $report['cost_lines']);
        $this->assertEquals('Sales line 1', $report['sales_lines'][0]['remarks']);
        $this->assertEquals('Cost line 1', $report['cost_lines'][0]['remarks']);
        $this->assertEquals('Cost line 2', $report['cost_lines'][1]['remarks']);
    }

    public function testGetReportIncludesMovementSummaryFields()
    {
        self::$pdo->prepare("INSERT INTO movements (id, pnr, incentive_amount, discount_amount) VALUES (2, 'PNR456', 5000, 1000)")->execute();

        $report = \PaymentReport::getReportByMovementId(2);

        $this->assertEquals(5000, $report['incentive_amount']);
        $this->assertEquals(1000, $report['discount_amount']);
    }
}
