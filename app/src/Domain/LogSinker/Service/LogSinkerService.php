<?php

namespace App\Domain\LogSinker\Service;

use App\Domain\LogEntry\LogEntryEntity;
use App\Domain\LogSinker\Parser\LogParserFactoryInterface;
use App\Domain\LogSinker\Reader\ReaderInterface;
use App\Domain\LogSinker\Repository\LogEntryRepositoryInterface;

final readonly class LogSinkerService implements LogSinkerServiceInterface
{
    public function __construct(
        private LogParserFactoryInterface $logParserFactory,
        private LogEntryRepositoryInterface $logEntryRepository
    ) {}
    public function importFrom(ReaderInterface $reader, int $batchSize): void
    {
        $parser = $this->logParserFactory->create($reader);
        $batch = [];

        foreach ($parser->parse() as $entry) {
            $batch[] = new LogEntryEntity(
                $entry->serviceName,
                $entry->timestamp,
                $entry->requestLine,
                $entry->statusCode
            );

            if (count($batch) >= $batchSize) {
                $this->logEntryRepository->save(...$batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            $this->logEntryRepository->save(...$batch);
        }
    }
}
