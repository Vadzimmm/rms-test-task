<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Parser;

use App\Domain\LogSinker\Reader\ReaderInterface;

interface LogParserFactoryInterface
{
    public function create(ReaderInterface $reader): ParserInterface;
}
