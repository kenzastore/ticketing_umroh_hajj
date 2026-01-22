<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/PaymentReport.php';
require_once __DIR__ . '/../app/models/AuditLog.php';

class PaymentReportE2ETest extends Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$pdo->exec("DELETE FROM audit_logs");
        self::$pdo->exec("DELETE FROM payment_report_lines");
        self::$pdo->exec("DELETE FROM movements");
        self::$pdo->exec("DELETE FROM users");
        $this->createTestUser('e2e_user');
    }

    public function testFullPaymentReportLifecycle()
    {
        $stmt = self::$pdo->query("SELECT id FROM users LIMIT 1");
        $userId = $stmt->fetchColumn();

        // 1. Create Movement
        $movementId = Movement::create([
            'pnr' => 'E2E-PNR',
            'tour_code' => 'E2E-TC',
            'agent_name' => 'E2E Agent',
            'deposit1_eemw_date' => '2026-02-01'
        ], [], $userId);
        $this->assertNotFalse($movementId);

        // 2. Add Payment Lines (one SALES, one COST)
        $salesLineId = PaymentReport::createLine([
            'reference_id' => 'E2E-PNR',
            'table_type' => 'SALES',
            'remarks' => 'DEPOSIT 1',
            'time_limit_date' => '2026-02-01',
            'debit_amount' => 1000000,
            'bank_to' => 'MANDIRI',
            'bank_to_number' => '123456789'
        ], $userId);
        $this->assertNotFalse($salesLineId);

        $costLineId = PaymentReport::createLine([
            'reference_id' => 'E2E-PNR',
            'table_type' => 'COST',
            'remarks' => 'COST TO AIRLINE',
            'debit_amount' => 800000,
            'bank_from' => 'MANDIRI',
            'bank_from_number' => '123456789',
            'bank_to' => 'FLYSCOOT',
            'bank_to_number' => '987654321'
        ], $userId);
        $this->assertNotFalse($costLineId);

        // 3. Verify Report Categorization
        $report = PaymentReport::getReportByMovementId($movementId);
        $this->assertCount(1, $report['sales_lines']);
        $this->assertCount(1, $report['cost_lines']);
        $this->assertEquals(1000000, $report['sales_lines'][0]['debit_amount']);
        $this->assertEquals(800000, $report['cost_lines'][0]['debit_amount']);

        // 4. Update Summary via Movement
        Movement::update($movementId, [
            'incentive_amount' => 200000,
            'discount_amount' => 10000
        ], $userId);

        $updatedReport = PaymentReport::getReportByMovementId($movementId);
        $this->assertEquals(200000, $updatedReport['incentive_amount']);
        $this->assertEquals(10000, $updatedReport['discount_amount']);

        // 5. Verify Audit Logs
        $stmt = self::$pdo->query("SELECT * FROM audit_logs ORDER BY id DESC");
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // We expect: CREATE line (x2), UPDATE movement
        $actions = array_column($logs, 'action');
        $this->assertContains('CREATE', $actions);
        $this->assertContains('UPDATE', $actions);
        
        $lineLog = array_filter($logs, function($l) { return $l['entity_type'] === 'payment_report_line'; });
        $this->assertNotEmpty($lineLog);
    }
}
