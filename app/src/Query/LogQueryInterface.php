<?php

declare(strict_types=1);

namespace App\Query;

use App\Dto\Request\LogFilterQueryParamsDto;
use App\Dto\Response\CountItemDto;

interface LogQueryInterface
{
    public function count(LogFilterQueryParamsDto $queryParams): CountItemDto;
}
