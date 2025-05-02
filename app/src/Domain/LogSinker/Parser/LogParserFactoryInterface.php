<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Parser;

interface LogParserFactoryInterface
{
    public function create(string $filePath): ParserInterface;
}
