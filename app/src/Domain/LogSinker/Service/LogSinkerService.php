<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Service;

use App\Domain\LogSinker\Exception\InvalidBatchSizeException;
use App\Domain\LogSinker\Parser\ParserInterface;
use App\Domain\LogSinker\Repository\LogEntryRepositoryInterface;
use App\Domain\LogSinker\Stream\LineStreamInterface;

final readonly class LogSinkerService implements LogSinkerServiceInterface
{
    public function __construct(
        private LogEntryRepositoryInterface $logEntryRepository,
        private ParserInterface $parser,
    ) {}

    public function importFrom(LineStreamInterface $lineStream, int $batchSize): void
    {
        if ($batchSize <= 0) {
            throw new InvalidBatchSizeException($batchSize);
        }

        $batch = [];

        foreach ($this->parser->parseFrom($lineStream) as $entry) {
            $batch[] = $entry;

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
