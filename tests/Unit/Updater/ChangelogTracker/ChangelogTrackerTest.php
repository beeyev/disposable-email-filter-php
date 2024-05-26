<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\ChangelogTracker;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\ChangelogTracker;
use Beeyev\DisposableEmailFilter\Updater\Shared\Whitelist;
use Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto\SourceBlacklistItemDto;
use Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto\SourceBlacklistsDto;
use PHPUnit\Framework\Assert;

/**
 * @covers \Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\ChangelogTracker
 *
 * @uses  \Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto\ChangesDto
 * @uses  \Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto\BlacklistItemDto
 * @uses  \Beeyev\DisposableEmailFilter\Updater\Shared\Whitelist
 * @uses  \Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto\SourceBlacklistItemDto
 * @uses  \Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto\SourceBlacklistsDto
 *
 * @internal
 */
final class ChangelogTrackerTest extends AbstractTestCase
{
    public function testNewBlacklistSourcesAdded(): void
    {
        $emptyLastSyncedBlacklistsData = $this->getEmptyLastSyncedBlacklistsData();
        $blacklists = $this->getBlacklists();

        $lastSyncedBlacklistsFileManipulatorStub = new LastSyncedBlacklistsFileManipulatorStub($emptyLastSyncedBlacklistsData);
        $changelogTracker = new ChangelogTracker($lastSyncedBlacklistsFileManipulatorStub, $blacklists, $this->getWhitelist());
        $changesDto = $changelogTracker->compareWithLastSyncedBlacklists();

        Assert::assertEquals(new \DateTimeImmutable($emptyLastSyncedBlacklistsData['date_time']), $changesDto->lastSyncedBlacklistsDateTime); // @phpstan-ignore argument.type
        Assert::assertEqualsWithDelta(new \DateTimeImmutable(), $changesDto->changesDateTime, 1);
        Assert::assertCount(count($emptyLastSyncedBlacklistsData['blacklists']), $changesDto->blacklistItems); // @phpstan-ignore argument.type

        Assert::assertSame(['whitelist2.local'], $changesDto->newWhitelistedDomains);
        Assert::assertSame(['whitelist4.local'], $changesDto->removedWhitelistedDomains);

        Assert::assertSame('bl_source1', $changesDto->blacklistItems['bl_source1']->sourceName);
        Assert::assertSame([
            'bl_source1-2.local',
            'bl_source1-3.local',
            'same-domain-1.local',
        ], $changesDto->blacklistItems['bl_source1']->addedDomains);
        Assert::assertSame([
            'bl_source1-to-remove-1.local',
        ], $changesDto->blacklistItems['bl_source1']->removedDomains);
        Assert::assertSame([
            'whitelist1.local',
        ], $changesDto->blacklistItems['bl_source1']->whitelistedDomains);

        Assert::assertSame('bl_source2', $changesDto->blacklistItems['bl_source2']->sourceName);
        Assert::assertSame([
            'bl_source2-1.local',
            'bl_source2-2.local',
            'bl_source2-3.local',
            'whitelist2.local',
            'same-domain-1.local',
        ], $changesDto->blacklistItems['bl_source2']->addedDomains);
        Assert::assertEmpty($changesDto->blacklistItems['bl_source2']->removedDomains);
        Assert::assertSame([
            'whitelist2.local',
        ], $changesDto->blacklistItems['bl_source2']->whitelistedDomains);

        Assert::assertSame('bl_source3', $changesDto->blacklistItems['bl_source3']->sourceName);
        Assert::assertEmpty($changesDto->blacklistItems['bl_source3']->addedDomains);
        Assert::assertEmpty($changesDto->blacklistItems['bl_source3']->removedDomains);
        Assert::assertEmpty($changesDto->blacklistItems['bl_source3']->whitelistedDomains);

        Assert::assertSame(['bl_source2'], $changesDto->addedBlacklistSources);
        Assert::assertSame(['bl-source-to-remove'], $changesDto->removedBlacklistSources);
        Assert::assertSame([
            'whitelist1.local',
            'whitelist2.local',
        ], $changesDto->overallWhitelistedDomains);

        Assert::assertSame([
            'bl_source1-2.local',
            'bl_source1-3.local',
            'bl_source2-1.local',
            'bl_source2-2.local',
            'bl_source2-3.local',
            'same-domain-1.local',
            'whitelist2.local',
        ], $changesDto->overallAddedDomains);

        Assert::assertSame([
            'bl-source-to-remove-1.local',
            'bl_source1-to-remove-1.local',
        ], $changesDto->overallRemovedDomains);
    }

