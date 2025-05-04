<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Exception;

final class FileReadException extends \RuntimeException
{
    public function __construct(
        readonly string $filePath,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $finalMessage = $message ?: sprintf('Error occurred while reading the file: %s', $filePath);
        parent::__construct($finalMessage, $code, $previous);
    }
}
