<?php

declare(strict_types=1);

namespace App\LogSinker\Parser;

use App\LogSinker\Stream\LineStreamInterface;
use Psr\Log\LoggerInterface;

final readonly class LogParser implements ParserInterface
{
    public function __construct(
        private ParserStrategyInterface $parserStrategy,
        private LoggerInterface $logger,
    ) {}

    public function parseFrom(LineStreamInterface $lineStream): iterable
    {
        foreach ($lineStream->read() as $logRecord) {
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
