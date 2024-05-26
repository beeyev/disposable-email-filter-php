<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Src\Support;

use Beeyev\DisposableEmailFilter\Support\Validator;
use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;

/**
 * @covers \Beeyev\DisposableEmailFilter\Support\Validator
 *
 * @internal
 */
final class ValidatorTest extends AbstractTestCase
{
    /**
     * @covers \Beeyev\DisposableEmailFilter\DisposableEmailFilter::isEmailAddressValid
     * @dataProvider domainValidatorDataProvider
     */
    public function testDomainValidator(string $domain, bool $expected): void
    {
        self::assertSame($expected, Validator::isDomainValid($domain)); // @phpstan-ignore argument.type
    }

    /**
     * @return non-empty-list<non-empty-array<non-empty-string, bool>>
     */
    public static function domainValidatorDataProvider(): array
    {
        // @phpstan-ignore return.type
        return [
            ['gmail.com', true],
            ['exa.mple.com', true],
            ['exam.ple', true],
            ['exam.сщь', false],
            ['gmail. com', false],
            ['ex/om', false],
            ['examp@le..com', false],
        ];
    }

    /**
     * @dataProvider validEmailsDataProvider
     */
    public function testEmailValidationSuccess(string $validEmailAddress): void
    {
        self::assertTrue(Validator::isEmailAddressValid($validEmailAddress));
    }

    /**
     * @return non-empty-list<non-empty-array<non-empty-string>>
     */
    public static function validEmailsDataProvider(): array
    {
        return [
            ['developer@yiiframework.com'],
            ['sam@rmcreative.ru'],
            ['5011@gmail.com'],
            ['Abc.123@example.com'],
            ['user+mailbox/department=shipping@example.com'],
            ['!#$%&\'*+-/=?^_`.{|}~@example.com'],
            ['test@nonexistingsubdomain.example.com'],
            [str_repeat('a', 64) . '@gmail.com'],
            ['name@' . str_repeat('a', 245) . '.com'],
            ['SAM@RMCREATIVE.RU'],
            ['developer@yiiframework.com'],
            ['sam@rmcreative.ru'],
            ['5011@gmail.com'],
            ['test@example.com'],
            ['5011@example.com'],
            ['test-@dummy.com'],
            ['example@xn--zcack7ayc9a.de'],
            ['sam@rmcreative.ru'],
            ['5011@gmail.com'],
            ['sam@rmcreative.ru'],
            ['5011@gmail.com'],
            ['test@example.com'],
            ['5011@gmail.com'],
            ['ipetrov@gmail.com'],
        ];
    }

    /**
     * @dataProvider invalidEmailsDataProvider
     */
    public function testEmailValidationFailed(string $invalidEmailAddress): void
    {
        self::assertFalse(Validator::isEmailAddressValid($invalidEmailAddress));
    }

    /**
     * @return non-empty-list<non-empty-array<non-empty-string>>
     */
    public static function invalidEmailsDataProvider(): array
    {
        return [
            ['1'],
            ['rmcreative.ru'],
            ['Carsten Brandt <mail@cebe.cc>'],
            ['"Carsten Brandt" <mail@cebe.cc>'],
            ['<mail@cebe.cc>'],
            ['info@örtliches.de'],
            ['sam@рмкреатиф.ru'],
            ['ex..ample@example.com'],
            [str_repeat('a', 65) . '@gmail.com'],
            ['name@' . str_repeat('a', 246) . '.com'],

            // Malicious email addresses that can be used to exploit SwiftMailer vulnerability CVE-2016-10074 while IDN
            // is disabled.
            // https://legalhackers.com/advisories/SwiftMailer-Exploit-Remote-Code-Exec-CVE-2016-10074-Vuln.html

            // This is the demo email used in the proof of concept of the exploit
            ['"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com'],

            // Trying more addresses
            ['"Attacker -Param2 -Param3"@test.com'],
            ['\'Attacker -Param2 -Param3\'@test.com'],
            ['"Attacker \" -Param2 -Param3"@test.com'],
            ["'Attacker \\' -Param2 -Param3'@test.com"],
            ['"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com'],

            // And even more variants
            ['"attacker\"\ -oQ/tmp/\ -X/var/www/cache/phpcode.php"@email.com'],
            ["\"attacker\\\"\0-oQ/tmp/\0-X/var/www/cache/phpcode.php\"@email.com"],
            ['"attacker@cebe.cc\"-Xbeep"@email.com'],
            ["'attacker\\' -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com"],
            ["'attacker\\\\' -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com"],
            ["'attacker\\\\'\\ -oQ/tmp/ -X/var/www/cache/phpcode.php'@email.com"],
            ["'attacker\\';touch /tmp/hackme'@email.com"],
            ["'attacker\\\\';touch /tmp/hackme'@email.com"],
            ["'attacker\\';touch/tmp/hackme'@email.com"],
            ["'attacker\\\\';touch/tmp/hackme'@email.com"],
            ['"attacker\" -oQ/tmp/ -X/var/www/cache/phpcode.php "@email.com'],

            ['rmcreative.ru'],
            ['info@örtliches.de'],
            ['üñîçøðé@üñîçøðé.com'],
            ['sam@рмкреатиф.ru'],
            ['Informtation info@oertliches.de'],
            ['John Smith <example.com>'],
            ['rmcreative.ru'],
            ['Carsten Brandt <mail@cebe.cc>'],
            ['"Carsten Brandt" <mail@cebe.cc>'],
            ['<mail@cebe.cc>'],
            ['Information info@örtliches.de'],
            ['rmcreative.ru'],
            ['John Smith <example.com>'],
            ['name@ñandu.cl'],
            ['gmail.con'],
            ['abc@'],
            ['abc@abc'],
            ['abc@abc@bb.com'],
        ];
    }
}
