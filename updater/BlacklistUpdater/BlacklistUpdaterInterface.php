<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater;

/** @internal */
interface BlacklistUpdaterInterface
{
    /**
     * @param non-empty-list<non-empty-string> $blacklist
     */
    public function update(array $blacklist): void;
}
