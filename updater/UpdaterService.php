<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater;

use Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\BlacklistUpdaterService;
use Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers\JsonBlacklistUpdater;
use Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers\PhpBlacklistStyleFixerCodeChecker;
use Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers\PhpBlacklistUpdater;
use Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers\TxtBlacklistUpdater;
use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\ChangelogTracker;
use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\LastSyncedBlacklistsFileManipulator;
use Beeyev\DisposableEmailFilter\Updater\DataManipulation\FileContentsLoader;
use Beeyev\DisposableEmailFilter\Updater\DataManipulation\FileContentsManipulator;
use Beeyev\DisposableEmailFilter\Updater\ReleaseNotesUpdater\ReleaseNotesUpdater;
use Beeyev\DisposableEmailFilter\Updater\Shared\Whitelist;
use Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\SourceBlacklistsLoader;

/** @internal */
final class UpdaterService
{
    public function execute(): void
    {
        $sourceBlacklistsDto = (new SourceBlacklistsLoader(new FileContentsLoader(), Config::SOURCE_BLACKLISTS_PATH))->load();
        $whitelist = Whitelist::fromContents(new FileContentsManipulator(Config::WHITELIST_PATH));

        $changelogTracker = new ChangelogTracker(new LastSyncedBlacklistsFileManipulator(Config::LAST_SYNCED_BLACKLISTS_DATA_FILEPATH), $sourceBlacklistsDto, $whitelist);
        $changesDto = $changelogTracker->compareWithLastSyncedBlacklists();
        if ($changesDto->hasAnyChanges() === false) {
            echo PHP_EOL . 'No changes detected. Exiting...' . PHP_EOL;

            exit(1);
        }

        $changelogTracker->updateLastSyncedBlacklists();

        $releaseNotesUpdater = new ReleaseNotesUpdater($changesDto, new FileContentsManipulator(Config::GENERATED_RELEASE_NOTES_FILE_PATH));
        $releaseNotesUpdater->updateReleaseNotes();

        $preparedBlacklistData = (new PrepareBlacklistService($sourceBlacklistsDto, new FileContentsManipulator(Config::WHITELIST_PATH)))->getPreparedBlacklist();
        (new BlacklistUpdaterService($preparedBlacklistData))
            ->update(new TxtBlacklistUpdater(new FileContentsManipulator(Config::TXT_BLACKLIST_PATH)))
            ->update(new JsonBlacklistUpdater(new FileContentsManipulator(Config::JSON_BLACKLIST_PATH)))
            ->update(
                new PhpBlacklistUpdater(
                    new FileContentsManipulator(Config::PHP_BLACKLIST_PATH),
                    new PhpBlacklistStyleFixerCodeChecker(Config::PHP_BLACKLIST_PATH)
                )
            );

        exit(0);
    }
}
