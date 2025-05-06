<?php

declare(strict_types=1);

namespace App\LogSinker\Service;

use App\LogSinker\Exception\InvalidBatchSizeException;
use App\LogSinker\Stream\LineStreamInterface;

interface LogSinkerServiceInterface
{
    /**
     * @throws InvalidBatchSizeException
     */
    public function importFrom(LineStreamInterface $lineStream, int $batchSize): void;
}
