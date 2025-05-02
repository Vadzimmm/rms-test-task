<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\SaveLogCommand;
use App\Domain\LogEntry\LogEntryEntity;
use App\Domain\LogSinker\Parser\LogParserFactoryInterface;
use App\Infrastructure\Repository\LogEntryRepositoryInterface;

final readonly class SaveLogCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private LogEntryRepositoryInterface $logEntryRepository,
        private LogParserFactoryInterface $logParserFactory,
    ) {}

    public function __invoke(SaveLogCommand $saveLogCommand): void
    {
        $logParser = $this->logParserFactory->create($saveLogCommand->filePath);

        $batchSize = $saveLogCommand->batchSize;
        $batch = [];

        foreach ($logParser->parse() as $entry) {
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
