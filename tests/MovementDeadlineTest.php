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
        $this->pdo->exec("DELETE FROM movements");
    }

    public function testGetTicketingDeadlines()
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $in4Days = date('Y-m-d', strtotime('+4 days'));

        // Past due
        $this->pdo->exec("INSERT INTO movements (pnr, agent_name, ticketing_deadline, ticketing_done) VALUES ('PNR-PAST', 'Agent A', '$yesterday', 0)");
        // Due tomorrow
        $this->pdo->exec("INSERT INTO movements (pnr, agent_name, ticketing_deadline, ticketing_done) VALUES ('PNR-TMRW', 'Agent B', '$tomorrow', 0)");
        // Due today
        $this->pdo->exec("INSERT INTO movements (pnr, agent_name, ticketing_deadline, ticketing_done) VALUES ('PNR-TODAY', 'Agent C', '$today', 0)");
        // Due in 4 days (excluded)
        $this->pdo->exec("INSERT INTO movements (pnr, agent_name, ticketing_deadline, ticketing_done) VALUES ('PNR-FAR', 'Agent D', '$in4Days', 0)");
        // Done (excluded)
        $this->pdo->exec("INSERT INTO movements (pnr, agent_name, ticketing_deadline, ticketing_done) VALUES ('PNR-DONE', 'Agent E', '$tomorrow', 1)");

        $results = Movement::getDeadlinesByCategory('ticketing', 3);

        $this->assertCount(3, $results);
        // Check sorting: yesterday, today, tomorrow
        $this->assertEquals('PNR-PAST', $results[0]['pnr']);
        $this->assertEquals('PNR-TODAY', $results[1]['pnr']);
        $this->assertEquals('PNR-TMRW', $results[2]['pnr']);
    }

    public function testGetDP1Deadlines()
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        
        // DP1 Airlines due
        $this->pdo->exec("INSERT INTO movements (pnr, deposit1_airlines_date, dp1_status) VALUES ('DP1-AIR', '$tomorrow', 'PENDING')");
        // DP1 EEMW due
        $this->pdo->exec("INSERT INTO movements (pnr, deposit1_eemw_date, dp1_status) VALUES ('DP1-EEMW', '$tomorrow', 'PENDING')");
        // DP1 Paid (excluded)
        $this->pdo->exec("INSERT INTO movements (pnr, deposit1_airlines_date, dp1_status) VALUES ('DP1-PAID', '$tomorrow', 'PAID')");

        $results = Movement::getDeadlinesByCategory('dp1', 3);

        $this->assertCount(2, $results);
    }

    public function testGetDP2Deadlines()
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        
        // DP2 Airlines due
        $this->pdo->exec("INSERT INTO movements (pnr, deposit2_airlines_date, dp2_status) VALUES ('DP2-AIR', '$tomorrow', 'PENDING')");
        // DP2 Paid (excluded)
        $this->pdo->exec("INSERT INTO movements (pnr, deposit2_airlines_date, dp2_status) VALUES ('DP2-PAID', '$tomorrow', 'PAID')");

        $results = Movement::getDeadlinesByCategory('dp2', 3);

        $this->assertCount(1, $results);
    }

    public function testGetFPDeadlines()
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        
        // FP Airlines due
        $this->pdo->exec("INSERT INTO movements (pnr, fullpay_airlines_date, fp_status) VALUES ('FP-AIR', '$tomorrow', 'PENDING')");
        // FP Paid (excluded)
        $this->pdo->exec("INSERT INTO movements (pnr, fullpay_airlines_date, fp_status) VALUES ('FP-PAID', '$tomorrow', 'PAID')");

        $results = Movement::getDeadlinesByCategory('fp', 3);

        $this->assertCount(1, $results);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM movements");
    }
}