<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\DisposableEmailDomains;

use Beeyev\DisposableEmailFilter\Exceptions\DisposableEmailFilterException;

/** @internal */
final class DisposableEmailDomains
{
    /** @var non-empty-string */
    private $disposableEmailDomainsFilePath;

    /** @var non-empty-array<non-empty-string, true>|null */
    private $disposableEmailDomains;

    /** @var \DateTimeImmutable */
    private $updatedDateTime;

    /**
     * @param non-empty-string $disposableEmailDomainsFilePath The path to the file with the list of disposable email domains
     */
    public function __construct(string $disposableEmailDomainsFilePath)
    {
        assert($disposableEmailDomainsFilePath !== '', 'The disposable email domains file path must not be empty.');
        $this->disposableEmailDomainsFilePath = $disposableEmailDomainsFilePath;
    }

    /**
     * Returns the date and time when the list of disposable email domains was last updated.
     *
     * @throws DisposableEmailFilterException If the disposable email domains file is not readable, or the list is incorrect or empty
     */
    public function getUpdatedDateTime(): \DateTimeImmutable
    {
        $this->lazyLoadDomainsData();

        return $this->updatedDateTime;
    }

    /**
     * Returns the list of disposable email domains.
     * The list is an associative array where the key is a domain name, and the value is always true.
     *
     * @throws DisposableEmailFilterException If the disposable email domains file is not readable, or the list is incorrect or empty
     *
     * @return non-empty-array<non-empty-string, true>
     */
    private function getDomains(): array
    {
        $this->lazyLoadDomainsData();

        return $this->disposableEmailDomains; // @phpstan-ignore return.type
    }

    /**
     * Returns true if the domain name is disposable email domain.
     *
     * @param non-empty-string $domainName
     *
     * @throws DisposableEmailFilterException If the domain name is empty
     */
    public function isDisposableEmailDomain(string $domainName): bool
    {
        if ($domainName === '') {
            throw new DisposableEmailFilterException('The domain name must not be empty.');
        }

        $domainName = strtolower($domainName);

        return isset($this->getDomains()[$domainName]);
    }

    /**
     * @throws DisposableEmailFilterException If the disposable email domains file is not readable, or the list is incorrect or empty
     */
    private function lazyLoadDomainsData(): void
    {
        if ($this->disposableEmailDomains === null) {
            if (!is_file($this->disposableEmailDomainsFilePath) || !is_readable($this->disposableEmailDomainsFilePath)) {
                throw new DisposableEmailFilterException("The disposable email domains file is not readable or does not exist: `{$this->disposableEmailDomainsFilePath}`");
            }

            /**
             * @var array{
             *        'updated_at': \DateTimeImmutable,
             *        'disposable_email_domains': non-empty-array<non-empty-string, true>
             *     } $domainsData
             */
            $domainsData = require $this->disposableEmailDomainsFilePath;

            if (!is_array($domainsData) || count($domainsData) === 0) {
                throw new DisposableEmailFilterException("The disposable email domains list is incorrect or empty: `{$this->disposableEmailDomainsFilePath}`");
            }

            if (!isset($domainsData['updated_at']) || !$domainsData['updated_at'] instanceof \DateTimeImmutable) {
                throw new DisposableEmailFilterException("The updated date time value 'updated_at' is incorrect or missing in the disposable email domains file: `{$this->disposableEmailDomainsFilePath}`");
            }

            if (
                !isset($domainsData['disposable_email_domains'])
                || !is_array($domainsData['disposable_email_domains'])
                || count($domainsData['disposable_email_domains']) === 0) {
                throw new DisposableEmailFilterException("The list of disposable email domains 'disposable_email_domains' is incorrect or missing in the disposable email domains file: `{$this->disposableEmailDomainsFilePath}`");
            }

            $this->updatedDateTime = $domainsData['updated_at'];
            $this->disposableEmailDomains = $domainsData['disposable_email_domains'];
        }
    }
}
