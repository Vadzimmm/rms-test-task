<?php

declare(strict_types=1);

namespace App\LogSinker;

interface ParserStrategyInterface
{
    public function parseEntry(string $logRecord): ?LogEntry;
}
