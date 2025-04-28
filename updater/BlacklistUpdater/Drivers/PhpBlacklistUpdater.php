<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers;

use Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\BlacklistUpdaterInterface;
use Beeyev\DisposableEmailFilter\Updater\DataManipulation\ContentsManipulatorInterface;

/** @internal */
final class PhpBlacklistUpdater implements BlacklistUpdaterInterface
{
    /**
     * @var ContentsManipulatorInterface
     * @readonly
     */
    private $fileContentsManipulator;

    /**
     * @var PhpBlacklistStyleFixerCodeCheckerInterface
     * @readonly
     */
    private $phpBlacklistStyleFixerCodeChecker;

    public function __construct(ContentsManipulatorInterface $fileContentsManipulator, PhpBlacklistStyleFixerCodeCheckerInterface $phpBlacklistStyleFixerCodeChecker)
    {
        $this->fileContentsManipulator = $fileContentsManipulator;
        $this->phpBlacklistStyleFixerCodeChecker = $phpBlacklistStyleFixerCodeChecker;
    }

    /**
     * @param non-empty-list<non-empty-string> $blacklist
     */
    public function update(array $blacklist): void
    {
        $this->fileContentsManipulator->set($this->prepareContents($blacklist));

        $this->phpBlacklistStyleFixerCodeChecker->fixAndCheck();
    }

    /**
     * @param non-empty-list<non-empty-string> $blacklist
     *
     * @return non-empty-string
     */
    private function prepareContents(array $blacklist): string
    {
        $domains = array_flip($blacklist);
        $domains = array_map(static function (): bool {
            return true;
        }, $domains);

        $currentDate = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $returnData = [
            'updated_at' => $currentDate,
            'disposable_email_domains' => $domains,
        ];

        // @todo: replace with a proper template engine
        $contents = [
            '<?php',
            '/**',
            ' * @author Alexander Tebiev - https://github.com/beeyev',
            ' * @link https://github.com/beeyev/disposable-email-filter-php',
            ' *',
            " * File updated at: {$currentDate->format('Y-m-d H:i:s (T)')}",
            ' */',
            'return ' . var_export($returnData, true) . ';',
        ];

        return implode(PHP_EOL, $contents);
    }
}
