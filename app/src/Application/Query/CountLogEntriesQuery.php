<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Shared\DTO\Request\LogFilterQueryParamsDto;

final readonly class CountLogEntriesQuery implements QueryInterface
{
    public function __construct(
        public LogFilterQueryParamsDto $logFilterQueryParamsDto
    ) {}
}
