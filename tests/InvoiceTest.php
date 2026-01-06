<?php
namespace Tests;

use App\Models\Invoice;
use PDO;

class InvoiceTest extends TestCase
{
    /** 
     * @test 
     * @covers \Invoice::create
     */
    public function it_logs_audit_trail_on_creation()
    {
        $userId = $this->createTestUser('invoice_creator');
        
        $header = [
            'invoice_no' => 'INV-TEST-001',
            'corporate_name' => 'Invoice Corp',
            'amount_idr' => 1000000
        ];
        $flightLines = [];
        $fareLines = [
            ['description' => 'Base Fare', 'amount_idr' => 1000000]
        ];

        $invoiceId = \Invoice::create($header, $flightLines, $fareLines, $userId);
        $this->assertNotFalse($invoiceId);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'invoice' AND entity_id = ? AND action = 'CREATE'");
        $stmt->execute([$invoiceId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        $newValue = json_decode($log['new_value'], true);
        $this->assertEquals('INV-TEST-001', $newValue['invoice_no']);
        $this->assertNotEmpty($newValue['fare_lines']);
    }
}
