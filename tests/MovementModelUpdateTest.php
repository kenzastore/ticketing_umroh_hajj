<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../includes/db_connect.php';

class MovementModelUpdateTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->pdo->exec("DELETE FROM movements");
    }

    public function testCreateAndUpdateWithCategory()
    {
        $data = [
            'pnr' => 'TEST01',
            'category' => 'HAJJI',
            'agent_name' => 'Test Hajji Agent',
            'created_date' => date('Y-m-d')
        ];

        $id = Movement::create($data);
        $this->assertNotFalse($id);

        $movement = Movement::readById($id);
        $this->assertEquals('HAJJI', $movement['category']);

        $updated = Movement::update($id, ['pnr' => 'TEST01-UPD']);
        $this->assertTrue($updated);

        $movement = Movement::readById($id);
        $this->assertEquals('TEST01-UPD', $movement['pnr']);
    }

    public function testReadAllWithCategoryFilter()
    {
        Movement::create(['pnr' => 'U1', 'category' => 'UMRAH', 'created_date' => date('Y-m-d')]);
        Movement::create(['pnr' => 'H1', 'category' => 'HAJJI', 'created_date' => date('Y-m-d')]);

        $umrah = Movement::readAll('UMRAH');
        $this->assertCount(1, $umrah);
        $this->assertEquals('U1', $umrah[0]['pnr']);

        $hajji = Movement::readAll('HAJJI');
        $this->assertCount(1, $hajji);
        $this->assertEquals('H1', $hajji[0]['pnr']);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM movements");
    }
}
