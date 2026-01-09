<?php
namespace Tests;

require_once __DIR__ . '/../includes/Parsedown.php';

/**
 * @covers \Parsedown
 */
class ManualRenderingTest extends TestCase
{
    public function testParsedownRendersMarkdown()
    {
        $parsedown = new \Parsedown();
        $markdown = "# Hello World\n\nThis is a **test**.";
        $html = $parsedown->text($markdown);
        
        $this->assertStringContainsString('<h1>Hello World</h1>', $html);
        $this->assertStringContainsString('<strong>test</strong>', $html);
    }

    public function testManualFilesAreReadable()
    {
        $this->assertFileExists(__DIR__ . '/../docs/manual/index.md');
        $this->assertIsReadable(__DIR__ . '/../docs/manual/index.md');
    }
}
