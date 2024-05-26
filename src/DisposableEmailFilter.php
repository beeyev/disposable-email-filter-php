<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter;

use Beeyev\DisposableEmailFilter\CustomEmailDomainFilter\CustomEmailDomainFilter;
use Beeyev\DisposableEmailFilter\CustomEmailDomainFilter\CustomEmailDomainFilterInterface;
use Beeyev\DisposableEmailFilter\DisposableEmailDomains\DisposableEmailDomains;
use Beeyev\DisposableEmailFilter\Exceptions\InvalidEmailAddressException;
use Beeyev\DisposableEmailFilter\Support\Validator;

class DisposableEmailFilter
{
    private const DEFAULT_DISPOSABLE_EMAIL_DOMAINS_FILE_PATH = __DIR__ . '/DisposableEmailDomains/DisposableEmailDomainsList.php.data';

    /**
     * @var CustomEmailDomainFilterInterface
     * @readonly
     */
    private $blacklistedDomains;

    /**
     * @var CustomEmailDomainFilterInterface
     * @readonly
     */
    private $whitelistedDomains;

    /**
     * @var DisposableEmailDomains
     * @readonly
     */
    private $disposableEmailDomains;

    public function __construct(
        ?CustomEmailDomainFilterInterface $blacklistedDomains = null,
        ?CustomEmailDomainFilterInterface $whitelistedDomains = null,
        ?DisposableEmailDomains $disposableEmailDomains = null
    ) {
        $this->blacklistedDomains = $blacklistedDomains ?? new CustomEmailDomainFilter();
        $this->whitelistedDomains = $whitelistedDomains ?? new CustomEmailDomainFilter();
        $this->disposableEmailDomains = $disposableEmailDomains ?? new DisposableEmailDomains(self::DEFAULT_DISPOSABLE_EMAIL_DOMAINS_FILE_PATH);
    }

    final public function disposableEmailDomains(): DisposableEmailDomains
    {
        return $this->disposableEmailDomains;
    }

    final public function blacklistedDomains(): CustomEmailDomainFilterInterface
    {
        return $this->blacklistedDomains;
    }

    final public function whitelistedDomains(): CustomEmailDomainFilterInterface
    {
        return $this->whitelistedDomains;
    }

    /**
     * @param string $emailAddress The full email address
     *
     * @throws InvalidEmailAddressException If the email address is invalid
     *
     * @return bool Returns true if the email address is disposable
     */
    final public function isDisposableEmailAddress(string $emailAddress): bool
    {
        if ($emailAddress === '') {
            throw new InvalidEmailAddressException('Email address cannot be empty.');
        }
        if ($this->isEmailAddressValid($emailAddress) === false) {
            throw new InvalidEmailAddressException("Invalid email address: `{$emailAddress}`");
        }

        $emailDomain = explode('@', $emailAddress)[1];
        assert($emailDomain !== '', 'The email domain must not be empty.');

        if ($this->whitelistedDomains()->isInList($emailDomain)) {
            return false;
        }

        if ($this->blacklistedDomains()->isInList($emailDomain)) {
            return true;
        }

        return $this->disposableEmailDomains->isDisposableEmailDomain($emailDomain);
    }

    /**
     * Validates the email address.
     *
     * @return bool Returns true if the email address is valid
     */
    final public function isEmailAddressValid(string $emailAddress): bool
    {
        return Validator::isEmailAddressValid($emailAddress);
    }
}
