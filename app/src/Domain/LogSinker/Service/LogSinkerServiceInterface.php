<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Service;

use App\Domain\LogSinker\Exception\InvalidBatchSizeException;
use App\Domain\LogSinker\Reader\ReaderInterface;

interface LogSinkerServiceInterface
{
    /**
     * @throws InvalidBatchSizeException
     */
    public function importFrom(ReaderInterface $reader, int $batchSize): void;
}
