<?php
use PHPUnit\Framework\TestCase;

class NotificationModelTest extends TestCase {
    private $pdo;
    private $stmt;

    protected function setUp(): void {
        $this->pdo = $this->createMock(PDO::class);
        $this->stmt = $this->createMock(PDOStatement::class);

        // Initialize the Notification model
        require_once __DIR__ . '/../app/models/Notification.php';
        Notification::init($this->pdo);
    }

    public function testCreateNotification() {
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('INSERT INTO notifications'))
            ->willReturn($this->stmt);

        $this->stmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $result = Notification::create([
            'entity_type' => 'test',
            'entity_id' => 1,
            'message' => 'Test Message',
            'alert_type' => 'INFO'
        ]);
        $this->assertTrue($result);
    }

    public function testExistsUnread() {
        // We expect this method to be added to Notification.php
        // It should prepare a SELECT statement
        
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('SELECT id FROM notifications'))
            ->willReturn($this->stmt);

        $this->stmt->expects($this->once())
            ->method('execute')
            ->with($this->callback(function($params) {
                // Params: [entity_type, entity_id, message_prefix%]
                return $params[0] === 'order' && $params[1] === 101 && strpos($params[2], 'Order') !== false;
            }))
            ->willReturn(true);

        $this->stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => 1]); // Simulate finding a row

        // This call will fail if the method doesn't exist yet
        $exists = Notification::existsUnread('order', 101, 'Order');
        $this->assertTrue($exists);
    }
}