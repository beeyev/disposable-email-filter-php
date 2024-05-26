<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers;

use Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\BlacklistUpdaterInterface;
use Beeyev\DisposableEmailFilter\Updater\DataManipulation\ContentsManipulatorInterface;

/** @internal */
final class TxtBlacklistUpdater implements BlacklistUpdaterInterface
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
        $txtContents = implode(PHP_EOL, $blacklist);
        $this->fileContentsManipulator->set($txtContents);
    }
}
