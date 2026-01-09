<?php
namespace Tests;

/**
 * @covers \ManualLinkRewriter (not yet existing as a class, but we will test the logic)
 */
class ManualLinkRewritingTest extends TestCase
{
    public function testLinkRewritingLogic()
    {
        $html = '<p>Go to <a href="./stage1_demand_intake.md">Stage 1</a>.</p>';
        
        // Regex logic we plan to use:
        // href="\./([^"]+)\.md" -> href="manual.php?page=$1"
        $rewrittenHtml = preg_replace('/href="\.\/([^"]+)\.md"/', 'href="manual.php?page=$1"', $html);
        
        $this->assertEquals('<p>Go to <a href="manual.php?page=stage1_demand_intake">Stage 1</a>.</p>', $rewrittenHtml);
    }

    public function testLinkRewritingHandlesSubdirectoriesOrNoDot()
    {
        $html = '<p>Link <a href="stage2_operational_execution.md">Stage 2</a></p>';
        $rewrittenHtml = preg_replace('/href="(?:\.\/)?([^"]+)\.md"/', 'href="manual.php?page=$1"', $html);
        $this->assertEquals('<p>Link <a href="manual.php?page=stage2_operational_execution">Stage 2</a></p>', $rewrittenHtml);
    }
}
