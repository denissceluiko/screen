<?php

namespace App\Services\Scanners;

use App\Contracts\FileScanner;
use App\DTOs\ScanResult;

class ClamAvScanner implements FileScanner
{
    /**
     * Scan the file using ClamAV.
     *
     * TODO: Add a ClamAV service to docker-compose.yml (image: clamav/clamav)
     * and expose it on port 3310. Then use the clamd socket or TCP stream to
     * submit the file for scanning.
     *
     * Example using Symfony Process (clamdscan CLI):
     *   $process = new Process(['clamdscan', '--no-summary', $absolutePath]);
     *   $process->run();
     *   if ($process->getExitCode() === 1) {
     *       preg_match('/^.+: (.+) FOUND/m', $process->getOutput(), $matches);
     *       return ScanResult::fail('clamav', $matches[1] ?? 'unknown');
     *   }
     */
    public function scan(string $absolutePath): ScanResult
    {
        // TODO: implement
        return ScanResult::pass('clamav');
    }
}
