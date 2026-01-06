<?php
namespace Tests;

use App\Models\Movement;
use PDO;

class MovementTest extends TestCase
{
    /** 
     * @test 
     * @covers \Movement::updateStatus
     */
    public function it_logs_audit_trail_on_status_change()
    {
        $userId = $this->createTestUser('movement_updater');
        
        // Setup initial movement record
        self::$pdo->prepare("INSERT INTO movements (pnr, dp1_status) VALUES ('PNR123', 'UNPAID')")->execute();
        $movementId = self::$pdo->lastInsertId();

        $updateData = ['dp1_status' => 'PAID'];
        $result = \Movement::updateStatus($movementId, $updateData, $userId);
        $this->assertTrue($result);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'movement' AND entity_id = ? AND action = 'UPDATE' ORDER BY id DESC LIMIT 1");
        $stmt->execute([$movementId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        
        $oldValue = json_decode($log['old_value'], true);
        $newValue = json_decode($log['new_value'], true);

        $this->assertEquals('UNPAID', $oldValue['dp1_status']);
        $this->assertEquals('PAID', $newValue['dp1_status']);
    }
}
