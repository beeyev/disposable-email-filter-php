<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Src\CustomEmailDomainFilter;

use Beeyev\DisposableEmailFilter\CustomEmailDomainFilter\CustomEmailDomainFilter;
use Beeyev\DisposableEmailFilter\Exceptions\DisposableEmailFilterException;
use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;

/**
 * @covers \Beeyev\DisposableEmailFilter\CustomEmailDomainFilter\CustomEmailDomainFilter
 *
 * @uses \Beeyev\DisposableEmailFilter\Support\Validator
 *
 * @internal
 */
final class CustomEmailDomainFilterTest extends AbstractTestCase
{
    public function testDomainCanBeAddedAndCheckedInList(): void
    {
        $customEmailDomainFilter = new CustomEmailDomainFilter();
        $customEmailDomainFilter->add('example1.com');

        self::assertTrue($customEmailDomainFilter->isInList('example1.com'));
    }

    public function testDomainIsSetAndCheckedInLowerCase(): void
    {
        $customEmailDomainFilter = new CustomEmailDomainFilter();
        $customEmailDomainFilter->add('examPLE1.com');

        self::assertTrue($customEmailDomainFilter->isInList('EXample1.com'));
    }

    public function testMultipleDomainsCanBeAddedAndCheckedInList(): void
    {
        $customEmailDomainFilter = new CustomEmailDomainFilter(['example1.com', 'example2.com']);
        $customEmailDomainFilter->addMultiple(['example2.com', 'example3.com']);

        self::assertTrue($customEmailDomainFilter->isInList('example1.com'));
        self::assertTrue($customEmailDomainFilter->isInList('example2.com'));
        self::assertTrue($customEmailDomainFilter->isInList('example3.com'));
    }

    public function testDomainReturnsFalseIfNotDefined(): void
    {
        $customEmailDomainFilter = new CustomEmailDomainFilter();
        $customEmailDomainFilter->add('example1.com');

        self::assertFalse($customEmailDomainFilter->isInList('example2.com'));
    }

    public function testAddingEmptyStringThrowsException(): void
    {
        $customEmailDomainFilter = new CustomEmailDomainFilter();

        $this->expectException(DisposableEmailFilterException::class);
        $this->expectExceptionMessage('The domain name must not be empty.');
        $customEmailDomainFilter->add(''); // @phpstan-ignore argument.type
    }

    public function testAddingInvalidDomainThrowsException(): void
    {
        $customEmailDomainFilter = new CustomEmailDomainFilter();

        $this->expectException(DisposableEmailFilterException::class);
        $this->expectExceptionMessageMatches('/^The domain name is invalid:/');
        $customEmailDomainFilter->add('@1111');
    }
}
