<?php

declare(strict_types=1);

namespace App\Service\LogSinker\Service;

use App\Service\LogSinker\Exception\InvalidBatchSizeException;
use App\Service\LogSinker\Stream\LineStreamInterface;

interface LogSinkerServiceInterface
{
    /**
     * @throws InvalidBatchSizeException
     */
    public function importFrom(LineStreamInterface $lineStream, int $batchSize): void;
}
