<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\CustomEmailDomainFilter;

use Beeyev\DisposableEmailFilter\Exceptions\DisposableEmailFilterException;
use Beeyev\DisposableEmailFilter\Support\Validator;

final class CustomEmailDomainFilter implements CustomEmailDomainFilterInterface
{
    /** @var array<non-empty-string, true>|null */
    private $emailDomains;

    /**
     * @param list<non-empty-string> $emailDomains
     */
    public function __construct(array $emailDomains = [])
    {
        $this->addMultiple($emailDomains);
    }

    /**
     * Adds a domain name to the whitelist.
     *
     * @param non-empty-string $emailDomain
     *
     * @throws DisposableEmailFilterException If the domain name is empty or invalid
     */
    public function add(string $emailDomain): CustomEmailDomainFilterInterface
    {
        if ($emailDomain === '') {
            throw new DisposableEmailFilterException('The domain name must not be empty.');
        }
        if (Validator::isDomainValid($emailDomain) === false) {
            throw new DisposableEmailFilterException("The domain name is invalid: `{$emailDomain}`");
        }

        $emailDomain = strtolower($emailDomain);

        $this->emailDomains[$emailDomain] = true;

        return $this;
    }

    /**
     * Adds multiple domain names to the whitelist.
     *
     * @param list<non-empty-string> $emailDomains
     *
     * @throws DisposableEmailFilterException If the domain name is empty or invalid
     */
    public function addMultiple(array $emailDomains): CustomEmailDomainFilterInterface
    {
        foreach ($emailDomains as $emailDomain) {
            $this->add($emailDomain);
        }

        return $this;
    }

    /**
     * Returns true if the domain name is set in the list.
     *
     * @param non-empty-string $emailDomain
     */
    public function isInList(string $emailDomain): bool
    {
        $emailDomain = strtolower($emailDomain);

        return isset($this->emailDomains[$emailDomain]);
    }
}
