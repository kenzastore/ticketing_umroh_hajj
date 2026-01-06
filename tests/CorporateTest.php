<?php
namespace Tests;

use App\Models\Corporate;
use App\Models\AuditLog;
use PDO;

class CorporateTest extends TestCase
{
    /** 
     * @test 
     * @covers \Corporate::create
     */
    public function it_logs_audit_trail_on_creation()
    {
        $userId = $this->createTestUser('corp_creator');
        
        $data = [
            'name' => 'Test Corporate',
            'address' => 'Test Address'
        ];

        $corporateId = \Corporate::create($data, $userId);
        $this->assertNotFalse($corporateId);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'corporate' AND entity_id = ? AND action = 'CREATE'");
        $stmt->execute([$corporateId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        $this->assertEquals($userId, $log['user_id']);
        
        $newValue = json_decode($log['new_value'], true);
        $this->assertEquals('Test Corporate', $newValue['name']);
    }

    /** 
     * @test 
     * @covers \Corporate::update
     */
    public function it_logs_audit_trail_on_update()
    {
        $userId = $this->createTestUser('corp_updater');
        $corporateId = \Corporate::create(['name' => 'Old Corp'], $userId);
        
        $updateData = ['name' => 'New Corp'];
        $result = \Corporate::update($corporateId, $updateData, $userId);
        $this->assertTrue($result);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'corporate' AND entity_id = ? AND action = 'UPDATE' ORDER BY id DESC LIMIT 1");
        $stmt->execute([$corporateId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        
        $oldValue = json_decode($log['old_value'], true);
        $newValue = json_decode($log['new_value'], true);

        $this->assertEquals('Old Corp', $oldValue['name']);
        $this->assertEquals('New Corp', $newValue['name']);
    }

    /** 
     * @test 
     * @covers \Corporate::delete
     */
    public function it_logs_audit_trail_on_deletion()
    {
        $userId = $this->createTestUser('corp_deleter');
        $corporateId = \Corporate::create(['name' => 'To Be Deleted'], $userId);

        $result = \Corporate::delete($corporateId, $userId);
        $this->assertTrue($result);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'corporate' AND entity_id = ? AND action = 'DELETE'");
        $stmt->execute([$corporateId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        $oldValue = json_decode($log['old_value'], true);
        $this->assertEquals('To Be Deleted', $oldValue['name']);
        $this->assertNull($log['new_value']);
    }
}
