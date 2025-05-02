<?php

declare(strict_types=1);

namespace App\Application\Command;

use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CommandBus implements CommandBusInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {}

    public function execute(CommandInterface $command): void
    {
        try {
            $this->commandBus->dispatch($command);
        } catch (ExceptionInterface $e) {
            throw $e->getPrevious() ?? $e;
        }
    }
}
