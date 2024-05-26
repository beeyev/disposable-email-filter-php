<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\DataManipulation;

use Beeyev\DisposableEmailFilter\Updater\Support\Utils;

/** @internal */
final class FileContentsLoader implements ContentsLoaderInterface
{
    /** {@inheritdoc} */
    public function load(string $filePath): string
    {
        return Utils::fileGetContents($filePath);
    }
}
