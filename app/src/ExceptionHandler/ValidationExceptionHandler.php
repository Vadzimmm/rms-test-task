<?php

declare(strict_types=1);

namespace App\ExceptionHandler;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ValidationExceptionHandler implements ExceptionHandlerInterface
{
    public function supports(\Throwable $e): bool
    {
        return $e->getPrevious() instanceof ValidationFailedException;
    }

    public function handle(\Throwable $e): JsonResponse
    {
        $errors = [];

        $previous = $e->getPrevious();

        if ($previous instanceof ValidationFailedException) {
            foreach ($previous->getViolations() as $v) {
                $errors[] = [
                    'field' => $v->getPropertyPath(),
                    'message' => $v->getMessage(),
                ];
            }
        }

        return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }
}
