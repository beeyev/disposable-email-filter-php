<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater;

/**
 * @codeCoverageIgnore
 *
 * @internal
 */
final class BlacklistUpdaterService
{
    /**
     * @var non-empty-list<non-empty-string>
     * @readonly
     */
    private $blacklist;

    /**
     * @param non-empty-list<non-empty-string> $blacklist
     */
    public function __construct(array $blacklist)
    {
        $this->blacklist = $blacklist;
    }

    public function update(BlacklistUpdaterInterface $blacklistUpdater): self
    {
        $blacklistUpdater->update($this->blacklist);

        return $this;
    }
}
