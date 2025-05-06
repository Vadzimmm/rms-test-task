<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Request\LogFilterQueryParamsDto;
use App\Dto\Response\CountItemDto;

interface LogQueryServiceInterface
{
    public function count(LogFilterQueryParamsDto $queryParams): CountItemDto;
}
