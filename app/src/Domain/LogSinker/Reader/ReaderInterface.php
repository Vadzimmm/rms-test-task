<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Reader;

use App\Domain\LogSinker\Exception\FileNotReadableException;
use App\Domain\LogSinker\Exception\FileOpenException;

interface ReaderInterface
{
    /**
     * @return iterable<string>
     *
     * @throws FileNotReadableException
     * @throws FileOpenException
     */
    public function read(): iterable;
}
