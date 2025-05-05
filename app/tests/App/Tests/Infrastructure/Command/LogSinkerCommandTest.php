<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Command;

use App\Application\Command\CommandBusInterface;
use App\Application\Command\SaveLogCommand;
use App\Domain\LogSinker\Exception\FileNotReadableException;
use App\Infrastructure\Command\LogSinkerCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(LogSinkerCommand::class)]
final class LogSinkerCommandTest extends TestCase
{
    public function testExecuteWithValidArguments(): void
    {
        $filePath = sys_get_temp_dir().'/test.log';
        file_put_contents($filePath, "log line\n");

        $busMock = $this->createMock(CommandBusInterface::class);
        $busMock->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (SaveLogCommand $command) use ($filePath) {
                return $command->filePath === $filePath && 5 === $command->batchSize;
            }))
        ;

        $command = new LogSinkerCommand($busMock);
        $tester = new CommandTester($command);

        $tester->execute([
            'file' => $filePath,
            'batchSize' => 5,
        ]);

        $this->assertSame(0, $tester->getStatusCode());

        unlink($filePath);
    }

    public function testFailsWithMissingFile(): void
    {
        $busMock = $this->createMock(CommandBusInterface::class);
        $command = new LogSinkerCommand($busMock);
        $tester = new CommandTester($command);

        $this->expectException(FileNotReadableException::class);

        $tester->execute([
            'file' => '/non/existing/path.log',
        ]);
    }

    public function testFailsWithInvalidBatchSize(): void
    {
        $filePath = sys_get_temp_dir().'/test_invalid_batch.log';
        file_put_contents($filePath, "log line\n");

        $busMock = $this->createMock(CommandBusInterface::class);
        $command = new LogSinkerCommand($busMock);
        $tester = new CommandTester($command);

        $tester->execute([
            'file' => $filePath,
            'batchSize' => 0,
        ]);

        $this->assertSame(1, $tester->getStatusCode());

        unlink($filePath);
    }
}
