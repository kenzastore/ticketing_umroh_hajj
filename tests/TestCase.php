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
            // Force re-include to get the variable in local scope
            ob_start();
            $pdo = null;
            require __DIR__ . '/../includes/db_connect.php';
            ob_end_clean();
            
            if (isset($pdo)) {
                self::$pdo = $pdo;
            } else {
                global $pdo;
                self::$pdo = $pdo;
            }
        }
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
