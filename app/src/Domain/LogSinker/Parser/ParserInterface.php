<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Parser;

use App\Domain\LogSinker\LogEntry;

interface ParserInterface
{
    /**
     * @return iterable<LogEntry>
     */
    public function parse(): iterable;
}
