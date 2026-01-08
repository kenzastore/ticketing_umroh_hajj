<?php

use PHPUnit\Framework\TestCase;

class UatExportReadinessTest extends TestCase
{
    public function testAllScenariosFollowTemplate()
    {
        $scenariosDir = __DIR__ . '/../conductor/uat/scenarios/';
        $files = glob($scenariosDir . '*.md');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $filename = basename($file);
            
            $this->assertStringContainsString('# UAT Scenario:', $content, "File $filename is missing H1 header.");
            $this->assertStringContainsString('## Scenario ID:', $content, "File $filename is missing Scenario ID.");
            $this->assertStringContainsString('## Test Steps', $content, "File $filename is missing Test Steps.");
            $this->assertStringContainsString('## Expected Result', $content, "File $filename is missing Expected Result.");
        }
    }

    public function testMasterPlanLinksAreCorrect()
    {
        $planPath = __DIR__ . '/../conductor/uat/uat_master_plan.md';
        $content = file_get_contents($planPath);
        
        preg_match_all('/\(\.\/scenarios\/(.*?\.md)\)/', $content, $matches);
        
        foreach ($matches[1] as $scenarioFile) {
            $path = __DIR__ . '/../conductor/uat/scenarios/' . $scenarioFile;
            $this->assertFileExists($path, "Link to $scenarioFile in Master Plan is broken.");
        }
    }
}
