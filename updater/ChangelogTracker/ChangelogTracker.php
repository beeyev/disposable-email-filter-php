<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\ChangelogTracker;

use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Contracts\LastSyncedBlacklistsManipulatorInterface;
use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto\BlacklistItemDto;
use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto\ChangesDto;
use Beeyev\DisposableEmailFilter\Updater\Shared\Whitelist;
use Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto\SourceBlacklistItemDto;
use Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto\SourceBlacklistsDto;
use Beeyev\DisposableEmailFilter\Updater\Support\Utils;

/**
 * @phpstan-type LastSyncedBlacklists array{
 *      date_time: non-empty-string,
 *      whitelisted_domains: list<non-empty-string>,
 *      blacklists: array<string, array{
 *          source_name: non-empty-string,
 *          domains: non-empty-list<non-empty-string>
 *      }>
 *  }
 *
 * @internal
 */
final class ChangelogTracker
{
    /**
     * @var LastSyncedBlacklistsManipulatorInterface
     * @readonly
     */
    private $lastSyncedBlacklistsFileManipulator;
    /**
     * @var SourceBlacklistsDto
     * @readonly
     */
    private $sourceBlacklistsDto;
    /**
     * @var Whitelist
     * @readonly
     */
    private $whitelist;

    public function __construct(LastSyncedBlacklistsManipulatorInterface $lastSyncedBlacklistsFileManipulator, SourceBlacklistsDto $sourceBlacklistsDto, Whitelist $whitelist)
    {
        $this->lastSyncedBlacklistsFileManipulator = $lastSyncedBlacklistsFileManipulator;
        $this->sourceBlacklistsDto = $sourceBlacklistsDto;
        $this->whitelist = $whitelist;
    }

    public function compareWithLastSyncedBlacklists(): ChangesDto
    {
        /** @var LastSyncedBlacklists $lastSyncedBlacklists */
        $lastSyncedBlacklists = $this->lastSyncedBlacklistsFileManipulator->getContents();
        // @todo: handle if list is empty
        assert(count($lastSyncedBlacklists) > 0, 'Last synced blacklists are empty');

        $lastSyncedBlacklistsDateTime = new \DateTimeImmutable($lastSyncedBlacklists['date_time']);

        $blacklistItems = [];
        foreach ($this->sourceBlacklistsDto->items as $sourceBlacklistItemDto) {
            $addedDomains = [];
            $removedDomains = [];
            $whitelistedDomains = $this->getWhitelistedDomains($sourceBlacklistItemDto);

            // If a Blacklist source was synced before
            if (isset($lastSyncedBlacklists['blacklists'][$sourceBlacklistItemDto->sourceName])) {
                $lastSyncedBlacklistDomains = $lastSyncedBlacklists['blacklists'][$sourceBlacklistItemDto->sourceName]['domains'];
                assert(count($lastSyncedBlacklistDomains) > 0);

                $addedDomains = $this->getAddedDomains($sourceBlacklistItemDto, $lastSyncedBlacklistDomains);
                $removedDomains = $this->getRemovedDomains($lastSyncedBlacklistDomains, $sourceBlacklistItemDto);
            }
            // If a Blacklist source is synced for the first time
            else {
                $addedDomains = $sourceBlacklistItemDto->domains;
            }

            $blacklistItems[$sourceBlacklistItemDto->sourceName] = new BlacklistItemDto(
                $sourceBlacklistItemDto->sourceName,
                $addedDomains,
                $removedDomains,
                $whitelistedDomains
            );
        }

        return new ChangesDto(
            $lastSyncedBlacklistsDateTime,
            new \DateTimeImmutable(),
            $blacklistItems,
            $this->getAddedBlacklistSources($this->sourceBlacklistsDto, $lastSyncedBlacklists),
            $this->getRemovedBlacklistSources($lastSyncedBlacklists, $this->sourceBlacklistsDto),
            $this->getOverallWhitelistedDomains(),
            $this->getNewWhitelistedDomains($lastSyncedBlacklists),
            $this->getRemovedWhitelistedDomains($lastSyncedBlacklists),
            $this->getOverallAddedDomains($lastSyncedBlacklists),
            $this->getOverallRemovedDomains($lastSyncedBlacklists)
        );
    }

    /**
     * @return list<non-empty-string>
     */
    private function getWhitelistedDomains(SourceBlacklistItemDto $sourceBlacklistItemDto): array
    {
        return array_values(array_intersect($this->whitelist->whitelist, $sourceBlacklistItemDto->domains));
    }

    /**
     * @return list<non-empty-string>
     */
    private function getOverallWhitelistedDomains(): array
    {
        static $resultCache = null;

        if ($resultCache === null) {
            $bDomains = array_unique(array_merge(...array_values(array_map(static function (SourceBlacklistItemDto $sourceBlacklistItemDto): array {
                return $sourceBlacklistItemDto->domains;
            }, $this->sourceBlacklistsDto->items))));

            $resultCache = array_values(array_intersect($this->whitelist->whitelist, $bDomains));
        }

        return $resultCache;
    }

