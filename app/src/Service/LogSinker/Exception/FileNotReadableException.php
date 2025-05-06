<?php

declare(strict_types=1);

namespace App\Service\LogSinker\Exception;

final class FileNotReadableException extends \RuntimeException
{
    public function __construct(
        readonly string $filePath,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $finalMessage = $message ?: sprintf('The file "%s" is not readable.', $filePath);
        parent::__construct($finalMessage, $code, $previous);
    }
}
