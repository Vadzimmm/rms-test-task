<?php

declare(strict_types=1);

namespace App\Tests\LogSinker\Stream;

use App\Service\LogSinker\Exception\FileNotReadableException;
use App\Service\LogSinker\Stream\FileLineStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FileLineStream::class)]
final class FileLineStreamTest extends TestCase
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

        $lineSteam = new FileLineStream($this->tempFile);
        $result = iterator_to_array($lineSteam->read());

        $this->assertSame($lines, $result);
    }

    public function testThrowsIfFileNotReadable(): void
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'log_');
        chmod($this->tempFile, 0000);

        $lineSteam = new FileLineStream($this->tempFile);

        $this->expectException(FileNotReadableException::class);
        iterator_to_array($lineSteam->read());
    }
}
