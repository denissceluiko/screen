<?php

namespace App\Services\Scanners;

use App\Contracts\FileScanner;
use App\DTOs\ScanResult;
use App\Services\DockerService;
use Illuminate\Support\Facades\Log;

class YaraScanner implements FileScanner
{
    public function __construct(private readonly DockerService $docker) {}

    /**
     * Scan the file using YARA rules running in the dedicated yara container.
     *
     * The scanner requires storage/yara-rules/index.yar to exist. Create it
     * with `include` directives pointing to your rule sets, for example:
     *
     *   include "neo23x0/thor-yara.yar"
     *   include "yara-forge/core.yar"
     *
     * If the index file does not exist the scan is skipped and the file is
     * treated as clean so that uploads are not blocked before rules are loaded.
     *
     * YARA always exits with code 0 on success regardless of whether rules
     * matched. Detection is based on output: any output means a rule matched.
     * A non-zero exit code indicates a YARA error (bad rules, unreadable file).
     */
    public function scan(string $absolutePath): ScanResult
    {
        $rulesFile = storage_path('yara-rules/index.yar');

        if (! file_exists($rulesFile)) {
            Log::warning('YaraScanner: no index.yar found in storage/yara-rules/, skipping scan.', [
                'file' => $absolutePath,
            ]);

            return ScanResult::pass('yara');
        }

        $container = config('services.yara.container');
        $containerRulesFile = config('services.yara.rules_file');

        $result = $this->docker->exec($container, [
            'yara',
            '--no-warnings',
            $containerRulesFile,
            $absolutePath,
        ]);

        if ($result->exitCode !== 0) {
            Log::error('YaraScanner: YARA exited with an error.', [
                'exit_code' => $result->exitCode,
                'output' => $result->output,
                'file' => $absolutePath,
            ]);

            return ScanResult::pass('yara');
        }

        $output = trim($result->output);

        if ($output === '') {
            return ScanResult::pass('yara');
        }

        $threat = $this->parseThreatName($output) ?? 'unknown';

        return ScanResult::fail('yara', $threat, $output);
    }

    /**
     * Extract the matched rule name from YARA's output line.
     * YARA output format: "{RuleName} {filepath}"
     */
    private function parseThreatName(string $output): ?string
    {
        $line = trim(explode("\n", $output)[0]);

        if ($line === '') {
            return null;
        }

        return explode(' ', $line)[0];
    }
}
