<?php

declare(strict_types=1);

namespace App\Dto\Response;

readonly class CountItemDto
{
    public function __construct(
        public int $counter
    ) {}
}
