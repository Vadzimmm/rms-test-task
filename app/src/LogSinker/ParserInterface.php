<?php

declare(strict_types=1);

namespace App\LogSinker;

interface ParserInterface
{
    /**
     * @return iterable<LogEntry>
     */
    public function parse(): iterable;
}
