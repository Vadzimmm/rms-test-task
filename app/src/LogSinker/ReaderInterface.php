<?php

declare(strict_types=1);

namespace App\LogSinker;

interface ReaderInterface
{
    /**
     * @return iterable<string>
     */
    public function read(): iterable;
}
