<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Parser;

use App\Domain\LogSinker\LogEntry;

readonly class RegexParserStrategyInterface implements ParserStrategyInterface
{
    protected const string LOG_PATTERN = '/^(\S+)(?:\s+-){2}\s+\[(\d{2}\/[A-Za-z]{3}\/\d{4}:\d{2}:\d{2}:\d{2}\s\+\d{4})\]\s+"([^"]+)"\s+(\d+)$/';
    protected const string DATETIME_FORMAT = 'd/M/Y:H:i:s O';

    public function parseEntry(string $logRecord): ?LogEntry
    {
        if (preg_match(static::LOG_PATTERN, $logRecord, $matches)) {
            [, $serviceName, $timestampStr, $requestLine, $statusCode] = $matches;

            $timestamp = \DateTimeImmutable::createFromFormat(
                static::DATETIME_FORMAT,
                $timestampStr
            );

            if (false === $timestamp) {
                throw new \RuntimeException("Invalid timestamp format: {$timestampStr}");
            }

            return new LogEntry(
                $serviceName,
                $timestamp,
                $requestLine,
                (int) $statusCode
            );
        }

        return null;
    }
}
