<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Parser;

use App\Domain\LogSinker\LogEntry;
use App\Domain\LogSinker\Stream\LineStreamInterface;

interface ParserInterface
{
    /**
     * @return iterable<LogEntry>
     */
    public function parseFrom(LineStreamInterface $lineStream): iterable;
}
