<?php

declare(strict_types=1);

namespace App\LogSinker\Parser;

use App\LogSinker\LogEntry;

interface ParserStrategyInterface
{
    public function parseEntry(string $logRecord): ?LogEntry;
}
