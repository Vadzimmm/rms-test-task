<?php

declare(strict_types=1);

namespace App\Exception;

final class InvalidDateTimeFormat extends \InvalidArgumentException
{
    public function __construct(
        string $message = 'Invalid datetime format',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
