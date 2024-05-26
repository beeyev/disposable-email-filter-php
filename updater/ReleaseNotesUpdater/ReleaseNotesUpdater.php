<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\ReleaseNotesUpdater;

use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Dto\ChangesDto;
use Beeyev\DisposableEmailFilter\Updater\DataManipulation\ContentsManipulatorInterface;

/** @internal */
final class ReleaseNotesUpdater
{
    private const MAX_DOMAINS_PREVIEW = 10;

    /**
     * @var ChangesDto
     * @readonly
     */
    private $changesDto;

    /**
     * @var ContentsManipulatorInterface
     * @readonly
     */
    private $fileContentsManipulator;

    public function __construct(ChangesDto $changesDto, ContentsManipulatorInterface $fileContentsManipulator)
    {
        $this->changesDto = $changesDto;
        $this->fileContentsManipulator = $fileContentsManipulator;
    }

    public function updateReleaseNotes(): void
    {
        $releaseNotes = $this->prepareReleaseNotes();
        $this->fileContentsManipulator->set($releaseNotes);
    }

    /**
     * @todo refactor using template engine, because current implementation is ugly as fuck
     *
     * @return non-empty-string
     */
    public function prepareReleaseNotes(): string
    {
        $md = [
            "# Disposable emails blacklist update - `{$this->changesDto->changesDateTime->format('Y-m-d')}`",
        ];

        if (count($this->changesDto->addedBlacklistSources) > 0) {
            $md[] = '';
            $md[] = '## New blacklists sources:';
            $md[] = implode(PHP_EOL, array_map(static function (string $source): string {
                return "- `{$source}`";
            }, $this->changesDto->addedBlacklistSources));
        }

        if (count($this->changesDto->removedBlacklistSources) > 0) {
            $md[] = '';
            $md[] = '## Removed blacklists sources:';
            $md[] = implode(PHP_EOL, array_map(static function (string $source): string {
                return "- `{$source}`";
            }, $this->changesDto->removedBlacklistSources));
        }

        if ($this->changesDto->isBlacklistUpdated()) {
            $md[] = '';
            $md[] = '## Updated blacklists sources:';
            foreach ($this->changesDto->blacklistItems as $blacklistItem) {
                if ($blacklistItem->isUpdated()) {
                    $md[] = sprintf('### Source: `%s`, Domains added: `%d`, removed: `%d`</summary>', $blacklistItem->sourceName, count($blacklistItem->addedDomains), count($blacklistItem->removedDomains));
                    if (count($blacklistItem->addedDomains) > 0) {
                        $md[] = '<details>';
                        $md[] = '<summary>Show added domains</summary>';
                        $md[] = '';
                        $md[] = '```';
                        $md[] = $this->getDomainsPreview($blacklistItem->addedDomains);
                        $md[] = '```';
                        $md[] = '';
                        $md[] = '</details>';
                        $md[] = '';
                    }
                    if (count($blacklistItem->removedDomains) > 0) {
                        $md[] = '<details>';
                        $md[] = '<summary>Show removed domains</summary>';
                        $md[] = '';
                        $md[] = '```';
                        $md[] = $this->getDomainsPreview($blacklistItem->removedDomains);
                        $md[] = '```';
                        $md[] = '';
                        $md[] = '</details>';
                        $md[] = '';
                    }
                    if (count($blacklistItem->whitelistedDomains) > 0) {
                        $md[] = '<details>';
                        $md[] = '<summary>Show whitelisted domains</summary>';
                        $md[] = '';
                        $md[] = '```';
                        $md[] = $this->getDomainsPreview($blacklistItem->whitelistedDomains);
                        $md[] = '```';
                        $md[] = '';
                        $md[] = '</details>';
                        $md[] = '';
                    }
                }
            }
        }

        return implode(PHP_EOL, $md);
    }

    /**
     * @param non-empty-list<non-empty-string> $domains
     *
     * @return non-empty-string
     */
    private function getDomainsPreview(array $domains): string
    {
        $result = array_slice($domains, 0, self::MAX_DOMAINS_PREVIEW);

        if (count($result) < count($domains)) {
            $result[] = '';
            $result[] = '... See full list in the source file.';
        }

        return implode(PHP_EOL, $result); // @phpstan-ignore return.type
    }
}
