<?php
namespace Tests;

use App\Models\BookingRequest;
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
}
