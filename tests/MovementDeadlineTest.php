<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../includes/db_connect.php';

class MovementDeadlineTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        global $pdo;
        $this->pdo = $pdo;
        
        // Clean up movements table for isolation (assuming test DB)
        // In a real env we might use transactions, but for now we'll delete what we create or truncate
        $this->pdo->exec("DELETE FROM movements");
    }

    public function testGetUpcomingDeadlines()
    {
        // 1. Create a movement due tomorrow
        $dueTomorrow = date('Y-m-d', strtotime('+1 day'));
        $this->pdo->exec("INSERT INTO movements (movement_no, agent_name, ticketing_deadline, ticketing_done) VALUES (101, 'Test Agent 1', '$dueTomorrow', 0)");

        // 2. Create a movement due in 5 days (should not be returned)
        $dueIn5Days = date('Y-m-d', strtotime('+5 days'));
        $this->pdo->exec("INSERT INTO movements (movement_no, agent_name, ticketing_deadline, ticketing_done) VALUES (102, 'Test Agent 2', '$dueIn5Days', 0)");

        // 3. Create a movement due tomorrow but already done (should not be returned)
        $this->pdo->exec("INSERT INTO movements (movement_no, agent_name, ticketing_deadline, ticketing_done) VALUES (103, 'Test Agent 3', '$dueTomorrow', 1)");

        // Call the method
        $deadlines = Movement::getUpcomingDeadlines(3);

        // Assertions
        $this->assertCount(1, $deadlines, "Should return exactly 1 upcoming deadline");
        $this->assertEquals('Test Agent 1', $deadlines[0]['agent_name']);
        $this->assertEquals($dueTomorrow, $deadlines[0]['ticketing_deadline']);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM movements");
    }
}
