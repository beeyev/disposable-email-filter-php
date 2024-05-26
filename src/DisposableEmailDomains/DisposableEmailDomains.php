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

    /**
     * @param non-empty-string $disposableEmailDomainsFilePath The path to the file with the list of disposable email domains
     */
    public function __construct(string $disposableEmailDomainsFilePath)
    {
        assert($disposableEmailDomainsFilePath !== '', 'The disposable email domains file path must not be empty.');
        $this->disposableEmailDomainsFilePath = $disposableEmailDomainsFilePath;
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
        if ($this->disposableEmailDomains === null) {
            if (!is_file($this->disposableEmailDomainsFilePath) || !is_readable($this->disposableEmailDomainsFilePath)) {
                throw new DisposableEmailFilterException("The disposable email domains file is not readable or does not exist: `{$this->disposableEmailDomainsFilePath}`");
            }

            $this->disposableEmailDomains = require $this->disposableEmailDomainsFilePath;

            if (!is_array($this->disposableEmailDomains) || count($this->disposableEmailDomains) === 0) {
                throw new DisposableEmailFilterException("The disposable email domains list is incorrect or empty: `{$this->disposableEmailDomainsFilePath}`");
            }
        }

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
}
