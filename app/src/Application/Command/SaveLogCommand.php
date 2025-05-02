<?php

declare(strict_types=1);

namespace App\Application\Command;

final readonly class SaveLogCommand implements CommandInterface
{
    public function __construct(
        public string $filePath,
        public int $batchSize
    ) {}
}
