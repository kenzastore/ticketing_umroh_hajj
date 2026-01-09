<?php
namespace Tests;

require_once __DIR__ . '/../app/models/AuditLog.php';

/**
 * @covers AuditLog
 */
class AuditLogTest extends TestCase
{
    public function testLogEntryCreation()
    {
        $userId = $this->createTestUser('audit_user');
        $action = 'TEST_ACTION';
        $entityType = 'test_entity';
        $entityId = 123;
        $oldValue = 'old';
        $newValue = 'new';

        $result = \AuditLog::log($userId, $action, $entityType, $entityId, $oldValue, $newValue);
        $this->assertTrue($result);

        $stmt = self::$pdo->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 1");
        $log = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals($userId, $log['user_id']);
        $this->assertEquals($action, $log['action']);
        $this->assertEquals($entityType, $log['entity_type']);
        $this->assertEquals($entityId, $log['entity_id']);
        $this->assertEquals($oldValue, $log['old_value']);
        $this->assertEquals($newValue, $log['new_value']);
    }

    public function testLogWithJsonValues()
    {
        $userId = $this->createTestUser('audit_json_user');
        $oldData = ['status' => 'pending', 'pax' => 10];
        $newData = ['status' => 'confirmed', 'pax' => 10];

        // AuditLog now auto-json_encodes
        $result = \AuditLog::log($userId, 'UPDATE', 'test', 1, $oldData, $newData);
        $this->assertTrue($result);

        $stmt = self::$pdo->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 1");
        $log = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals(json_encode($oldData), $log['old_value']);
        $this->assertEquals(json_encode($newData), $log['new_value']);
    }

    public function testLogCreateAndDelete()
    {
        $userId = $this->createTestUser('audit_cd_user');

        // Test CREATE (oldValue is null)
        $newData = ['id' => 1, 'name' => 'New Item'];
        $result = \AuditLog::log($userId, 'CREATE', 'item', 1, null, $newData);
        $this->assertTrue($result);

        $stmt = self::$pdo->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 1");
        $log = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertEquals('CREATE', $log['action']);
        $this->assertNull($log['old_value']); // Should be stored as NULL in DB
        $this->assertEquals(json_encode($newData), $log['new_value']);

        // Test DELETE (newValue is null)
        $oldData = ['id' => 1, 'name' => 'Old Item'];
        $result = \AuditLog::log($userId, 'DELETE', 'item', 1, $oldData, null);
        $this->assertTrue($result);

        $stmt = self::$pdo->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 1");
        $log = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertEquals('DELETE', $log['action']);
        $this->assertEquals(json_encode($oldData), $log['old_value']);
        $this->assertNull($log['new_value']); // Should be stored as NULL
    }
}
