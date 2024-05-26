<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers;

/** @internal */
interface PhpBlacklistStyleFixerCodeCheckerInterface
{
    public function fixAndCheck(): void;
}
