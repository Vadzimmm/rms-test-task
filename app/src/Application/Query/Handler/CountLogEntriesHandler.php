<?php

declare(strict_types=1);

namespace App\Application\Query\Handler;

use App\Application\Query\CountLogEntriesQuery;
use App\Domain\LogSinker\Repository\LogEntryRepositoryInterface;

final readonly class CountLogEntriesHandler implements QueryHandlerInterface
{
    public function __construct(
        private LogEntryRepositoryInterface $logEntryRepository
    ) {}

    public function __invoke(CountLogEntriesQuery $countLogEntriesQuery): int
    {
        $parameters = $countLogEntriesQuery->logFilterQueryParamsDto;

        return $this->logEntryRepository->countFilteredLogEntries(
            $parameters->serviceNames,
            $parameters->statusCode,
            $parameters->startDate,
            $parameters->endDate
        );
    }
}
