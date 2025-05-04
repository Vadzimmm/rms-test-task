<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Reader;

use App\Domain\LogSinker\Exception\FileNotReadableException;
use App\Domain\LogSinker\Exception\FileOpenException;
use App\Domain\LogSinker\Exception\FileReadException;

interface ReaderInterface
{
    /**
     * @return iterable<string>
     * @throws FileNotReadableException|FileOpenException|FileReadException
     */
    public function read(): iterable;
}