    /**
     * @param LastSyncedBlacklists $lastSyncedBlacklists
     *
     * @return list<non-empty-string>
     */
    private function getNewWhitelistedDomains(array $lastSyncedBlacklists): array
    {
        return array_values(array_diff($this->getOverallWhitelistedDomains(), $lastSyncedBlacklists['whitelisted_domains']));
    }

    /**
     * @param LastSyncedBlacklists $lastSyncedBlacklists
     *
     * @return list<non-empty-string>
     */
    private function getRemovedWhitelistedDomains(array $lastSyncedBlacklists): array
    {
        return array_values(array_diff($lastSyncedBlacklists['whitelisted_domains'], $this->getOverallWhitelistedDomains()));
    }

    /**
     * @param LastSyncedBlacklists $lastSyncedBlacklists
     *
     * @return list<non-empty-string>
     */
    private function getOverallAddedDomains(array $lastSyncedBlacklists): array
    {
        $lastSyncedBlacklistDomains = array_unique(array_merge(...array_values(array_map(static function (array $blacklist): array {
            return $blacklist['domains'];
        }, $lastSyncedBlacklists['blacklists']))));

        $bDomains = array_unique(array_merge(...array_values(array_map(static function (SourceBlacklistItemDto $sourceBlacklistItemDto): array {
            return $sourceBlacklistItemDto->domains;
        }, $this->sourceBlacklistsDto->items))));

        $result = array_diff($bDomains, $lastSyncedBlacklistDomains);

        return Utils::naturalSort($result);
    }

    /**
     * @param LastSyncedBlacklists $lastSyncedBlacklists
     *
     * @return list<non-empty-string>
     */
    private function getOverallRemovedDomains(array $lastSyncedBlacklists): array
    {
        $lastSyncedBlacklistDomains = array_unique(array_merge(...array_values(array_map(static function (array $blacklist): array {
            return $blacklist['domains'];
        }, $lastSyncedBlacklists['blacklists']))));

        $bDomains = array_unique(array_merge(...array_values(array_map(static function (SourceBlacklistItemDto $sourceBlacklistItemDto): array {
            return $sourceBlacklistItemDto->domains;
        }, $this->sourceBlacklistsDto->items))));

        $result = array_diff($lastSyncedBlacklistDomains, $bDomains);

        return Utils::naturalSort($result);
    }

    /**
     * @param LastSyncedBlacklists $lastSyncedBlacklists
     *
     * @return list<non-empty-string>
     */
    private function getAddedBlacklistSources(SourceBlacklistsDto $sourceBlacklistsDto, array $lastSyncedBlacklists): array
    {
        $addedBlacklistSources = [];

        foreach ($this->sourceBlacklistsDto->items as $sourceBlacklistItemDto) {
            if (!isset($lastSyncedBlacklists['blacklists'][$sourceBlacklistItemDto->sourceName])) {
                $addedBlacklistSources[] = $sourceBlacklistItemDto->sourceName;
            }
        }

        return $addedBlacklistSources;
    }

    /**
     * @param LastSyncedBlacklists $lastSyncedBlacklists
     *
     * @return list<non-empty-string>
     */
    private function getRemovedBlacklistSources(array $lastSyncedBlacklists, SourceBlacklistsDto $sourceBlacklistsDto): array
    {
        $removedBlacklistSources = [];

        $sourceBlacklistNames = array_map(static function (SourceBlacklistItemDto $sourceBlacklistItemDto): string {
            return $sourceBlacklistItemDto->sourceName;
        }, $sourceBlacklistsDto->items);

        foreach ($lastSyncedBlacklists['blacklists'] as $blacklist) {
            if (!in_array($blacklist['source_name'], $sourceBlacklistNames, true)) {
                $removedBlacklistSources[] = $blacklist['source_name'];
            }
        }

        return $removedBlacklistSources;
    }

    /**
     * @param non-empty-list<non-empty-string> $lastSyncedDomains
     *
     * @return list<non-empty-string>
     */
    private function getAddedDomains(SourceBlacklistItemDto $sourceBlacklistItemDto, array $lastSyncedDomains): array
    {
        return array_values(array_diff($sourceBlacklistItemDto->domains, $lastSyncedDomains));
    }

    /**
     * @param non-empty-list<non-empty-string> $lastSyncedDomains
     *
     * @return list<non-empty-string>
     */
    private function getRemovedDomains(array $lastSyncedDomains, SourceBlacklistItemDto $sourceBlacklistItemDto): array
    {
        return array_values(array_diff($lastSyncedDomains, $sourceBlacklistItemDto->domains));
    }

    public function updateLastSyncedBlacklists(): void
    {
        /** @var LastSyncedBlacklists $result */
        $result = [
            'date_time' => $this->sourceBlacklistsDto->dateTime->format(\DateTimeInterface::ATOM),
            'blacklists' => [],
            'whitelisted_domains' => $this->getOverallWhitelistedDomains(),
        ];

        foreach ($this->sourceBlacklistsDto->items as $sourceBlacklistItemDto) {
            $result['blacklists'][$sourceBlacklistItemDto->sourceName] = [
                'source_name' => $sourceBlacklistItemDto->sourceName,
                'domains' => $sourceBlacklistItemDto->domains,
            ];
        }

        $this->lastSyncedBlacklistsFileManipulator->overrideContents($result);
    }
}
