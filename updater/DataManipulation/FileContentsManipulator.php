<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\DataManipulation;

use Beeyev\DisposableEmailFilter\Updater\Support\Utils;

/** @internal */
final class FileContentsManipulator implements ContentsManipulatorInterface
{
    /** @var non-empty-string */
    private $filePath;

    /**
     * @param non-empty-string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function get(): string
    {
        return Utils::fileGetContents($this->filePath);
    }

    public function set(string $contents): void
    {
        Utils::filePutContents($this->filePath, $contents);
    }
}
