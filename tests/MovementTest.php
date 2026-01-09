<?php
namespace Tests;

use PDO;

class MovementTest extends TestCase
{
    /** 
     * @test 
     * @covers \Movement::create
     */
    public function testCreateLogsAudit()
    {
        $userId = $this->createTestUser('mv_creator');
        
        $data = ['pnr' => 'NEWPNR', 'tour_code' => 'TC123'];
        $movementId = \Movement::create($data, [], $userId);
        
        $this->assertNotFalse($movementId);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'movement' AND entity_id = ? AND action = 'CREATE'");
        $stmt->execute([$movementId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        $newValue = json_decode($log['new_value'], true);
        $this->assertEquals('NEWPNR', $newValue['pnr']);
    }

    /** 
     * @test 
     * @covers \Movement::update
     */
    public function testUpdateLogsAudit()
    {
        $userId = $this->createTestUser('mv_updater');
        
        // Setup initial movement record
        self::$pdo->prepare("INSERT INTO movements (pnr, tour_code) VALUES ('PNR_UPD', 'TC_OLD')")->execute();
        $movementId = self::$pdo->lastInsertId();

        $updateData = ['tour_code' => 'TC_NEW'];
        $result = \Movement::update($movementId, $updateData, $userId);
        $this->assertTrue($result);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'movement' AND entity_id = ? AND action = 'UPDATE' ORDER BY id DESC LIMIT 1");
        $stmt->execute([$movementId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        
        $oldValue = json_decode($log['old_value'], true);
        $newValue = json_decode($log['new_value'], true);

        $this->assertEquals('TC_OLD', $oldValue['tour_code']);
        $this->assertEquals('TC_NEW', $newValue['tour_code']);
    }

    /** 
     * @test 
     * @covers \Movement::updateStatus
     */
    public function testUpdateStatusLogsAudit()
    {
        $userId = $this->createTestUser('mv_stat_updater');
        
        // Setup initial movement record
        self::$pdo->prepare("INSERT INTO movements (pnr, dp1_status) VALUES ('PNR_STAT', 'UNPAID')")->execute();
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
