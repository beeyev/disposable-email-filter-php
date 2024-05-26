<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\ChangelogTracker;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\LastSyncedBlacklistsFileManipulator;
use Beeyev\DisposableEmailFilter\Updater\Support\Utils;

/**
 * @covers \Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\LastSyncedBlacklistsFileManipulator
 *
 * @uses \Beeyev\DisposableEmailFilter\Updater\Support\Utils
 *
 * @internal
 */
final class LastSyncedBlacklistsFileManipulatorTest extends AbstractTestCase
{
    private const TEST_JSON_CONTENTS = [
        'boo' => 'bar',
    ];

    /** @var non-empty-string */
    private $tempFilePath;

    protected function setUp(): void
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'phpunit_');
        assert(is_string($tempFilePath) && $tempFilePath !== '');
        $this->tempFilePath = $tempFilePath;

        Utils::filePutContents($this->tempFilePath, $this->getTestJsonContents());
    }

    public function testGetContentsSuccessfullyReturnsArray(): void
    {
        $manipulator = new LastSyncedBlacklistsFileManipulator($this->tempFilePath);

        $contents = $manipulator->getContents();

        self::assertSame(self::TEST_JSON_CONTENTS, $contents);
    }

    public function testOverrideContentsSuccessfullyOverrides(): void
    {
        $manipulator = new LastSyncedBlacklistsFileManipulator($this->tempFilePath);

        $manipulator->overrideContents(['lorem' => 'ipsum']);

        $result = Utils::fileGetContents($this->tempFilePath);

        self::assertSame(['lorem' => 'ipsum'], Utils::jsonDecode($result));
    }

    private function getTestJsonContents(): string
    {
        return json_encode(self::TEST_JSON_CONTENTS); // @phpstan-ignore return.type
    }

    protected function tearDown(): void
    {
        unlink($this->tempFilePath);
    }
}