    public function testUpdateLastSyncedBlacklists(): void
    {
        $emptyLastSyncedBlacklistsData = $this->getEmptyLastSyncedBlacklistsData();
        $blacklists = $this->getBlacklists();

        $lastSyncedBlacklistsFileManipulatorStub = new LastSyncedBlacklistsFileManipulatorStub($emptyLastSyncedBlacklistsData);
        $changelogTracker = new ChangelogTracker($lastSyncedBlacklistsFileManipulatorStub, $blacklists, $this->getWhitelist());
        $changelogTracker->updateLastSyncedBlacklists();

        $newContents = $lastSyncedBlacklistsFileManipulatorStub->getContents();

        Assert::assertEqualsWithDelta(new \DateTimeImmutable(), new \DateTimeImmutable($newContents['date_time']), 1); /** @phpstan-ignore argument.type */
        $expectedDateTime = (new \DateTimeImmutable($newContents['date_time']))->format(\DateTimeInterface::ATOM); // @phpstan-ignore argument.type

        Assert::assertSame([
            'date_time' => $expectedDateTime,
            'blacklists' => [
                'bl_source1' => [
                    'source_name' => 'bl_source1',
                    'domains' => [
                        'bl_source1-1.local',
                        'bl_source1-2.local',
                        'bl_source1-3.local',
                        'whitelist1.local',
                        'same-domain-1.local',
                    ],
                ],
                'bl_source2' => [
                    'source_name' => 'bl_source2',
                    'domains' => [
                        'bl_source2-1.local',
                        'bl_source2-2.local',
                        'bl_source2-3.local',
                        'whitelist2.local',
                        'same-domain-1.local',
                    ],
                ],
                'bl_source3' => [
                    'source_name' => 'bl_source3',
                    'domains' => [
                        'bl_source3-1.local',
                    ],
                ],
            ],
            'whitelisted_domains' => [
                'whitelist1.local',
                'whitelist2.local',
            ],
        ], $newContents);
    }

    private function getBlacklists(): SourceBlacklistsDto
    {
        return new SourceBlacklistsDto(
            [
                new SourceBlacklistItemDto('bl_source1', [
                    'bl_source1-1.local',
                    'bl_source1-2.local',
                    'bl_source1-3.local',
                    'whitelist1.local',
                    'same-domain-1.local',
                ]),
                new SourceBlacklistItemDto('bl_source2', [
                    'bl_source2-1.local',
                    'bl_source2-2.local',
                    'bl_source2-3.local',
                    'whitelist2.local',
                    'same-domain-1.local',
                ]),
                new SourceBlacklistItemDto('bl_source3', [
                    'bl_source3-1.local',
                ]),
            ]
        );
    }

    /**
     * @return array<mixed>
     */
    private function getEmptyLastSyncedBlacklistsData(): array
    {
        return [
            'date_time' => '2024-04-14T10:43:04+00:00',
            'whitelisted_domains' => [
                'whitelist1.local',
                'whitelist4.local',
            ],
            'blacklists' => [
                'bl-source-to-remove' => [
                    'source_name' => 'bl-source-to-remove',
                    'domains' => [
                        'bl-source-to-remove-1.local',
                    ],
                ],
                'bl_source1' => [
                    'source_name' => 'bl_source1',
                    'domains' => [
                        'bl_source1-1.local',
                        'bl_source1-to-remove-1.local',
                        'whitelist1.local',
                    ],
                ],
                'bl_source3' => [
                    'source_name' => 'bl_source3',
                    'domains' => [
                        'bl_source3-1.local',
                    ],
                ],
            ],
        ];
    }

    private function getWhitelist(): Whitelist
    {
        return new Whitelist([
            'whitelist1.local',
            'whitelist2.local',
            'whitelist3.local',
            'whitelist4.local',
        ]);
    }
}
