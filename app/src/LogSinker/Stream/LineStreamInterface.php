<?php

declare(strict_types=1);

namespace App\LogSinker\Stream;

use App\LogSinker\Exception\FileNotReadableException;
use App\LogSinker\Exception\FileOpenException;

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
