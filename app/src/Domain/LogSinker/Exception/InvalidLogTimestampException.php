<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Exception;

final class InvalidLogTimestampException extends \RuntimeException
{
    public function __construct(string $rawTimestamp, string $expectedFormat)
    {
        $message = sprintf(
            'Failed to parse timestamp "%s" with expected format "%s".',
            $rawTimestamp,
            $expectedFormat
        );

        parent::__construct($message);
    }
}
