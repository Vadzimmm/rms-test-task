<?php

declare(strict_types=1);

namespace App\LogSinker\Parser;

use App\LogSinker\LogEntry;
use App\LogSinker\Stream\LineStreamInterface;

interface ParserInterface
{
    /**
     * @return iterable<LogEntry>
     */
    public function parseFrom(LineStreamInterface $lineStream): iterable;
}
