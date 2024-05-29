<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\BlacklistUpdater\Drivers;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Tests\Support\Updater\DataManipulation\ContentsManipulatorTestHelper;
use Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers\PhpBlacklistStyleFixerCodeCheckerInterface;
use Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers\PhpBlacklistUpdater;

/**
 * @covers \Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers\PhpBlacklistUpdater
 *
 * @internal
 */
final class PhpBlacklistUpdaterTest extends AbstractTestCase
{
    public function testUpdate(): void
    {
        $currentDate = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $contentsManipulatorTestHelper = new ContentsManipulatorTestHelper('');
        $phpBlacklistStyleFixerCodeChecker = $this->createMock(PhpBlacklistStyleFixerCodeCheckerInterface::class);
        $phpBlacklistStyleFixerCodeChecker->expects(self::once())->method('fixAndCheck');
        $phpBlacklistUpdater = new PhpBlacklistUpdater($contentsManipulatorTestHelper, $phpBlacklistStyleFixerCodeChecker);

        $phpBlacklistUpdater->update(['example1.com', 'example2.com']);
        $result = eval(trim($contentsManipulatorTestHelper->get(), '<?php ?>'));

        self::assertIsArray($result);
        self::assertSame(['example1.com' => true, 'example2.com' => true], $result['disposable_email_domains']);

        self::assertInstanceOf(\DateTimeImmutable::class, $result['updated_at']);
        self::assertEqualsWithDelta($currentDate, $result['updated_at'], 1);
        self::assertSame('UTC', $result['updated_at']->getTimezone()->getName());
    }
}
