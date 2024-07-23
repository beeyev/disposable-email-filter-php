<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater;

/** @internal */
final class Config
{
    public const LOCAL_BLACKLIST_NAME = 'local';
    public const LOCAL_BLACKLIST_PATH = __DIR__ . '/../blacklist.txt';

    public const SOURCE_BLACKLISTS_PATH = [
        self::LOCAL_BLACKLIST_NAME => self::LOCAL_BLACKLIST_PATH,
        'github.com/7c/fakefilter' => 'https://raw.githubusercontent.com/7c/fakefilter/main/txt/data.txt',
        'github.com/FGRibreau/mailchecker' => 'https://raw.githubusercontent.com/FGRibreau/mailchecker/master/list.txt',
        'github.com/disposable-email-domains/disposable-email-domains' => 'https://raw.githubusercontent.com/disposable-email-domains/disposable-email-domains/master/disposable_email_blocklist.conf',
        'github.com/unkn0w/disposable-email-domain-list' => 'https://raw.githubusercontent.com/unkn0w/disposable-email-domain-list/main/domains.txt',
    ];

    public const LAST_SYNCED_BLACKLISTS_DATA_FILEPATH = __DIR__ . '/ChangelogTracker/last-synced-blacklists-data.json';
    public const README_FILEPATH = __DIR__ . '/../README.md';
    public const INFO_POINTER_FROM = '### Last changes:';
    public const INFO_POINTER_TO = '___';
    public const GENERATED_RELEASE_NOTES_FILE_PATH = __DIR__ . '/ReleaseNotesUpdater/release_notes.md';
    public const JSON_BLACKLIST_PATH = __DIR__ . '/../disposable_email_domains.json';
    public const TXT_BLACKLIST_PATH = __DIR__ . '/../disposable_email_domains.txt';
    public const PHP_BLACKLIST_PATH = __DIR__ . '/../src/DisposableEmailDomains/DisposableEmailDomainsList.php.data';
    public const WHITELIST_PATH = __DIR__ . '/../whitelist.txt';
}
