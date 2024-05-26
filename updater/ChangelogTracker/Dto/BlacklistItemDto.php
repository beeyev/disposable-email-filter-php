<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto;

/**
 * @codeCoverageIgnore
 *
 * @internal
 */
final class BlacklistItemDto
{
    /**
     * @var non-empty-string
     * @readonly
     */
    public $sourceName;

    /**
     * @var list<non-empty-string>
     * @readonly
     */
    public $addedDomains;

    /**
     * @var list<non-empty-string>
     * @readonly
     */
    public $removedDomains;

    /**
     * @var list<non-empty-string>
     * @readonly
     */
    public $whitelistedDomains;

    /**
     * @param non-empty-string       $sourceName
     * @param list<non-empty-string> $addedDomains
     * @param list<non-empty-string> $removedDomains
     * @param list<non-empty-string> $whitelistedDomains
     */
    public function __construct(
        string $sourceName,
        array $addedDomains,
        array $removedDomains,
        array $whitelistedDomains
    ) {
        $this->sourceName = $sourceName;
        $this->addedDomains = $addedDomains;
        $this->removedDomains = $removedDomains;
        $this->whitelistedDomains = $whitelistedDomains;
    }

    public function isUpdated(): bool
    {
        return count($this->addedDomains) > 0 || count($this->removedDomains) > 0;
    }
}
