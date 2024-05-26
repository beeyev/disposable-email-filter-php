<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\DataManipulation;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Updater\DataManipulation\FileContentsLoader;
use Beeyev\DisposableEmailFilter\Updater\Support\Utils;

/**
 * @covers \Beeyev\DisposableEmailFilter\Updater\DataManipulation\FileContentsLoader
 *
 * @uses \Beeyev\DisposableEmailFilter\Updater\Support\Utils
 *
 * @internal
 */
final class FileContentsLoaderTest extends AbstractTestCase
{
    private const TEST_DATA = 'lorem ipsum dolor sit amet';

    /** @var non-empty-string */
    private $tempFilePath;

    protected function setUp(): void
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'phpunit_');
        assert(is_string($tempFilePath) && $tempFilePath !== '');
        $this->tempFilePath = $tempFilePath;

        Utils::filePutContents($this->tempFilePath, self::TEST_DATA);
    }

    public function testLoadSuccessfullyLoadsContents(): void
    {
        $loader = new FileContentsLoader();

        $contents = $loader->load($this->tempFilePath);

        self::assertSame(self::TEST_DATA, $contents);
    }

    protected function tearDown(): void
    {
        unlink($this->tempFilePath);
    }
}
