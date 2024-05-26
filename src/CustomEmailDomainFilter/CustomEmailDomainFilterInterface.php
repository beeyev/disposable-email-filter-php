<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\CustomEmailDomainFilter;

use Beeyev\DisposableEmailFilter\Exceptions\DisposableEmailFilterException;

interface CustomEmailDomainFilterInterface
{
    /**
     * Adds a domain name to the whitelist.
     *
     * @param non-empty-string $emailDomain
     *
     * @throws DisposableEmailFilterException If the domain name is empty or invalid
     */
    public function add(string $emailDomain): self;

    /**
     * Adds multiple domain names to the whitelist.
     *
     * @param list<non-empty-string> $emailDomains
     *
     * @throws DisposableEmailFilterException If the domain name is empty or invalid
     */
    public function addMultiple(array $emailDomains): self;

    /**
     * Returns true if the domain name is whitelisted.
     *
     * @param non-empty-string $emailDomain
     */
    public function isInList(string $emailDomain): bool;
}
