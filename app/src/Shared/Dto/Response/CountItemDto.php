<?php

declare(strict_types=1);

namespace App\Shared\DTO\Response;

readonly class CountItemDto
{
    public function __construct(
        public int $counter
    ) {}
}
