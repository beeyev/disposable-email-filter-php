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
final class SourceBlacklistsDto
{
    /**
     * @var non-empty-list<SourceBlacklistItemDto>
     * @readonly
     */
    public $items;

    /**
     * @var \DateTimeInterface
     * @readonly
     */
    public $dateTime;

    /**
     * @param non-empty-list<SourceBlacklistItemDto> $sourceDomainListItemDto
     */
    public function __construct(array $sourceDomainListItemDto, ?\DateTimeInterface $dateTime = null)
    {
        $this->items = $sourceDomainListItemDto;
        $this->dateTime = $dateTime ?? new \DateTimeImmutable();
    }
}
