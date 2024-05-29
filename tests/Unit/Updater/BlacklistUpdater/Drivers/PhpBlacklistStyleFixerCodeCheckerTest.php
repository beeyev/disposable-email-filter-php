<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\BlacklistUpdater\Drivers;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers\PhpBlacklistStyleFixerCodeChecker;
use Beeyev\DisposableEmailFilter\Updater\Support\Utils;

/**
 * @covers \Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers\PhpBlacklistStyleFixerCodeChecker
 *
 * @uses \Beeyev\DisposableEmailFilter\Updater\Support\Utils
 *
 * @internal
 */
final class PhpBlacklistStyleFixerCodeCheckerTest extends AbstractTestCase
{
    /** @var non-empty-string */
    private $tempFilePath;

    protected function setUp(): void
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'phpunit_');
        assert(is_string($tempFilePath) && $tempFilePath !== '');
        $this->tempFilePath = $tempFilePath;
    }

    public function testSuccessfulFixAndCheck(): void
    {
        Utils::filePutContents($this->tempFilePath, PhpBlacklistUpdaterDummyData::getValidPhpData());

        $phpBlacklistStyleFixerCodeChecker = new PhpBlacklistStyleFixerCodeChecker($this->tempFilePath);
        $phpBlacklistStyleFixerCodeChecker->fixAndCheck();

        $resultArray = require $this->tempFilePath;
        self::assertIsArray($resultArray);
    }

    public function testInvalidFixAndCheck(): void
    {
        Utils::filePutContents($this->tempFilePath, PhpBlacklistUpdaterDummyData::getInvalidPhpData());
        $phpBlacklistStyleFixerCodeChecker = new PhpBlacklistStyleFixerCodeChecker($this->tempFilePath);

        $this->expectException(\RuntimeException::class);
        $phpBlacklistStyleFixerCodeChecker->fixAndCheck();
    }

    public function testEmptyArrayFixAndCheck(): void
    {
        Utils::filePutContents($this->tempFilePath, PhpBlacklistUpdaterDummyData::getEmptyPhpData());
        $phpBlacklistStyleFixerCodeChecker = new PhpBlacklistStyleFixerCodeChecker($this->tempFilePath);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Given file does not contain any data.');
        $phpBlacklistStyleFixerCodeChecker->fixAndCheck();
    }

    protected function tearDown(): void
    {
        unlink($this->tempFilePath);
    }
}
