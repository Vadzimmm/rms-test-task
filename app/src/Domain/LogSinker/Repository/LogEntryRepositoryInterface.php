<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Repository;

use App\Domain\LogSinker\LogEntry;

interface LogEntryRepositoryInterface
{
    /**
     * @param null|array<string> $serviceNames
     */
    public function countFilteredLogEntries(
        ?array $serviceNames = null,
        ?int $statusCode = null,
        ?\DateTimeImmutable $startDate = null,
        ?\DateTimeImmutable $endDate = null,
    ): int;

    public function save(LogEntry ...$logEntries): void;
}
