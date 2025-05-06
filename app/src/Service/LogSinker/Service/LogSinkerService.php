<?php

declare(strict_types=1);

namespace App\Service\LogSinker\Service;

use App\Service\LogSinker\Exception\InvalidBatchSizeException;
use App\Service\LogSinker\Parser\ParserInterface;
use App\Service\LogSinker\Repository\LogEntryRepositoryInterface;
use App\Service\LogSinker\Stream\LineStreamInterface;
use Psr\Log\LoggerInterface;

final readonly class LogSinkerService implements LogSinkerServiceInterface
{
    public function __construct(
        private LogEntryRepositoryInterface $logEntryRepository,
        private ParserInterface $parser,
        private LoggerInterface $logger,
    ) {}

    public function importFrom(LineStreamInterface $lineStream, int $batchSize): void
    {
        if ($batchSize <= 0) {
            throw new InvalidBatchSizeException($batchSize);
        }

        $batch = [];

        foreach ($this->parser->parseFrom($lineStream) as $entry) {
            $this->logger->info(sprintf(
                'Parsed: %s [%d] @ timestamp: %s',
                $entry->serviceName,
                $entry->statusCode,
                $entry->timestamp->getTimestamp()
            ));

            $batch[] = $entry;

            if (count($batch) >= $batchSize) {
                $this->logEntryRepository->save(...$batch);
                $this->logger->info(sprintf('Saved batch of %d entries', count($batch)));
                $batch = [];
            }
        }

        if (!empty($batch)) {
            $this->logEntryRepository->save(...$batch);
            $this->logger->info(sprintf('Saved final batch of %d entries', count($batch)));
        }
    }
}
