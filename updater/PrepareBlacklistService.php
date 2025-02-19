<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater;

use Beeyev\DisposableEmailFilter\Updater\DataManipulation\ContentsManipulatorInterface;
use Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto\SourceBlacklistsDto;
use Beeyev\DisposableEmailFilter\Updater\Support\DomainsExtractor;
use Beeyev\DisposableEmailFilter\Updater\Support\Utils;

/** @internal */
final class PrepareBlacklistService
{
    /**
     * @var SourceBlacklistsDto
     * @readonly
     */
    private $sourceBlacklistsDto;

    /**
     * @var ContentsManipulatorInterface
     * @readonly
     */
    private $whitelistContentsManipulator;

    public function __construct(SourceBlacklistsDto $sourceBlacklistsDto, ContentsManipulatorInterface $whitelistContentsManipulator)
    {
        $this->sourceBlacklistsDto = $sourceBlacklistsDto;
        $this->whitelistContentsManipulator = $whitelistContentsManipulator;
    }

    /**
     * @return non-empty-list<non-empty-string>
     */
    public function getPreparedBlacklist(): array
    {
        $whitelistData = $this->getWhitelistData();
        $blacklistData = $this->getUniqueBlacklistDomains();

        $result = array_diff($blacklistData, $whitelistData);
        assert(count($result) > 0);

        return Utils::naturalSort($result);
    }

    /**
     * @return list<non-empty-string>
     */
    private function getWhitelistData(): array
    {
        $whitelistContents = $this->whitelistContentsManipulator->get();
        assert($whitelistContents !== '');

        return DomainsExtractor::toArray($whitelistContents);
    }

    /**
     * @return non-empty-list<non-empty-string>
     */
    private function getUniqueBlacklistDomains(): array
    {
        $whitelistContents = $this->whitelistContentsManipulator->get();
        assert($whitelistContents !== '');

        $bDomains = [];
        foreach ($this->sourceBlacklistsDto->items as $sourceBlacklistItemDto) {
            $bDomains[] = $sourceBlacklistItemDto->domains;
        }

        $domains = array_unique(array_merge(...$bDomains));
        $domains = array_values($domains);
        assert(count($domains) > 0);
        /* @var non-empty-list<non-empty-string> $domains */
        return $domains;
    }
}
