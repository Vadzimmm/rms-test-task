<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Parser;

use App\Domain\LogSinker\Reader\ReaderInterface;
use Psr\Log\LoggerInterface;

final readonly class LogParser implements ParserInterface
{
    public function __construct(
        private ParserStrategyInterface $parserStrategy,
        private LoggerInterface $logger,
    ) {}

    public function parseFrom(ReaderInterface $reader): iterable
    {
        foreach ($reader->read() as $logRecord) {
            $entry = $this->parserStrategy->parseEntry($logRecord);

            if (null === $entry) {
                $this->logger->debug(
                    'Unable to parse log line: null returned from parser strategy',
                    [
                        'line' => $logRecord,
                        'strategy' => get_class($this->parserStrategy),
                    ]
                );
            } else {
                yield $entry;
            }
        }
    }
}
