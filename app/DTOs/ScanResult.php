<?php

namespace App\DTOs;

readonly class ScanResult
{
    public function __construct(
        public bool $passed,
        public string $scanner,
        public ?string $threat = null,
        public ?string $details = null,
    ) {}

    public static function pass(string $scanner): self
    {
        return new self(passed: true, scanner: $scanner);
    }

    public static function fail(string $scanner, string $threat, ?string $details = null): self
    {
        return new self(passed: false, scanner: $scanner, threat: $threat, details: $details);
    }
}
