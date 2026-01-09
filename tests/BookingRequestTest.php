<?php
namespace Tests;

use PDO;

class BookingRequestTest extends TestCase
{
    /** 
     * @test 
     * @covers \BookingRequest::create
     */
    public function it_logs_audit_trail_on_creation()
    {
        $userId = $this->createTestUser('br_creator');
        
        $header = [
            'corporate_name' => 'BR Corp',
            'agent_name' => 'BR Agent',
            'group_size' => 20
        ];
        $legs = [
            ['flight_no' => 'TR123', 'sector' => 'SIN-JED']
        ];

        $requestId = \BookingRequest::create($header, $legs, $userId);
        $this->assertNotFalse($requestId);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'booking_request' AND entity_id = ? AND action = 'CREATE'");
        $stmt->execute([$requestId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        $newValue = json_decode($log['new_value'], true);
        $this->assertEquals('BR Corp', $newValue['corporate_name']);
        $this->assertNotEmpty($newValue['legs']);
    }

    /** 
     * @test 
     * @covers \BookingRequest::update
     */
    public function it_logs_audit_trail_on_update()
    {
        $userId = $this->createTestUser('br_updater');
        $requestId = \BookingRequest::create(['corporate_name' => 'Original'], [], $userId);

        $result = \BookingRequest::update($requestId, ['corporate_name' => 'Updated'], [], $userId);
        $this->assertTrue($result);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'booking_request' AND entity_id = ? AND action = 'UPDATE'");
        $stmt->execute([$requestId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        $oldValue = json_decode($log['old_value'], true);
        $newValue = json_decode($log['new_value'], true);
        
        $this->assertEquals('Original', $oldValue['corporate_name']);
        $this->assertEquals('Updated', $newValue['corporate_name']);
    }

    /** 
     * @test 
     * @covers \BookingRequest::delete
     */
    public function it_logs_audit_trail_on_deletion()
    {
        $userId = $this->createTestUser('br_deleter');
        $requestId = \BookingRequest::create(['corporate_name' => 'To Delete'], [], $userId);

        $result = \BookingRequest::delete($requestId, $userId);
        $this->assertTrue($result);

        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'booking_request' AND entity_id = ? AND action = 'DELETE'");
        $stmt->execute([$requestId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        $oldValue = json_decode($log['old_value'], true);
        $this->assertEquals('To Delete', $oldValue['corporate_name']);
    }

    /**
     * @test
     */
    public function it_logs_audit_trail_on_conversion_simulation()
    {
        $userId = $this->createTestUser('br_converter');
        $requestId = \BookingRequest::create(['request_no' => 100, 'pnr' => 'TESTPNR'], [], $userId);

        // Simulation of convert_request_process.php logic
        // 1. Create Movement (Mocked or simple insert)
        $movementId = 999; // Dummy ID for simulation

        // 2. Audit Log
        \AuditLog::log($userId, 'CONVERTED_TO_MOVEMENT', 'booking_request', $requestId, null, ['movement_id' => $movementId, 'pnr' => 'TESTPNR']);

        // 3. Verify Log
        $stmt = self::$pdo->prepare("SELECT * FROM audit_logs WHERE entity_type = 'booking_request' AND entity_id = ? AND action = 'CONVERTED_TO_MOVEMENT'");
        $stmt->execute([$requestId]);
        $log = $stmt->fetch();

        $this->assertNotFalse($log);
        $newVal = json_decode($log['new_value'], true);
        $this->assertEquals($movementId, $newVal['movement_id']);
        $this->assertEquals('TESTPNR', $newVal['pnr']);
    }
}
