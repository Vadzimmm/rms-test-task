<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\Request\LogFilterQueryParamsDto;
use App\Entity\LogEntryEntity;

interface LogEntryRepositoryInterface
{
    public function countFilteredLogEntries(LogFilterQueryParamsDto $filtersDto): int;

    public function save(LogEntryEntity $logEntryEntity): void;
}
