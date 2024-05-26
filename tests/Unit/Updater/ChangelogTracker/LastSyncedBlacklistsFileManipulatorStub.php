<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\ChangelogTracker;

use Beeyev\DisposableEmailFilter\Updater\ChangelogTracker\Contracts\LastSyncedBlacklistsManipulatorInterface;

/**
 * @internal
 */
final class LastSyncedBlacklistsFileManipulatorStub implements LastSyncedBlacklistsManipulatorInterface
{
    /** @var array<non-empty-string, mixed> */
    private $data;

    /**
     * @param array<non-empty-string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array<non-empty-string, mixed>
     */
    public function getContents(): array
    {
        return $this->data;
    }

    /**
     * @param non-empty-array<non-empty-string, mixed> $data
     */
    public function overrideContents(array $data): void
    {
        $this->data = $data;
    }
}
