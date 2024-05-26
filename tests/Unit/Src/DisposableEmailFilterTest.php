<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Src;

use Beeyev\DisposableEmailFilter\CustomEmailDomainFilter\CustomEmailDomainFilter;
use Beeyev\DisposableEmailFilter\DisposableEmailDomains\DisposableEmailDomains;
use Beeyev\DisposableEmailFilter\DisposableEmailFilter;
use Beeyev\DisposableEmailFilter\Exceptions\InvalidEmailAddressException;
use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Tests\Unit\Src\DisposableEmailDomains\DisposableEmailDomainsTest;

/**
 * @covers \Beeyev\DisposableEmailFilter\DisposableEmailFilter
 *
 * @uses \Beeyev\DisposableEmailFilter\CustomEmailDomainFilter\CustomEmailDomainFilter
 * @uses \Beeyev\DisposableEmailFilter\DisposableEmailDomains\DisposableEmailDomains
 * @uses \Beeyev\DisposableEmailFilter\Support\Validator
 *
 * @internal
 */
final class DisposableEmailFilterTest extends AbstractTestCase
{
    /** @var DisposableEmailFilter */
    private $disposableEmailFilter;

    protected function setUp(): void
    {
        $this->disposableEmailFilter = new DisposableEmailFilter(
            new CustomEmailDomainFilter(['blacklisted1.com', 'blacklisted2.com', 'blacklisted3.com']),
            new CustomEmailDomainFilter(['abc2.com', 'whitelisted1.com', 'blacklisted3.com']),
            new DisposableEmailDomains(DisposableEmailDomainsTest::DISPOSABLE_EMAIL_DOMAINS_FILE_PATH)
        );
    }

    public function testWhitelistedEmails(): void
    {
        self::assertFalse($this->disposableEmailFilter->isDisposableEmailAddress('test@abc2.com'));
        self::assertFalse($this->disposableEmailFilter->isDisposableEmailAddress('test@whitelisted1.com'));
        self::assertFalse($this->disposableEmailFilter->isDisposableEmailAddress('test@blacklisted3.com')); // Whitelisted has higher priority
    }

    public function testBlacklistedEmails(): void
    {
        self::assertTrue($this->disposableEmailFilter->isDisposableEmailAddress('test@blacklisted1.com'));
        self::assertFalse($this->disposableEmailFilter->isDisposableEmailAddress('test@blacklisted3.com')); // Whitelisted has higher priority
    }

    public function testCheckWhenEmailIsInDisposableList(): void
    {
        self::assertTrue($this->disposableEmailFilter->isDisposableEmailAddress('test@test1.nl'));
    }

    public function testCheckWhenEmailIsNotInDisposableList(): void
    {
        self::assertFalse($this->disposableEmailFilter->isDisposableEmailAddress('test@holabola.nl'));
    }

    public function testThrowsExceptionWhenEmailAddressIsEmpty(): void
    {
        $this->expectException(InvalidEmailAddressException::class);
        $this->expectExceptionMessage('Email address cannot be empty');
        $this->disposableEmailFilter->isDisposableEmailAddress('');
    }

    public function testThrowsExceptionWhenEmailAddressIsInvalid(): void
    {
        $this->expectException(InvalidEmailAddressException::class);
        $this->expectExceptionMessage('Invalid email address: `abc.abc.ru`');
        $this->disposableEmailFilter->isDisposableEmailAddress('abc.abc.ru');
    }
}
