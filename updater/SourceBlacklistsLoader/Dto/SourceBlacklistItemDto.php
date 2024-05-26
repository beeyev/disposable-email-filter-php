<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto;

/**
 * @codeCoverageIgnore
 *
 * @internal
 */
final class SourceBlacklistItemDto
{
    /**
     * @var non-empty-string
     * @readonly
     */
    public $sourceName;

    /**
     * @var non-empty-list<non-empty-string>
     * @readonly
     */
    public $domains;

    /**
     * @param non-empty-string                 $sourceName
     * @param non-empty-list<non-empty-string> $domains
     */
    public function __construct(string $sourceName, array $domains)
    {
        $this->sourceName = $sourceName;
        $this->domains = $domains;
    }
}
