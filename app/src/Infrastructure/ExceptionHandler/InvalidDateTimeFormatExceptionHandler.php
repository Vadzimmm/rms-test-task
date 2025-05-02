<?php

declare(strict_types=1);

namespace App\Infrastructure\ExceptionHandler;

use App\Shared\Exception\InvalidDateTimeFormat;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class InvalidDateTimeFormatExceptionHandler implements ExceptionHandlerInterface
{
    public function supports(\Throwable $e): bool
    {
        return $e instanceof InvalidDateTimeFormat;
    }

    public function handle(\Throwable $e): JsonResponse
    {
        return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
    }
}
