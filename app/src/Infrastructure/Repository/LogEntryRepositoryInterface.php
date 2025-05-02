<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\LogEntry\LogEntryEntity;
use App\Shared\DTO\Request\LogFilterQueryParamsDto;

interface LogEntryRepositoryInterface
{
    public function countFilteredLogEntries(LogFilterQueryParamsDto $filtersDto): int;

    public function save(LogEntryEntity $logEntryEntity): void;
}
