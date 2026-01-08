<?php

use PHPUnit\Framework\TestCase;

class UatDirectoryStructureTest extends TestCase
{
    public function testUatDirectoriesExist()
    {
        $baseDir = __DIR__ . '/../conductor/uat';
        
        $this->assertDirectoryExists($baseDir, "The 'conductor/uat' directory should exist.");
        $this->assertDirectoryExists($baseDir . '/scenarios', "The 'conductor/uat/scenarios' directory should exist.");
        $this->assertDirectoryExists($baseDir . '/templates', "The 'conductor/uat/templates' directory should exist.");
        $this->assertDirectoryExists($baseDir . '/results', "The 'conductor/uat/results' directory should exist.");
    }
}
