<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Support;

/** @internal */
final class Validator
{
    private const PATTERN1 = '/^(?P<name>(?:"?([^"]*)"?\s)?)(?:\s+)?((?P<open><?)((?P<local>.+)@(?P<domain>[^>]+))(?P<close>>?))/';
    private const PATTERN2 = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';

    /**
     * Validates the email address.
     * Original implementation - https://github.com/yiisoft/validator
     */
    public static function isEmailAddressValid(string $emailAddress): bool
    {
        if ((bool) preg_match(self::PATTERN1, $emailAddress, $matches) === false) {
            $isValid = false;
        } elseif (is_string($matches['local']) && strlen($matches['local']) > 64) {
            // The maximum total length of a username or other local-part is 64 octets. RFC 5322 section 4.5.3.1.1
            // https://www.rfc-editor.org/rfc/rfc5321#section-4.5.3.1.1
            $isValid = false;
        } elseif (
            is_string($matches['local'])
            && strlen($matches['local']) + strlen((string) $matches['domain']) > 253
        ) {
            // There is a restriction in RFC 2821 on the length of an address in MAIL and RCPT commands
            // of 254 characters. Since addresses that do not fit in those fields are not normally useful, the
            // upper limit on address lengths should normally be considered to be 254.
            //
            // Dominic Sayers, RFC 3696 erratum 1690
            // https://www.rfc-editor.org/errata_search.php?eid=1690
            $isValid = false;
        } else {
            $isValid = (bool) preg_match(self::PATTERN2, $emailAddress);
        }

        return $isValid;
    }

    /**
     * Validates the domain name.
     *
     * @param non-empty-string $domain
     */
    public static function isDomainValid(string $domain): bool
    {
        return filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }
}
