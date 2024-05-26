<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\BlacklistUpdater\Drivers;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Tests\Support\Updater\DataManipulation\ContentsManipulatorTestHelper;
use Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers\JsonBlacklistUpdater;
use Beeyev\DisposableEmailFilter\Updater\Support\Utils;

/**
 * @covers \Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers\JsonBlacklistUpdater
 *
 * @uses \Beeyev\DisposableEmailFilter\Updater\Support\Utils
 *
 * @internal
 */
final class JsonBlacklistUpdaterTest extends AbstractTestCase
{
    public function testUpdate(): void
    {
        $contentsManipulatorTestHelper = new ContentsManipulatorTestHelper('');

        $phpBlacklistUpdater = new JsonBlacklistUpdater($contentsManipulatorTestHelper);
        $phpBlacklistUpdater->update(['example1.com', 'example2.com']);
        $result = Utils::jsonDecode($contentsManipulatorTestHelper->get());

        self::assertSame(['example1.com', 'example2.com'], $result);
    }
}
