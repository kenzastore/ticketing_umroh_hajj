<?php
use PHPUnit\Framework\TestCase;

class UatDistributionTest extends Tests\TestCase
{
    public function testWorkflowStageDistribution()
    {
        // Execute seeder
        exec("php " . __DIR__ . '/../database/seed_uat_system.php');

        // 1. New Requests (~25)
        $stmt = self::$pdo->query("SELECT COUNT(*) FROM booking_requests WHERE is_converted = 0");
        $newCount = $stmt->fetchColumn();
        $this->assertGreaterThanOrEqual(20, $newCount, "Should have enough new requests");

        // 2. Active Movements (approx 25-50 based on logic)
        $stmtMv = self::$pdo->query("SELECT COUNT(*) FROM movements WHERE ticketing_done = 0");
        $activeCount = $stmtMv->fetchColumn();
        $this->assertGreaterThanOrEqual(20, $activeCount, "Should have enough active movements");

        // 3. Finalized Records (ticketing done)
        $stmtDone = self::$pdo->query("SELECT COUNT(*) FROM movements WHERE ticketing_done = 1");
        $doneCount = $stmtDone->fetchColumn();
        $this->assertGreaterThanOrEqual(20, $doneCount, "Should have enough finalized records");

        // 4. Urgent Scenarios (Deadline < now or near)
        $stmtUrgent = self::$pdo->query("SELECT COUNT(*) FROM movements WHERE ticketing_deadline <= DATE_ADD(CURDATE(), INTERVAL 1 DAY)");
        $urgentCount = $stmtUrgent->fetchColumn();
        $this->assertGreaterThanOrEqual(10, $urgentCount, "Should have urgent/overdue scenarios");
    }

    public function testAuditLogChronology()
    {
        // Check log distribution
        $stmt = self::$pdo->query("SELECT entity_type, COUNT(*) as cnt FROM audit_logs GROUP BY entity_type");
        $logs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // print_r($logs); // Debug
        
        $this->assertArrayHasKey('booking_request', $logs, "Should have request logs. Actual keys: " . implode(', ', array_keys($logs)));
        $this->assertArrayHasKey('movement', $logs, "Should have movement logs");
        $this->assertGreaterThanOrEqual(100, $logs['booking_request'], "At least 100 request logs expected");
    }
}
