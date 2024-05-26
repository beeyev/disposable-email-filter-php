<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto;

/** @internal */
final class ChangesDto
{
    /**
     * @var \DateTimeInterface
     * @readonly
     */
    public $lastSyncedBlacklistsDateTime;

    /**
     * @var \DateTimeInterface
     * @readonly
     */
    public $changesDateTime;

    /**
     * @var array<non-empty-string, BlacklistItemDto>
     * @readonly
     */
    public $blacklistItems;

    /**
     * @var list<non-empty-string>
     * @readonly
     */
    public $addedBlacklistSources;

    /**
     * @var list<non-empty-string>
     * @readonly
     */
    public $removedBlacklistSources;

    /**
     * @var list<non-empty-string>
     * @readonly
     */
    public $overallWhitelistedDomains;

    /**
     * @var list<non-empty-string>
     * @readonly
     */
    public $newWhitelistedDomains;

    /**
     * @var list<non-empty-string>
     * @readonly
     */
    public $removedWhitelistedDomains;

    /**
     * @var list<non-empty-string>
     * @readonly
     */
    public $overallAddedDomains;

    /**
     * @var list<non-empty-string>
     * @readonly
     */
    public $overallRemovedDomains;

    /**
     * @param array<non-empty-string, BlacklistItemDto> $blacklistItems
     * @param list<non-empty-string>                    $addedBlacklistSources
     * @param list<non-empty-string>                    $removedBlacklistSources
     * @param list<non-empty-string>                    $overallWhitelistedDomains
     * @param list<non-empty-string>                    $newWhitelistedDomains
     * @param list<non-empty-string>                    $removedWhitelistedDomains
     * @param list<non-empty-string>                    $overallAddedDomains
     * @param list<non-empty-string>                    $overallRemovedDomains
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        \DateTimeInterface $lastSyncedBlacklistsDateTime,
        \DateTimeInterface $changesDateTime,
        array $blacklistItems,
        array $addedBlacklistSources,
        array $removedBlacklistSources,
        array $overallWhitelistedDomains,
        array $newWhitelistedDomains,
        array $removedWhitelistedDomains,
        array $overallAddedDomains,
        array $overallRemovedDomains
    ) {
        $this->lastSyncedBlacklistsDateTime = $lastSyncedBlacklistsDateTime;
        $this->changesDateTime = $changesDateTime;
        $this->blacklistItems = $blacklistItems;
        $this->addedBlacklistSources = $addedBlacklistSources;
        $this->removedBlacklistSources = $removedBlacklistSources;
        $this->overallWhitelistedDomains = $overallWhitelistedDomains;
        $this->newWhitelistedDomains = $newWhitelistedDomains;
        $this->removedWhitelistedDomains = $removedWhitelistedDomains;
        $this->overallAddedDomains = $overallAddedDomains;
        $this->overallRemovedDomains = $overallRemovedDomains;
    }

    public function hasAnyChanges(): bool
    {
        if (
            count($this->addedBlacklistSources) > 0
            || count($this->removedBlacklistSources) > 0
            || count($this->newWhitelistedDomains) > 0
            || count($this->removedWhitelistedDomains) > 0
        ) {
            return true;
        }

        return $this->isBlacklistUpdated();
    }

    public function isBlacklistUpdated(): bool
    {
        foreach ($this->blacklistItems as $blacklistItem) {
            if ($blacklistItem->isUpdated()) {
                return true;
            }
        }

        return false;
    }
}
