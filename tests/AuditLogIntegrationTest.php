<?php
namespace Tests;

require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/AuditLog.php';

/**
 * @covers User
 * @covers AuditLog
 */
class AuditLogIntegrationTest extends TestCase
{
    public function testUserCreationCreatesAuditLog()
    {
        // 1. Create a user
        $userData = [
            'username' => 'audit_test_create',
            'password' => 'secret123',
            'role_id' => 1, // Assumes role 1 exists, usually admin
            'full_name' => 'Audit Test User'
        ];
        
        // Ensure role exists
        $stmt = self::$pdo->prepare("INSERT IGNORE INTO roles (id, name, description) VALUES (1, 'admin', 'Administrator')");
        $stmt->execute();

        $userId = \User::create($userData);
        $this->assertNotFalse($userId, "User creation failed");

        // 2. Verify Audit Log
        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE action = 'USER_CREATED' AND entity_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$userId]);
        $log = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotFalse($log, "Audit log for USER_CREATED not found");
        $this->assertEquals('user', $log['entity_type']);
        $newValue = json_decode($log['new_value'], true);
        $this->assertEquals($userData['username'], $newValue['username']);
        $this->assertArrayNotHasKey('password', $newValue, "Password should not be logged");
    }

    public function testUserUpdateCreatesAuditLog()
    {
        // 1. Setup User
        $userId = $this->createTestUser('audit_test_update');
        $oldUser = \User::readById($userId);

        // 2. Update User
        $updateData = ['full_name' => 'Updated Name'];
        $success = \User::update($userId, $updateData);
        $this->assertTrue($success, "User update failed");

        // 3. Verify Audit Log
        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE action = 'USER_UPDATED' AND entity_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$userId]);
        $log = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotFalse($log, "Audit log for USER_UPDATED not found");
        $this->assertEquals('user', $log['entity_type']);
        
        $oldVal = json_decode($log['old_value'], true);
        $newVal = json_decode($log['new_value'], true);

        $this->assertEquals('Test User', $oldVal['full_name']);
        $this->assertEquals('Updated Name', $newVal['full_name']);
    }

    public function testUserDeleteCreatesAuditLog()
    {
        // 1. Setup User
        $userId = $this->createTestUser('audit_test_delete');

        // 2. Delete User
        $success = \User::delete($userId);
        $this->assertTrue($success, "User delete failed");

        // 3. Verify Audit Log
        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE action = 'USER_DELETED' AND entity_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$userId]);
        $log = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotFalse($log, "Audit log for USER_DELETED not found");
        $this->assertEquals('user', $log['entity_type']);
        
        $oldVal = json_decode($log['old_value'], true);
        $this->assertEquals('audit_test_delete', $oldVal['username']);
        $this->assertNull($log['new_value']);
    }
}