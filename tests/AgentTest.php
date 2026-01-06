<?php
namespace Tests;

use App\Models\Agent;
use App\Models\AuditLog;
use PDO;

class AgentTest extends TestCase
{
    /** 
     * @test 
     * @covers \Agent::create
     */
    public function it_logs_audit_trail_on_creation()
    {
        $userId = $this->createTestUser('agent_creator');
        
        $data = [
            'name' => 'Test Agent',
            'skyagent_id' => 'SKY123',
            'phone' => '0812345678',
            'email' => 'test@agent.com'
        ];

        $agentId = \Agent::create($data, $userId);
        $this->assertNotFalse($agentId);

        // Check audit_logs table
        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'agent' AND entity_id = ? AND action = 'CREATE'");
        $stmt->execute([$agentId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log, "Audit log was not created");
        $this->assertEquals($userId, $log['user_id']);
        
        $newValue = json_decode($log['new_value'], true);
        $this->assertEquals('Test Agent', $newValue['name']);
        $this->assertEquals('SKY123', $newValue['skyagent_id']);
    }

    /** 
     * @test 
     * @covers \Agent::update
     */
    public function it_logs_audit_trail_on_update()
    {
        $userId = $this->createTestUser('agent_updater');
        $agentId = \Agent::create(['name' => 'Original Name'], $userId);
        
        $updateData = ['name' => 'Updated Name'];
        $result = \Agent::update($agentId, $updateData, $userId);
        $this->assertTrue($result);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'agent' AND entity_id = ? AND action = 'UPDATE' ORDER BY id DESC LIMIT 1");
        $stmt->execute([$agentId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log, "Audit log was not created for update");
        
        $oldValue = json_decode($log['old_value'], true);
        $newValue = json_decode($log['new_value'], true);

        $this->assertEquals('Original Name', $oldValue['name']);
        $this->assertEquals('Updated Name', $newValue['name']);
    }

    /** 
     * @test 
     * @covers \Agent::delete
     */
    public function it_logs_audit_trail_on_deletion()
    {
        $userId = $this->createTestUser('agent_deleter');
        $agentId = \Agent::create(['name' => 'To Be Deleted'], $userId);

        $result = \Agent::delete($agentId, $userId);
        $this->assertTrue($result);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'agent' AND entity_id = ? AND action = 'DELETE'");
        $stmt->execute([$agentId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        $oldValue = json_decode($log['old_value'], true);
        $this->assertEquals('To Be Deleted', $oldValue['name']);
        $this->assertNull($log['new_value']);
    }
}
