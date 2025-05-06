<?php

declare(strict_types=1);

namespace App\Service\LogSinker\Stream;

use App\Service\LogSinker\Exception\FileNotReadableException;
use App\Service\LogSinker\Exception\FileOpenException;

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
