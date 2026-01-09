<?php
namespace Tests;

/**
 * @covers \ManualToC (not yet existing as a class)
 */
class ManualToCTest extends TestCase
{
    public function testToCGenerationLogic()
    {
        $manualDir = __DIR__ . '/../docs/manual/';
        $files = glob($manualDir . '*.md');
        $this->assertNotEmpty($files);
        
        $pages = [];
        foreach ($files as $file) {
            $pages[] = basename($file, '.md');
        }
        
        $this->assertContains('index', $pages);
        $this->assertContains('stage1_demand_intake', $pages);
    }
}
