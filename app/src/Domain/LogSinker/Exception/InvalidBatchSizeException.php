<?php

declare(strict_types=1);

namespace App\Domain\LogSinker\Exception;

final class InvalidBatchSizeException extends \InvalidArgumentException
{
    public function __construct(int $batchSize)
    {
        parent::__construct(sprintf('Batch size must be greater than 0, %d given.', $batchSize));
    }
}
