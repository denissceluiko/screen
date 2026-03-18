<?php

namespace Tests\Feature;

use App\DTOs\ScanResult;
use App\Services\DockerService;
use App\Services\Scanners\YaraScanner;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Integration tests for YaraScanner.
 *
 * These tests require the YARA container to be running (`vendor/bin/sail up -d yara`)
 * and storage/yara-rules/index.yar to exist. They write temporary files to the
 * real slides disk so they are accessible to the shared container volume.
 */
class YaraScannerTest extends TestCase
{
    private YaraScanner $scanner;

    /** @var list<string> absolute paths of temp files to clean up after each test */
    private array $tempFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->scanner = new YaraScanner(new DockerService(
            socketPath: config('services.yara.socket', '/var/run/docker.sock'),
        ));
    }

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }

        parent::tearDown();
    }

    public function test_clean_jpeg_passes(): void
    {
        $path = $this->writeTempFile('clean_'.Str::random(8).'.jpg', $this->minimalJpeg());

        $result = $this->scanner->scan($path);

        $this->assertInstanceOf(ScanResult::class, $result);
        $this->assertTrue($result->passed);
        $this->assertNull($result->threat);
    }

    public function test_webshell_content_is_detected(): void
    {
        // Contains eval($_POST[...]) which matches webshell_php_generic_eval
        $payload = '<?php eval($_POST[\'cmd\']); ?>';
        $path = $this->writeTempFile('webshell_'.Str::random(8).'.php', $payload);

        $result = $this->scanner->scan($path);

        $this->assertFalse($result->passed);
        $this->assertNotNull($result->threat);
        $this->assertStringContainsString('webshell', strtolower($result->threat));
    }

    public function test_result_includes_matched_rule_name(): void
    {
        $payload = '<?php eval($_POST[\'cmd\']); ?>';
        $path = $this->writeTempFile('rule_name_'.Str::random(8).'.php', $payload);

        $result = $this->scanner->scan($path);

        $this->assertFalse($result->passed);
        $this->assertNotEmpty($result->details);
        // YARA output format: "{RuleName} {filepath}" — rule name should be in details
        $this->assertStringContainsString($result->threat, $result->details);
    }

    public function test_skips_scan_when_no_index_file(): void
    {
        $rulesFile = storage_path('yara-rules/index.yar');
        $backup = $rulesFile.'.bak';

        rename($rulesFile, $backup);

        try {
            $path = $this->writeTempFile('skip_'.Str::random(8).'.php', '<?php eval($_POST[\'cmd\']); ?>');
            $result = $this->scanner->scan($path);

            $this->assertTrue($result->passed);
        } finally {
            rename($backup, $rulesFile);
        }
    }

    private function writeTempFile(string $filename, string $contents): string
    {
        $path = storage_path('app/slides/'.$filename);
        file_put_contents($path, $contents);
        $this->tempFiles[] = $path;

        return $path;
    }

    private function minimalJpeg(): string
    {
        $image = imagecreatetruecolor(1, 1);
        ob_start();
        imagejpeg($image);

        return ob_get_clean();
    }
}
