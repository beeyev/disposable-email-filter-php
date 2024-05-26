<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader;

use Beeyev\DisposableEmailFilter\Updater\DataManipulation\ContentsLoaderInterface;
use Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto\SourceBlacklistItemDto;
use Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto\SourceBlacklistsDto;
use Beeyev\DisposableEmailFilter\Updater\Support\DomainsExtractor;

/** @internal */
final class SourceBlacklistsLoader
{
    /** @var ContentsLoaderInterface */
    private $contentsLoader;

    /** @var non-empty-array<non-empty-string, non-empty-string> */
    private $sourceBlacklistsPath;

    /**
     * @param non-empty-array<non-empty-string, non-empty-string> $sourceBlacklistsPath
     */
    public function __construct(ContentsLoaderInterface $contentsLoader, array $sourceBlacklistsPath)
    {
        $this->contentsLoader = $contentsLoader;
        $this->sourceBlacklistsPath = $sourceBlacklistsPath;
    }

    public function load(): SourceBlacklistsDto
    {
        $items = [];

        foreach ($this->sourceBlacklistsPath as $sourceName => $filepath) {
            $fileContents = $this->contentsLoader->load($filepath);
            if ($fileContents === '') {
                throw new \RuntimeException("Source blacklist `{$sourceName}` file is empty: `{$filepath}`");
            }

            $domains = DomainsExtractor::toArray($fileContents);
            if (count($domains) === 0) {
                throw new \RuntimeException("Source blacklist `{$sourceName}` file: `{$filepath}`, does not contain any domains");
            }

            $items[] = new SourceBlacklistItemDto(
                $sourceName,
                $domains
            );
        }

        return new SourceBlacklistsDto($items);
    }
}
