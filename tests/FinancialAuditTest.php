<?php
namespace Tests;

require_once __DIR__ . '/../app/models/Invoice.php';
require_once __DIR__ . '/../app/models/Payment.php';
require_once __DIR__ . '/../app/models/PaymentAdvise.php';

use PDO;

/**
 * @covers \Invoice
 * @covers \Payment
 * @covers \PaymentAdvise
 */
class FinancialAuditTest extends TestCase
{
    public function testInvoiceCreationLogsAudit()
    {
        $userId = $this->createTestUser('inv_creator');
        
        $header = ['invoice_no' => 'INV001', 'amount_idr' => 1000000];
        $id = \Invoice::create($header, [], [], $userId);
        $this->assertNotFalse($id);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'invoice' AND entity_id = ? AND action = 'CREATE'");
        $stmt->execute([$id]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        $newValue = json_decode($log['new_value'], true);
        $this->assertEquals('INV001', $newValue['invoice_no']);
    }

    public function testInvoiceStatusChangeLogsAudit()
    {
        $userId = $this->createTestUser('inv_upd');
        $id = \Invoice::create(['invoice_no' => 'INV002'], [], [], $userId);

        $result = \Invoice::updateStatus($id, 'PAID', $userId);
        $this->assertTrue($result);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'invoice' AND entity_id = ? AND action = 'STATUS_CHANGE'");
        $stmt->execute([$id]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        $newValue = json_decode($log['new_value'], true);
        $this->assertEquals('PAID', $newValue['status']);
    }

    public function testPaymentRecordingLogsAudit()
    {
        $userId = $this->createTestUser('pay_recorder');
        // Need to fake session for Payment::create usage of $_SESSION
        $_SESSION['user_id'] = $userId;

        $invId = \Invoice::create(['invoice_no' => 'INV003', 'amount_idr' => 500000], [], [], $userId);

        $paymentData = [
            'invoice_id' => $invId,
            'amount_paid' => 500000,
            'payment_date' => date('Y-m-d'),
            'payment_method' => 'TRANSFER'
        ];

        $payId = \Payment::create($paymentData);
        $this->assertNotFalse($payId);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'payment' AND entity_id = ? AND action = 'PAYMENT_RECORDED'");
        $stmt->execute([$payId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        $newVal = json_decode($log['new_value'], true);
        $this->assertEquals(500000, $newVal['amount_paid']);
        $this->assertArrayHasKey('receipt_hash', $newVal);
    }

    public function testPaymentAdviseLogsAudit()
    {
        $userId = $this->createTestUser('pa_user');
        
        // 1. Create
        $data = ['agent_name' => 'Agent X', 'total_amount' => 1000];
        $id = \PaymentAdvise::create($data, $userId);
        $this->assertNotFalse($id);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'payment_advise' AND entity_id = ? AND action = 'CREATE'");
        $stmt->execute([$id]);
        $log = $stmt->fetch();
        $this->assertNotFalse($log);

        // 2. Update
        $result = \PaymentAdvise::update($id, ['agent_name' => 'Agent Y'], $userId);
        $this->assertTrue($result);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'payment_advise' AND entity_id = ? AND action = 'UPDATE'");
        $stmt->execute([$id]);
        $log = $stmt->fetch();
        $this->assertNotFalse($log);
        $newVal = json_decode($log['new_value'], true);
        $this->assertEquals('Agent Y', $newVal['agent_name']);

        // 3. Delete
        $result = \PaymentAdvise::delete($id, $userId);
        $this->assertTrue($result);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'payment_advise' AND entity_id = ? AND action = 'DELETE'");
        $stmt->execute([$id]);
        $log = $stmt->fetch();
        $this->assertNotFalse($log);
    }
}
