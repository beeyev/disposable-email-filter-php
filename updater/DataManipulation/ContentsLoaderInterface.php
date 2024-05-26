<?php
/**
 * @author Alexander Tebiev
 *
 * @see https://github.com/beeyev
 */

namespace Beeyev\DisposableEmailFilter\Updater\DataManipulation;

/** @internal */
interface ContentsLoaderInterface
{
    /**
     * @param non-empty-string $filePath
     *
     * @throws \RuntimeException
     */
    public function load(string $filePath): string;
}
