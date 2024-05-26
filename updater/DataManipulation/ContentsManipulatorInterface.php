<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */

namespace Beeyev\DisposableEmailFilter\Updater\DataManipulation;

/** @internal */
interface ContentsManipulatorInterface
{
    public function get(): string;

    /**
     * @param non-empty-string $contents
     */
    public function set(string $contents): void;
}
