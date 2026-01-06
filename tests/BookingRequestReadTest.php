<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/models/BookingRequest.php';
require_once __DIR__ . '/../includes/db_connect.php';

class BookingRequestReadTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        global $pdo;
        $this->pdo = $pdo;
        
        // Clean up
        $this->pdo->exec("DELETE FROM booking_requests");
        $this->pdo->exec("DELETE FROM booking_request_legs");
    }

    public function testReadAllReturnsLegs()
    {
        // 1. Create a request
        $header = [
            'request_no' => 1001,
            'agent_name' => 'Test Agent',
            'group_size' => 10
        ];
        
        $legs = [
            [
                'leg_no' => 1,
                'flight_date' => '2026-05-01',
                'flight_no' => 'TR100',
                'sector' => 'SUB-SIN'
            ],
            [
                'leg_no' => 2,
                'flight_date' => '2026-05-01',
                'flight_no' => 'TR101',
                'sector' => 'SIN-JED'
            ]
        ];

        $id = BookingRequest::create($header, $legs);
        $this->assertNotFalse($id, "Booking request should be created");

        // 2. Read all
        $all = BookingRequest::readAll();
        
        // 3. Verify legs are attached
        $this->assertNotEmpty($all);
        $found = false;
        foreach ($all as $req) {
            if ($req['id'] == $id) {
                $found = true;
                $this->assertArrayHasKey('legs', $req, "Result should contain 'legs' key");
                $this->assertCount(2, $req['legs'], "Should have 2 legs");
                
                // Verify leg order or content
                $this->assertEquals(1, $req['legs'][0]['leg_no']);
                $this->assertEquals('TR100', $req['legs'][0]['flight_no']);
                break;
            }
        }
        
        $this->assertTrue($found, "Created request not found in readAll()");
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM booking_requests");
    }
}
