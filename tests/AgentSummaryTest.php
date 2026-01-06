<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/models/Agent.php';
require_once __DIR__ . '/../includes/db_connect.php';

class AgentSummaryTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->pdo->exec("DELETE FROM movements");
        $this->pdo->exec("DELETE FROM payments");
        $this->pdo->exec("DELETE FROM invoices");
    }

    public function testGetAgentSummary()
    {
        // 1. Create a movement for Agent A
        $this->pdo->exec("INSERT INTO movements (pnr, agent_name, passenger_count, total_selling) VALUES ('PNRA1', 'Agent A', 10, 1000000)");
        
        // 2. Create a movement for Agent B
        $this->pdo->exec("INSERT INTO movements (pnr, agent_name, passenger_count, total_selling) VALUES ('PNRB1', 'Agent B', 5, 500000)");

        $summary = Agent::getAgentSummary();

        $this->assertCount(2, $summary);
        
        // Check Agent A
        $foundA = false;
        foreach ($summary as $row) {
            if ($row['agent_name'] === 'Agent A') {
                $foundA = true;
                $this->assertEquals(1, $row['total_pnrs']);
                $this->assertEquals(10, $row['total_pax']);
                $this->assertEquals(1000000, $row['total_revenue']);
            }
        }
        $this->assertTrue($foundA);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM movements");
    }
}
