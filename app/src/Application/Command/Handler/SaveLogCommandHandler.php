<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\SaveLogCommand;
use App\Domain\LogSinker\Reader\FileReader;
use App\Domain\LogSinker\Service\LogSinkerServiceInterface;

final readonly class SaveLogCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private LogSinkerServiceInterface $logSinkerService,
    ) {}

    public function __invoke(SaveLogCommand $saveLogCommand): void
    {
        $reader = new FileReader($saveLogCommand->filePath);
        $this->logSinkerService->importFrom($reader, $saveLogCommand->batchSize);
    }
}
