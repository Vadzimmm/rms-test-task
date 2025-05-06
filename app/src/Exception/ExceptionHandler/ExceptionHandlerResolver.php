<?php

declare(strict_types=1);

namespace App\Exception\ExceptionHandler;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class ExceptionHandlerResolver
{
    /**
     * @param iterable<ExceptionHandlerInterface> $handlers
     */
    public function __construct(
        private iterable $handlers,
        private LoggerInterface $logger
    ) {}

    public function handle(\Throwable $exception): JsonResponse
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($exception)) {
                return $handler->handle($exception);
            }
        }

        $this->logger->error($exception->getMessage());

        return new JsonResponse(
            ['error' => 'Unexpected error'],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
