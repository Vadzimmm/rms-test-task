<?php

declare(strict_types=1);

namespace App\LogSinker;

final readonly class LogEntry
{
    public function __construct(
        public string $serviceName,
        public \DateTimeImmutable $timestamp,
        public string $requestLine,
        public int $statusCode,
    ) {}
}
