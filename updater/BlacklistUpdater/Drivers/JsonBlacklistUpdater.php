<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers;

use Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\BlacklistUpdaterInterface;
use Beeyev\DisposableEmailFilter\Updater\DataManipulation\ContentsManipulatorInterface;

/** @internal */
final class JsonBlacklistUpdater implements BlacklistUpdaterInterface
{
    /**
     * @var ContentsManipulatorInterface
     * @readonly
     */
    private $fileContentsManipulator;

    public function __construct(ContentsManipulatorInterface $fileContentsManipulator)
    {
        $this->fileContentsManipulator = $fileContentsManipulator;
    }

    /**
     * @param non-empty-list<non-empty-string> $blacklist
     */
    public function update(array $blacklist): void
    {
        $jsonContents = json_encode($blacklist, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        assert($jsonContents !== false);

        $this->fileContentsManipulator->set($jsonContents);
    }
}
