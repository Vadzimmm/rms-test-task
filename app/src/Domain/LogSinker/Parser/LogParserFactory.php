<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Parser;

use App\Domain\LogSinker\Reader\ReaderInterface;

final readonly class LogParserFactory implements LogParserFactoryInterface
{
    public function __construct(
        private ParserStrategyInterface $parserStrategy
    ) {}

    public function create(ReaderInterface $reader): ParserInterface
    {
        return new LogParser($reader, $this->parserStrategy);
    }
}
