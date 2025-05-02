<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Reader;

interface ReaderInterface
{
    /**
     * @return iterable<string>
     */
    public function read(): iterable;
}
