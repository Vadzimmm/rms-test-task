<?php

declare(strict_types=1);

namespace App\Tests\Domain\LogSinker\Reader;

use App\Domain\LogSinker\Exception\FileNotReadableException;
use App\Domain\LogSinker\Reader\FileReader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FileReader::class)]
final class FileReaderTest extends TestCase
{
    private string $tempFile;

    protected function tearDown(): void
    {
        if (isset($this->tempFile) && file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testSuccessfulRead(): void
    {
        $lines = ['First line'.PHP_EOL, 'Second line'.PHP_EOL, 'Third line'.PHP_EOL];

        $this->tempFile = tempnam(sys_get_temp_dir(), 'log_');
        file_put_contents($this->tempFile, implode('', $lines));

        $reader = new FileReader($this->tempFile);
        $result = iterator_to_array($reader->read());

        $this->assertSame($lines, $result);
    }

    public function testThrowsIfFileNotReadable(): void
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'log_');
        chmod($this->tempFile, 0000);

        $reader = new FileReader($this->tempFile);

        $this->expectException(FileNotReadableException::class);
        iterator_to_array($reader->read());
    }
}
