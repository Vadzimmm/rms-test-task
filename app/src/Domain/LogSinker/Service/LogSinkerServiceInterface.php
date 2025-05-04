<?php

namespace App\Domain\LogSinker\Service;

use App\Domain\LogSinker\Reader\ReaderInterface;

interface LogSinkerServiceInterface
{
    public function importFrom(ReaderInterface $reader, int $batchSize): void;
}
