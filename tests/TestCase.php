<?php
namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PDO;

abstract class TestCase extends BaseTestCase
{
    protected static $pdo;

    public static function setUpBeforeClass(): void
    {
        if (!self::$pdo) {
            global $pdo;
            self::$pdo = $pdo;
        }
    }

    protected function createTestUser($username = 'testuser')
    {
        $stmt = self::$pdo->prepare("INSERT IGNORE INTO roles (name) VALUES ('admin')");
        $stmt->execute();
        
        $stmt = self::$pdo->query("SELECT id FROM roles WHERE name = 'admin' LIMIT 1");
        $roleId = $stmt->fetchColumn();

        $stmt = self::$pdo->prepare("INSERT INTO users (username, password, role_id, full_name) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, 'password', $roleId, 'Test User']);
        return self::$pdo->lastInsertId();
    }

    protected function setUp(): void
    {
        self::$pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        if (self::$pdo->inTransaction()) {
            self::$pdo->rollBack();
        }
    }
}
