<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Contracts;

/** @internal */
interface LastSyncedBlacklistsManipulatorInterface
{
    /**
     * @return array<non-empty-string, mixed>
     */
    public function getContents(): array;

    /**
     * @param non-empty-array<non-empty-string, mixed> $data
     */
    public function overrideContents(array $data): void;
}
