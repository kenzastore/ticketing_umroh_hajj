<?php
namespace Tests;

class MovementTcpSchemaTest extends TestCase
{
    /** 
     * @test 
     * @coversNothing
     */
    public function testMovementsTableHasNewColumns()
    {
        $stmt = self::$pdo->query("DESCRIBE movements");
        $columns = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $this->assertContains('booking_request_id', $columns, "Column 'booking_request_id' is missing from 'movements' table.");
        $this->assertContains('tcp', $columns, "Column 'tcp' is missing from 'movements' table.");
    }

    /** 
     * @test 
     * @coversNothing
     */
    public function testMovementsTableHasIndexes()
    {
        $stmt = self::$pdo->query("SHOW INDEX FROM movements");
        $indexes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $indexNames = array_column($indexes, 'Key_name');
        
        $this->assertContains('idx_mv_booking_request', $indexNames, "Index 'idx_mv_booking_request' is missing from 'movements' table.");
    }
}
