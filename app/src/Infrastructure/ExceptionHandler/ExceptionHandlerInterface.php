<?php

declare(strict_types=1);

namespace App\Infrastructure\ExceptionHandler;

use Symfony\Component\HttpFoundation\JsonResponse;

interface ExceptionHandlerInterface
{
    public function supports(\Throwable $e): bool;

    public function handle(\Throwable $e): JsonResponse;
}
