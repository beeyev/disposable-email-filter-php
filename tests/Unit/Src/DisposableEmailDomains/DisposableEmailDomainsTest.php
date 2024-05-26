<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Src\DisposableEmailDomains;

use Beeyev\DisposableEmailFilter\DisposableEmailDomains\DisposableEmailDomains;
use Beeyev\DisposableEmailFilter\Exceptions\DisposableEmailFilterException;
use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;

/**
 * @covers \Beeyev\DisposableEmailFilter\DisposableEmailDomains\DisposableEmailDomains
 *
 * @internal
 */
final class DisposableEmailDomainsTest extends AbstractTestCase
{
    public const DISPOSABLE_EMAIL_DOMAINS_FILE_PATH = __DIR__ . '/DisposableEmailDomainsList.php.dummy_data';

    public function testDisposableEmailDomainCanBeChecked(): void
    {
        $disposableEmailDomains = new DisposableEmailDomains(self::DISPOSABLE_EMAIL_DOMAINS_FILE_PATH);

        self::assertTrue($disposableEmailDomains->isDisposableEmailDomain('abc1.com'));
        self::assertTrue($disposableEmailDomains->isDisposableEmailDomain('test2.nl'));
        self::assertFalse($disposableEmailDomains->isDisposableEmailDomain('not-here.ru'));
    }

    public function testDisposableEmailDomainThrowsExceptionIfEmpty(): void
    {
        $disposableEmailDomains = new DisposableEmailDomains(self::DISPOSABLE_EMAIL_DOMAINS_FILE_PATH);

        $this->expectException(DisposableEmailFilterException::class);
        $this->expectExceptionMessage('The domain name must not be empty.');
        $disposableEmailDomains->isDisposableEmailDomain(''); // @phpstan-ignore argument.type
    }

    public function testDisposableEmailDomainThrowsExceptionIfFileNotReadable(): void
    {
        $disposableEmailDomains = new DisposableEmailDomains('not-exists');

        $this->expectException(DisposableEmailFilterException::class);
        $this->expectExceptionMessage('The disposable email domains file is not readable or does not exist: `not-exists`');
        $disposableEmailDomains->isDisposableEmailDomain('abc1.com');
    }

    public function testDisposableEmailDomainThrowsExceptionIfListEmpty(): void
    {
        $this->expectException(DisposableEmailFilterException::class);
        $this->expectExceptionMessageMatches('/^The disposable email domains list is incorrect or empty:/');

        $disposableEmailDomains = new DisposableEmailDomains(__DIR__ . '/DisposableEmailDomainsList.php.empty_data');
        $disposableEmailDomains->isDisposableEmailDomain('abc1.com');
    }
}
