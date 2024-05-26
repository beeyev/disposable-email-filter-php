<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\BlacklistUpdater\Drivers;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Tests\Support\Updater\DataManipulation\ContentsManipulatorTestHelper;
use Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers\TxtBlacklistUpdater;

/**
 * @covers \Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers\TxtBlacklistUpdater
 *
 * @internal
 */
final class TxtBlacklistUpdaterTest extends AbstractTestCase
{
    public function testUpdate(): void
    {
        $contentsManipulatorTestHelper = new ContentsManipulatorTestHelper('');

        $phpBlacklistUpdater = new TxtBlacklistUpdater($contentsManipulatorTestHelper);

        $phpBlacklistUpdater->update(['example1.com', 'example2.com']);

        self::assertSame('example1.com' . PHP_EOL . 'example2.com', $contentsManipulatorTestHelper->get());
    }
}
