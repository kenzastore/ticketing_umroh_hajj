<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/models/PaymentAdvise.php';
require_once __DIR__ . '/../includes/db_connect.php';

class PaymentAdviseTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->pdo->exec("DELETE FROM payment_advises");
    }

    public function testCreateAndReadAdvise()
    {
        $data = [
            'agent_name' => 'PT JALAN JALAN',
            'tour_code' => '5FEB-40-13D-TRID',
            'pnr' => 'TRV12JT',
            'date_created' => '2025-12-30',
            'approved_fare' => 10000000,
            'total_amount' => 320000000
        ];

        $id = PaymentAdvise::create($data);
        $this->assertNotFalse($id);

        $advise = PaymentAdvise::readById($id);
        $this->assertEquals('PT JALAN JALAN', $advise['agent_name']);
        $this->assertEquals('TRV12JT', $advise['pnr']);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM payment_advises");
    }
}
