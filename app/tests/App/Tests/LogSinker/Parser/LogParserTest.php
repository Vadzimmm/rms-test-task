<?php

declare(strict_types=1);

namespace App\Tests\LogSinker\Parser;

use App\LogSinker\LogEntry;
use App\LogSinker\Parser\LogParser;
use App\LogSinker\Parser\ParserStrategyInterface;
use App\LogSinker\Stream\LineStreamInterface;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[CoversClass(LogParser::class)]
final class LogParserTest extends TestCase
{
    public function testSuccessfulParse(): void
    {
        $logLine = 'USER-SERVICE - - [18/Aug/2018:10:33:59 +0000] "POST /users HTTP/1.1" 201';
        $carbon = new Carbon('18/Aug/2018:10:33:59 +0000');

        $parsedEntry = new LogEntry(
            'USER-SERVICE',
            \DateTimeImmutable::createFromMutable($carbon),
            'POST /users HTTP/1.1',
            201
        );

        $lineSteam = $this->createMock(LineStreamInterface::class);
        $lineSteam->method('read')->willReturn([$logLine]);

        $strategy = $this->createMock(ParserStrategyInterface::class);
        $strategy->expects($this->once())
            ->method('parseEntry')
            ->with($logLine)
            ->willReturn($parsedEntry)
        ;

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('debug');

        $parser = new LogParser($strategy, $logger);

        $result = iterator_to_array($parser->parseFrom($lineSteam));

        $this->assertCount(1, $result);
        $this->assertSame($parsedEntry, $result[0]);
    }

    public function testParseReturnsNullLogsDebug(): void
    {
        $logLine = 'INVALID LOG LINE';

        $lineSteam = $this->createMock(LineStreamInterface::class);
        $lineSteam->method('read')->willReturn([$logLine]);

        $strategy = $this->createMock(ParserStrategyInterface::class);
        $strategy->expects($this->once())
            ->method('parseEntry')
            ->with($logLine)
            ->willReturn(null)
        ;

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->atLeastOnce())->method('debug');

        $parser = new LogParser($strategy, $logger);

        $result = iterator_to_array($parser->parseFrom($lineSteam));

        $this->assertCount(0, $result);
    }
}
