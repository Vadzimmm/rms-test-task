<?php

declare(strict_types=1);

namespace App\Infrastructure\EventListener;

use App\Infrastructure\ExceptionHandler\ExceptionHandlerResolver;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final readonly class ExceptionListener
{
    public function __construct(
        private ExceptionHandlerResolver $resolver
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = $this->resolver->handle($exception);
        $event->setResponse($response);
    }
}
