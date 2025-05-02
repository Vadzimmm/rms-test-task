<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Parser;

use App\Domain\LogSinker\Reader\ReaderInterface;

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
