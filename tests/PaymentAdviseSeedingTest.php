<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/PaymentAdvise.php';

class PaymentAdviseSeedingTest extends TestCase
{
    protected static $db;

    public static function setUpBeforeClass(): void
    {
        global $pdo;
        self::$db = $pdo;
        PaymentAdvise::init(self::$db);
    }

    public function testPaymentAdviseTableHasSeededData()
    {
        $advises = PaymentAdvise::readAll();
        
        // We expect at least 10 records as per spec
        $this->assertGreaterThanOrEqual(10, count($advises), "Should have at least 10 seeded payment advises.");
        
        $pendingCount = 0;
        $transferredCount = 0;
        $airlines = [];

        foreach ($advises as $a) {
            if ($a['date_bank_transferred']) {
                $transferredCount++;
            } else {
                $pendingCount++;
            }
            if ($a['company_name']) {
                $airlines[] = $a['company_name'];
            }
            
            // Verify link to movement
            $this->assertNotEmpty($a['movement_id'], "Each advice should be linked to a movement.");
            $this->assertNotEmpty($a['pnr'], "PNR should be populated.");
            $this->assertNotEmpty($a['tour_code'], "Tour Code should be populated.");
            
            // Verify financial logic (approximate)
            if ($a['total_amount'] > 0) {
                $this->assertEquals($a['total_amount'] * 0.2, $a['deposit_amount'], "Deposit should be 20% of total.");
                $this->assertEquals($a['total_amount'] * 0.8, $a['balance_payment_amount'], "Balance should be 80% of total.");
            }
        }

        $this->assertGreaterThan(0, $pendingCount, "Should have some PENDING records.");
        $this->assertGreaterThan(0, $transferredCount, "Should have some TRANSFERRED records.");
        $this->assertGreaterThan(1, count(array_unique($airlines)), "Should have records for varied airlines.");
    }
}
