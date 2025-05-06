<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\LogSinkerCommand;
use App\LogSinker\Exception\FileNotReadableException;
use App\LogSinker\LogEntry;
use App\LogSinker\Parser\ParserInterface;
use App\LogSinker\Repository\LogEntryRepositoryInterface;
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

        $entry = new LogEntry(
            'service',
            new \DateTimeImmutable('2024-01-01T00:00:00+00:00'),
            'GET /test HTTP/1.1',
            200
        );

        $parser = $this->createMock(ParserInterface::class);
        $parser->method('parseFrom')->willReturn([$entry]);

        $repo = $this->createMock(LogEntryRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('save')
            ->with($entry)
        ;

        $command = new LogSinkerCommand($repo, $parser);
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
        $this->expectException(FileNotReadableException::class);

        $parser = $this->createMock(ParserInterface::class);
        $repo = $this->createMock(LogEntryRepositoryInterface::class);

        $command = new LogSinkerCommand($repo, $parser);
        $tester = new CommandTester($command);

        $tester->execute([
            'file' => '/non/existing/path.log',
        ]);
    }

    public function testFailsWithInvalidBatchSize(): void
    {
        $filePath = sys_get_temp_dir().'/test_invalid_batch.log';
        file_put_contents($filePath, "log line\n");

        $parser = $this->createMock(ParserInterface::class);
        $repo = $this->createMock(LogEntryRepositoryInterface::class);

        $command = new LogSinkerCommand($repo, $parser);
        $tester = new CommandTester($command);

        $tester->execute([
            'file' => $filePath,
            'batchSize' => 0,
        ]);

        $this->assertSame(1, $tester->getStatusCode());

        unlink($filePath);
    }
}
