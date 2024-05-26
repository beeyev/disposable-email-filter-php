<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Support\Updater\DataManipulation;

use Beeyev\DisposableEmailFilter\Updater\DataManipulation\ContentsManipulatorInterface;

/**
 * @internal
 */
final class ContentsManipulatorTestHelper implements ContentsManipulatorInterface
{
    /** @var string */
    private $contents;

    public function __construct(string $contents)
    {
        $this->contents = $contents;
    }

    public function get(): string
    {
        return $this->contents;
    }

    public function set(string $contents): void
    {
        $this->contents = $contents;
    }
}
