<?php

declare(strict_types=1);

namespace App\Tests\LogSinker\Parser;

use App\Service\LogSinker\Exception\InvalidLogTimestampException;
use App\Service\LogSinker\LogEntry;
use App\Service\LogSinker\Parser\ParserStrategyInterface;
use App\Service\LogSinker\Parser\RegexParserStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RegexParserStrategy::class)]
final class RegexParserStrategyTest extends TestCase
{
    private ParserStrategyInterface $parser;

    protected function setUp(): void
    {
        $this->parser = new RegexParserStrategy();
    }

    public function testParsesValidLogLine(): void
    {
        $logLine = 'INVOICE-SERVICE - - [18/Aug/2018:10:26:53 +0000] "POST /invoices HTTP/1.1" 201';

        $entry = $this->parser->parseEntry($logLine);

        $this->assertInstanceOf(LogEntry::class, $entry);
        $this->assertSame('INVOICE-SERVICE', $entry->serviceName);
        $this->assertSame('POST /invoices HTTP/1.1', $entry->requestLine);
        $this->assertSame(201, $entry->statusCode);
        $this->assertSame('2018-08-18T10:26:53+00:00', $entry->timestamp->format(\DateTimeInterface::ATOM));
    }

    public function testReturnsNullIfPatternDoesNotMatch(): void
    {
        $invalidLine = 'not a valid log line';
        $this->assertNull($this->parser->parseEntry($invalidLine));
    }

    public function testThrowsExceptionIfDateInvalid(): void
    {
        $badLine = 'INVOICE-SERVICE - - [99/XXX/2018:99:99:99 +0000] "GET / HTTP/1.1" 200';

        $this->expectException(InvalidLogTimestampException::class);

        $this->parser->parseEntry($badLine);
    }
}
