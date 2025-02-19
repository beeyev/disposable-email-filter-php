<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\Support;

/** @internal */
final class DomainsExtractor
{
    private const SPLIT_LINE_REGEX = '~[\r\n]+~';

    /**
     * @param non-empty-string $multilineDomainList
     *
     * @return list<non-empty-string>
     */
    public static function toArray(string $multilineDomainList): array
    {
        $result = [];

        foreach ((array) preg_split(self::SPLIT_LINE_REGEX, $multilineDomainList) as $line) {
            $line = trim((string) $line);

            if (
                $line === '' || ctype_space($line) // Skip empty lines
                || Utils::isStringStartsWith($line, '#') // Skip Comments
                || self::isDomainValid($line) === false // Skip incorrect domains
            ) {
                continue;
            }

            $line = mb_strtolower($line);

            if (!in_array($line, $result, true)) {
                $result[] = $line;
            }
        }

        if ($result === []) {
            return [];
        }

        $result = array_unique($result);

        Utils::naturalSort($result);

        return array_values($result);
    }

    private static function isDomainValid(string $domain): bool
    {
        return filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }
}
