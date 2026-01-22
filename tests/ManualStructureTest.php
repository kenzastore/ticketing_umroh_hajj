<?php

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class ManualStructureTest extends TestCase
{
    public function testManualDirectoryExists()
    {
        $this->assertDirectoryExists(__DIR__ . '/../docs/manual');
    }

    public function testManualIndexExists()
    {
        $this->assertFileExists(__DIR__ . '/../docs/manual/index.md');
    }

    public function testManualChapterTemplateExists()
    {
        $this->assertFileExists(__DIR__ . '/../docs/manual/template.md');
        $content = file_get_contents(__DIR__ . '/../docs/manual/template.md');
        $this->assertStringContainsString('# Judul Tahap/Modul', $content);
        $this->assertStringContainsString('## Pendahuluan', $content);
        $this->assertStringContainsString('## Langkah-langkah', $content);
        $this->assertStringContainsString('## Peran yang Bertanggung Jawab', $content);
    }
}
