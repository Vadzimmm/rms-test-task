<?php

declare(strict_types=1);

namespace App\Tests\Domain\LogSinker\Service;

use App\Domain\LogSinker\Exception\InvalidBatchSizeException;
use App\Domain\LogSinker\LogEntry;
use App\Domain\LogSinker\Parser\ParserInterface;
use App\Domain\LogSinker\Repository\LogEntryRepositoryInterface;
use App\Domain\LogSinker\Service\LogSinkerService;
use App\Domain\LogSinker\Stream\LineStreamInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LogSinkerService::class)]
final class LogSinkerServiceTest extends TestCase
{
    public function testImportWithExactBatchSize(): void
    {
        $batchSize = 2;
        $entries = [
            $this->createFakeEntry(),
            $this->createFakeEntry(),
        ];
        $lineSteam = $this->createMock(LineStreamInterface::class);
        $parser = $this->createMock(ParserInterface::class);
        $parser->method('parseFrom')->with($lineSteam)->willReturn($entries);
        $repo = $this->createMock(LogEntryRepositoryInterface::class);

        $repo->expects($this->once())
            ->method('save')
            ->with(...$entries)
        ;

        $service = new LogSinkerService($repo, $parser);
        $service->importFrom($lineSteam, $batchSize);
    }

    public function testImportWithMultipleBatchesAndRemainder(): void
    {
        $batchSize = 3;
        $e1 = $this->createFakeEntry();
        $e2 = $this->createFakeEntry();
        $e3 = $this->createFakeEntry();
        $e4 = $this->createFakeEntry();
        $entries = [$e1, $e2, $e3, $e4];
        $reader = $this->createMock(LineStreamInterface::class);
        $parser = $this->createMock(ParserInterface::class);
        $parser->method('parseFrom')->with($reader)->willReturn($entries);
        $calls = [];
        $repo = $this->createMock(LogEntryRepositoryInterface::class);
        $repo->method('save')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $calls[] = $args;
            })
        ;

        $service = new LogSinkerService($repo, $parser);
        $service->importFrom($reader, $batchSize);

        $this->assertCount(2, $calls);
        $this->assertSame([$e1, $e2, $e3], $calls[0]);
        $this->assertSame([$e4], $calls[1]);
    }

    public function testThrowsOnInvalidBatchSize(): void
    {
        $reader = $this->createMock(LineStreamInterface::class);
        $parser = $this->createMock(ParserInterface::class);
        $repo = $this->createMock(LogEntryRepositoryInterface::class);
        $service = new LogSinkerService($repo, $parser);

        $this->expectException(InvalidBatchSizeException::class);
        $service->importFrom($reader, 0);
    }

    private function createFakeEntry(): LogEntry
    {
        return new LogEntry(
            'test-service',
            new \DateTimeImmutable('2024-01-01T00:00:00+00:00'),
            'GET /test HTTP/1.1',
            200
        );
    }
}
