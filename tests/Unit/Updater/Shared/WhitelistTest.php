<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\Shared;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Tests\Support\Updater\DataManipulation\ContentsManipulatorTestHelper;
use Beeyev\DisposableEmailFilter\Updater\Shared\Whitelist;

/**
 * @covers \Beeyev\DisposableEmailFilter\Updater\Shared\Whitelist
 *
 * @uses \Beeyev\DisposableEmailFilter\Updater\Support\DomainsExtractor
 * @uses \Beeyev\DisposableEmailFilter\Updater\Support\Utils
 *
 * @internal
 */
final class WhitelistTest extends AbstractTestCase
{
    public function testFromContents(): void
    {
        $contentsManipulatorTestHelper = new ContentsManipulatorTestHelper(implode(PHP_EOL, [
            'abc1.com',
            'abc1.com',
            'abc2.com',
        ]));

        $whitelist = Whitelist::fromContents($contentsManipulatorTestHelper);

        self::assertSame(['abc1.com', 'abc2.com'], $whitelist->whitelist);
    }
}
