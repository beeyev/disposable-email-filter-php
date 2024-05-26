<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\ReleaseNotesUpdater;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Tests\Support\Updater\DataManipulation\ContentsManipulatorTestHelper;
use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto\BlacklistItemDto;
use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto\ChangesDto;
use Beeyev\DisposableEmailFilter\Updater\ReleaseNotesUpdater\ReleaseNotesUpdater;

/**
 * @todo Complete the test
 *
 * @covers \Beeyev\DisposableEmailFilter\Updater\ReleaseNotesUpdater\ReleaseNotesUpdater
 *
 * @uses \Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto\ChangesDto
 *
 * @internal
 */
final class ReleaseNotesUpdaterTest extends AbstractTestCase
{
    public function testUpdateReleaseNotes(): void
    {
        $changesDto = new ChangesDto(
            (new \DateTimeImmutable())->modify('-1 day'),
            new \DateTimeImmutable(),
            $this->getBlacklistItems(),
            ['sourceName-1', 'sourceName-2'],
            ['sourceName-5', 'sourceName-6'],
            ['whitelistedDomain1-1.com', 'whitelistedDomain4-1.com'],
            ['whitelistedDomain1-2.com', 'whitelistedDomain2-1.com'],
            ['whitelistedDomain3-1.com', 'whitelistedDomain3-2.com'],
            ['addedDomain1-1.com', 'addedDomain1-2.com', 'addedDomain1-3.com', 'addedDomain2-1.com', 'addedDomain2-2.com'],
            ['removedDomain1-1.com', 'removedDomain1-2.com', 'removedDomain3-1.com', 'removedDomain3-2.com']
        );

        $contentsManipulatorTestHelper = new ContentsManipulatorTestHelper('');

        $releaseNotesUpdater = new ReleaseNotesUpdater($changesDto, $contentsManipulatorTestHelper);
        $releaseNotesUpdater->updateReleaseNotes();

        self::assertStringContainsString('Disposable emails blacklist update', $contentsManipulatorTestHelper->get());
    }

    /**
     * @return array<non-empty-string, BlacklistItemDto>
     */
    private function getBlacklistItems(): array
    {
        return [
            'sourceName-1' => new BlacklistItemDto(
                'sourceName-1',
                [
                    'addedDomain1-1.com',
                    'addedDomain1-2.com',
                    'addedDomain1-3.com',
                    'addedDomain1-4.com',
                    'addedDomain1-5.com',
                    'addedDomain1-6.com',
                    'addedDomain1-7.com',
                    'addedDomain1-8.com',
                    'addedDomain1-9.com',
                    'addedDomain1-10.com',
                    'addedDomain1-11.com',
                ],
                [
                    'removedDomain1-1.com',
                    'removedDomain1-2.com',
                ],
                [
                    'whitelistedDomain1-1.com',
                ]
            ),
            'sourceName-2' => new BlacklistItemDto(
                'sourceName-2',
                [
                    'addedDomain2-1.com',
                    'addedDomain2-2.com',
                ],
                [],
                []
            ),
            'sourceName-3' => new BlacklistItemDto(
                'sourceName-3',
                [],
                [
                    'removedDomain3-1.com',
                    'removedDomain3-2.com',
                ],
                []
            ),
            'sourceName-4' => new BlacklistItemDto(
                'sourceName-4',
                [],
                [],
                [
                    'whitelistedDomain4-1.com',
                ]
            ),
        ];
    }
}
