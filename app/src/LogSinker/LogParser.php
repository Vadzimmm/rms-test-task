<?php

declare(strict_types=1);

namespace App\LogSinker;

readonly class LogParser implements ParserInterface
{
    public function __construct(
        private ReaderInterface $reader,
        private ParserStrategyInterface $parserStrategy
    ) {}

    public function parse(): iterable
    {
        foreach ($this->reader->read() as $logRecord) {
            $entry = $this->parserStrategy->parseEntry($logRecord);

            if (null !== $entry) {
                yield $entry;
            }
        }
    }
}
