<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\Shared;

use Beeyev\DisposableEmailFilter\Updater\DataManipulation\ContentsManipulatorInterface;
use Beeyev\DisposableEmailFilter\Updater\Support\DomainsExtractor;

/** @internal */
final class Whitelist
{
    /**
     * @var list<non-empty-string>
     * @readonly
     */
    public $whitelist;

    /**
     * @param list<non-empty-string> $whitelist
     */
    public function __construct(array $whitelist)
    {
        $this->whitelist = $whitelist;
    }

    public static function fromContents(ContentsManipulatorInterface $whitelistContentsManipulator): self
    {
        $whitelistContents = $whitelistContentsManipulator->get();
        assert($whitelistContents !== '');

        return new self(DomainsExtractor::toArray($whitelistContents));
    }
}
