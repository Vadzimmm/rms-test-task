<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Reader;

use App\Domain\LogSinker\Exception\FileNotReadableException;
use App\Domain\LogSinker\Exception\FileOpenException;

final readonly class FileReader implements ReaderInterface
{
    public function __construct(
        private string $filePath
    ) {}

    public function read(): \Generator
    {
        if (!is_readable($this->filePath)) {
            throw new FileNotReadableException($this->filePath);
        }

        $file = fopen($this->filePath, 'rb');

        if (!$file) {
            throw new FileOpenException($this->filePath);
        }

        while (!feof($file)) {
            $line = fgets($file);
            if (false !== $line) {
                yield $line;
            }
        }

        fclose($file);
    }
}
