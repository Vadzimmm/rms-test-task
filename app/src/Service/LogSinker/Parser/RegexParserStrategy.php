<?php

declare(strict_types=1);

namespace App\Service\LogSinker\Parser;

use App\Service\LogSinker\Exception\InvalidLogTimestampException;
use App\Service\LogSinker\LogEntry;

final readonly class RegexParserStrategy implements ParserStrategyInterface
{
    private const string LOG_PATTERN = '/^(\S+)(?:\s+-){2}\s+\[(\d{2}\/[A-Za-z]{3}\/\d{4}:\d{2}:\d{2}:\d{2}\s\+\d{4})\]\s+"([^"]+)"\s+(\d+)$/';
    private const string DATETIME_FORMAT = 'd/M/Y:H:i:s O';

    public function parseEntry(string $logRecord): ?LogEntry
    {
        if (!preg_match(self::LOG_PATTERN, trim($logRecord), $matches)) {
            return null;
        }

        [, $serviceName, $timestampStr, $requestLine, $statusCode] = $matches;

        $timestamp = \DateTimeImmutable::createFromFormat(
            self::DATETIME_FORMAT,
            $timestampStr
        );

        if (false === $timestamp) {
            throw new InvalidLogTimestampException($timestampStr, self::DATETIME_FORMAT);
        }

        return new LogEntry(
            $serviceName,
            $timestamp,
            $requestLine,
            (int) $statusCode
        );
    }
}
