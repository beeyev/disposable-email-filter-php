<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\ChangelogTracker\Dto;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto\BlacklistItemDto;
use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto\ChangesDto;

/**
 * @covers \Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto\ChangesDto
 *
 * @uses  \Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto\BlacklistItemDto
 *
 * @internal
 */
final class ChangesDtoTest extends AbstractTestCase
{
    /**
     * @dataProvider hasChangesProvider
     */
    public function testHasChanges(ChangesDto $changesDto, bool $expectedResult): void
    {
        self::assertSame($expectedResult, $changesDto->hasAnyChanges());
    }

    /**
     * @return array<mixed>
     */
    public static function hasChangesProvider(): array
    {
        return [
            'no changes' => [
                new ChangesDto(
                    new \DateTimeImmutable(),
                    new \DateTimeImmutable(),
                    [new BlacklistItemDto('source1', [], [], [])], // @phpstan-ignore argument.type
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    []
                ),
                false,
            ],
            'black list updated' => [
                new ChangesDto(
                    new \DateTimeImmutable(),
                    new \DateTimeImmutable(),
                    [new BlacklistItemDto('source1', ['domain1.local', 'domain2.local'], [], [])], // @phpstan-ignore argument.type
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    []
                ),
                true,
            ],
            'added blacklist source' => [
                new ChangesDto(
                    new \DateTimeImmutable(),
                    new \DateTimeImmutable(),
                    [],
                    ['source1'],
                    [],
                    [],
                    [],
                    [],
                    [],
                    []
                ),
                true,
            ],
            'removed blacklist source' => [
                new ChangesDto(
                    new \DateTimeImmutable(),
                    new \DateTimeImmutable(),
                    [],
                    [],
                    ['source1'],
                    [],
                    [],
                    [],
                    [],
                    []
                ),
                true,
            ],
            'new whitelisted domain' => [
                new ChangesDto(
                    new \DateTimeImmutable(),
                    new \DateTimeImmutable(),
                    [],
                    [],
                    [],
                    [],
                    ['domain1.local'],
                    [],
                    [],
                    []
                ),
                true,
            ],
            'removed whitelisted domain' => [
                new ChangesDto(
                    new \DateTimeImmutable(),
                    new \DateTimeImmutable(),
                    [],
                    [],
                    [],
                    [],
                    [],
                    ['domain1.local'],
                    [],
                    []
                ),
                true,
            ],
        ];
    }
}
