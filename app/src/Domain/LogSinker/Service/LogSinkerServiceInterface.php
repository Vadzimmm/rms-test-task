<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Service;

use App\Domain\LogSinker\Exception\InvalidBatchSizeException;
use App\Domain\LogSinker\Stream\LineStreamInterface;

interface LogSinkerServiceInterface
{
    /**
     * @throws InvalidBatchSizeException
     */
    public function importFrom(LineStreamInterface $lineStream, int $batchSize): void;
}
