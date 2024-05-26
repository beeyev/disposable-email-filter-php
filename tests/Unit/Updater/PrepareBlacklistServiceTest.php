<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Tests\Support\Updater\DataManipulation\ContentsManipulatorTestHelper;
use Beeyev\DisposableEmailFilter\Updater\PrepareBlacklistService;
use Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto\SourceBlacklistItemDto;
use Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto\SourceBlacklistsDto;

/**
 * @covers \Beeyev\DisposableEmailFilter\Updater\PrepareBlacklistService
 *
 * @uses   \Beeyev\DisposableEmailFilter\Updater\Support\DomainsExtractor
 * @uses   \Beeyev\DisposableEmailFilter\Updater\Support\Utils
 *
 * @internal
 */
final class PrepareBlacklistServiceTest extends AbstractTestCase
{
    public function testGetPreparedBlacklistSuccessfully(): void
    {
        $sourceBlacklistsDto = new SourceBlacklistsDto([
            new SourceBlacklistItemDto('source1', ['abc2.com', 'abc1.com']),
            new SourceBlacklistItemDto('source2', ['def1.com', 'def2.com', 'abc1.com']),
        ]);

        $whitelistContentsManipulator = new ContentsManipulatorTestHelper('def2.com');

        $prepareBlacklistService = new PrepareBlacklistService($sourceBlacklistsDto, $whitelistContentsManipulator);

        $preparedBlacklist = $prepareBlacklistService->getPreparedBlacklist();

        self::assertSame(['abc1.com', 'abc2.com', 'def1.com'], $preparedBlacklist);
    }
}
