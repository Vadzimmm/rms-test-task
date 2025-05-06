<?php

declare(strict_types=1);

namespace App\Service\LogSinker\Parser;

use App\Service\LogSinker\LogEntry;
use App\Service\LogSinker\Stream\LineStreamInterface;

interface ParserInterface
{
    /**
     * @return iterable<LogEntry>
     */
    public function parseFrom(LineStreamInterface $lineStream): iterable;
}
