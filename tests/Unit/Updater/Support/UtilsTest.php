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

    /**
     * @dataProvider domainProvider
     */
    public function testNaturalSortOrdersDomainsCorrectly(array $input, array $expected): void
    {
        Utils::naturalSort($input);
        self::assertSame($expected, $input);
    }

    public function domainProvider(): array
    {
        return [
            'Numerical sorting with leading zeros' => [
                ['47gmail.com', '047gmail.com', '47bmt.com'],
                ['47bmt.com', '047gmail.com', '47gmail.com'],
            ],
            'Mixed numerical/alpha segments' => [
                ['img12.com', 'img2.com', 'img1.com'],
                ['img1.com', 'img2.com', 'img12.com'],
            ],
            'Natural equivalence with string fallback' => [
                ['apple01.com', 'apple1.com', 'apple001.com'],
                ['apple001.com', 'apple01.com', 'apple1.com'],
            ],
            'Different TLDs with same name' => [
                ['test.net', 'test.com', 'test.org'],
                ['test.com', 'test.net', 'test.org'],
            ],
            'Complex domain patterns' => [
                ['1a.example.com', 'a1.example.com', '01a.example.com'],
                ['01a.example.com', '1a.example.com', 'a1.example.com'],
            ],
            'Subdomain sorting' => [
                ['blog.47gmail.com', '047gmail.com', 'mail.47gmail.com'],
                ['047gmail.com', 'blog.47gmail.com', 'mail.47gmail.com'],
            ],
            'Numerical TLDs (uncommon but valid)' => [
                ['example.42', 'example.7', 'example.007'],
                ['example.007', 'example.7', 'example.42'],
            ],
        ];
    }

    // Test edge cases
    public function testSpecialCases(): void
    {
        $empty = [];
        Utils::naturalSort($empty);
        self::assertSame([], $empty);

        $single = ['single.com'];
        Utils::naturalSort($single);
        self::assertSame(['single.com'], $single);

        $duplicates = ['dup.com', 'dup.com'];
        Utils::naturalSort($duplicates);
        self::assertSame(['dup.com', 'dup.com'], $duplicates);
    }
}
