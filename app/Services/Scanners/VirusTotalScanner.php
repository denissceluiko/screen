<?php

namespace App\Services\Scanners;

use App\Contracts\FileScanner;
use App\DTOs\ScanResult;

class VirusTotalScanner implements FileScanner
{
    /**
     * Scan the file via the VirusTotal API.
     *
     * TODO: Set VIRUSTOTAL_API_KEY in .env and config/services.php.
     * Upload the file (or its SHA-256 hash) to the VirusTotal Files API and
     * poll the analysis endpoint until a verdict is returned.
     * API docs: https://docs.virustotal.com/reference/files
     *
     * Note: files leave your server when using this scanner. Ensure your
     * privacy policy and user agreements reflect this before enabling it.
     */
    public function scan(string $absolutePath): ScanResult
    {
        // TODO: implement
        return ScanResult::pass('virustotal');
    }
}
