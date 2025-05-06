<?php

declare(strict_types=1);

namespace App\LogSinker\Stream;

use App\LogSinker\Exception\FileNotReadableException;
use App\LogSinker\Exception\FileOpenException;

final readonly class FileLineStream implements LineStreamInterface
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

        if (!feof($file)) {
            $line = fgets($file);
            if (false !== $line) {
                yield str_replace(pack('H*', 'EFBBBF'), '', $line);
            }
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
