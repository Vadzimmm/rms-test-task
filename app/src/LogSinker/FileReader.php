<?php

declare(strict_types=1);

namespace App\LogSinker;

use App\LogSinker\Exception\FileNotReadableException;
use App\LogSinker\Exception\FileOpenException;
use App\LogSinker\Exception\FileReadException;

readonly class FileReader implements ReaderInterface
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

        try {
            while (false !== ($line = fgets($file))) {
                yield trim($line);
            }

            if (!feof($file)) {
                throw new FileReadException($this->filePath);
            }
        } finally {
            fclose($file);
        }
    }
}
