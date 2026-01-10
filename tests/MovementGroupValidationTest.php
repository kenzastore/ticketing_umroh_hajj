<?php
namespace Tests;

class MovementGroupValidationTest extends TestCase
{
    /** 
     * @test 
     * @coversNothing
     */
    public function testGetGroupPassengerSum()
    {
        $tourCode = 'GRP_SUM_TEST';
        $requestId = 999;

        // Insert split PNRs for this group
        self::$pdo->prepare("INSERT INTO movements (tour_code, movement_no, passenger_count, pnr) VALUES (?, ?, ?, ?)")
            ->execute([$tourCode, $requestId, 25, 'PNR-A']);
        self::$pdo->prepare("INSERT INTO movements (tour_code, movement_no, passenger_count, pnr) VALUES (?, ?, ?, ?)")
            ->execute([$tourCode, $requestId, 15, 'PNR-B']);
            
        // Insert a PNR for a DIFFERENT group
        self::$pdo->prepare("INSERT INTO movements (tour_code, movement_no, passenger_count, pnr) VALUES (?, ?, ?, ?)")
            ->execute(['OTHER_GRP', $requestId, 10, 'PNR-C']);

        $sum = \Movement::getGroupPassengerSum($tourCode, $requestId);
        $this->assertEquals(40, $sum);
    }

    /** 
     * @test 
     * @coversNothing
     */
    public function testGetGroupTcp()
    {
        $tourCode = 'GRP_TCP_TEST';
        $requestId = 888;
        $expectedTcp = 50;

        // TCP is stored in the movement rows (replicated for now, as per standard practice when no single header exists)
        self::$pdo->prepare("INSERT INTO movements (tour_code, movement_no, tcp, pnr) VALUES (?, ?, ?, ?)")
            ->execute([$tourCode, $requestId, $expectedTcp, 'PNR-X']);

        $tcp = \Movement::getGroupTcp($tourCode, $requestId);
        $this->assertEquals($expectedTcp, $tcp);
    }

    /** 
     * @test 
     * @coversNothing
     */
    public function testGetGroupTcpFallback()
    {
        // 1. Create a booking request with TCP
        $expectedTcp = 60;
        self::$pdo->prepare("INSERT INTO booking_requests (tcp, request_no) VALUES (?, ?)")
            ->execute([$expectedTcp, 777]);
        $brId = self::$pdo->lastInsertId();

        $tourCode = 'FALLBACK_TEST';
        $movementNo = 777;

        // 2. Create a movement linked to it but WITHOUT its own TCP
        self::$pdo->prepare("INSERT INTO movements (tour_code, movement_no, booking_request_id, pnr) VALUES (?, ?, ?, ?)")
            ->execute([$tourCode, $movementNo, $brId, 'PNR-FALLBACK']);

        $tcp = \Movement::getGroupTcp($tourCode, $movementNo);
        $this->assertEquals($expectedTcp, $tcp);
    }
}
