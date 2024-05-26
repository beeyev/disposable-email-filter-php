<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\Support;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Updater\Support\Utils;

/**
 * @covers \Beeyev\DisposableEmailFilter\Updater\Support\Utils
 *
 * @internal
 */
final class UtilsTest extends AbstractTestCase
{
    public function testFileGetContentsSuccess(): void
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'phpunit_');
        assert(is_string($tempFilePath) && $tempFilePath !== '');

        Utils::filePutContents($tempFilePath, 'lorem ipsum dolor sit amet');

        $contents = Utils::fileGetContents($tempFilePath);
        self::assertSame('lorem ipsum dolor sit amet', $contents);

        unlink($tempFilePath);
    }

    public function testFileGetContentsThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        Utils::fileGetContents('/dummy');
    }

    public function testFilePutContentsThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        Utils::filePutContents('/dummy', 'lorem ipsum dolor sit amet');
    }

    public function testIsStringStartsWith(): void
    {
        self::assertTrue(Utils::isStringStartsWith('lorem ipsum dolor sit amet', 'lorem '));
        self::assertFalse(Utils::isStringStartsWith('lorem ipsum dolor sit amet', 'ipsum'));
    }

    public function testJsonDecodeSuccess(): void
    {
        $data = Utils::jsonDecode('{"key": "value"}');
        self::assertSame(['key' => 'value'], $data);
    }

    public function testJsonDecodeThrowsExceptionIfInvalidJson(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Could not decode JSON string\. Error: .+$/');
        Utils::jsonDecode('{"key": "value');
    }

    public function testJsonDecodeThrowsExceptionIfResultIsNotArray(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not decode JSON string. Result is not an array');
        Utils::jsonDecode('true');
    }

    public function testHash(): void
    {
        $hash = Utils::hash('lorem ipsum dolor sit amet');
        self::assertSame('201730d4278e576b25515bd90c6072d3', $hash);
    }
}
