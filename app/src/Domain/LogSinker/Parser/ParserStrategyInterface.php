<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Parser;

use App\Domain\LogSinker\LogEntry;

interface ParserStrategyInterface
{
    public function parseEntry(string $logRecord): ?LogEntry;
}
