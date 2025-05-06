<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Request\LogFilterQueryParamsDto;
use App\Dto\Response\CountItemDto;
use App\LogSinker\Repository\LogEntryRepositoryInterface;

final readonly class LogQueryService implements LogQueryServiceInterface
{
    public function __construct(
        private LogEntryRepositoryInterface $logEntryRepository,
    ) {}

    public function count(LogFilterQueryParamsDto $queryParams): CountItemDto
    {
        $result = $this->logEntryRepository->countFilteredLogEntries(
            $queryParams->serviceNames,
            $queryParams->statusCode,
            $queryParams->startDate,
            $queryParams->endDate
        );

        return new CountItemDto($result);
    }
}
