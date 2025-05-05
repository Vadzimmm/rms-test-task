<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Stream;

use App\Domain\LogSinker\Exception\FileNotReadableException;
use App\Domain\LogSinker\Exception\FileOpenException;

interface LineStreamInterface
{
    /**
     * @return iterable<string>
     *
     * @throws FileNotReadableException
     * @throws FileOpenException
     */
    public function read(): iterable;
}
