<?php

namespace App\DTOs;

readonly class DockerExecResult
{
    public function __construct(
        public int $exitCode,
        public string $output,
    ) {}

    public function succeeded(): bool
    {
        return $this->exitCode === 0;
    }
}
