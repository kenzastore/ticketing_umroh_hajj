<?php

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class ManualCrossLinkTest extends TestCase
{
    private $manualDir;

    protected function setUp(): void
    {
        $this->manualDir = __DIR__ . '/../docs/manual/';
    }

    public function testAllInternalLinksAreValid()
    {
        $indexFile = $this->manualDir . 'index.md';
        $this->assertFileExists($indexFile);

        $files = glob($this->manualDir . '*.md');
        $allContent = '';
        foreach ($files as $file) {
            $allContent .= file_get_contents($file);
        }

        // Regex to find Markdown links like [text](./link.md)
        preg_match_all('/\[[^\]]+\]\(\.\/([^\)]+\.md)\)/', $allContent, $matches);

        $failedLinks = [];
        foreach ($matches[1] as $link) {
            $targetPath = $this->manualDir . $link;
            if (!file_exists($targetPath)) {
                $failedLinks[] = $link;
            }
        }

        $this->assertEmpty($failedLinks, 'Some internal Markdown links are broken: ' . implode(', ', $failedLinks));
    }
}
