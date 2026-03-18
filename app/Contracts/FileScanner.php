<?php

namespace App\Contracts;

use App\DTOs\ScanResult;

interface FileScanner
{
    /**
     * Scan the file at the given absolute path.
     */
    public function scan(string $absolutePath): ScanResult;
}
