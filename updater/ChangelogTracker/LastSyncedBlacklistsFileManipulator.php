<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\ChangelogTracker;

use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Contracts\LastSyncedBlacklistsManipulatorInterface;
use Beeyev\DisposableEmailFilter\Updater\Support\Utils;

/** @internal */
final class LastSyncedBlacklistsFileManipulator implements LastSyncedBlacklistsManipulatorInterface
{
    /**
     * @var non-empty-string
     * @readonly
     */
    private $filePath;

    /**
     * @param non-empty-string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return array<non-empty-string, mixed>
     */
    public function getContents(): array
    {
        $fileContents = Utils::fileGetContents($this->filePath);

        return Utils::jsonDecode($fileContents);
    }

    /**
     * @param non-empty-array<non-empty-string, mixed> $data
     */
    public function overrideContents(array $data): void
    {
        $dataJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        assert($dataJson !== false);

        Utils::filePutContents($this->filePath, $dataJson);
    }
}
