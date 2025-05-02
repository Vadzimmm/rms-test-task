<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\LogSinker\Parser\LogParser;
use App\Domain\LogSinker\Parser\LogParserFactoryInterface;
use App\Domain\LogSinker\Parser\ParserInterface;
use App\Domain\LogSinker\Parser\ParserStrategyInterface;
use App\Domain\LogSinker\Reader\FileReader;

final readonly class LogParserFactory implements LogParserFactoryInterface
{
    public function __construct(
        private ParserStrategyInterface $parserStrategy
    ) {}

    public function create(string $filePath): ParserInterface
    {
        $reader = new FileReader($filePath);

        return new LogParser($reader, $this->parserStrategy);
    }
}
