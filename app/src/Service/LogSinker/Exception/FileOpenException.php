<?php

declare(strict_types=1);

namespace App\Service\LogSinker\Exception;

final class FileOpenException extends \RuntimeException
{
    public function __construct(
        readonly string $filePath,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $finalMessage = $message ?: sprintf('Failed to open the file. Filepath: "%s"', $filePath);
        parent::__construct($finalMessage, $code, $previous);
    }
}
