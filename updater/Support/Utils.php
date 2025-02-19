<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\Support;

/** @internal */
final class Utils
{
    /**
     * @param non-empty-string $filePath
     *
     * @throws \RuntimeException
     */
    public static function fileGetContents(string $filePath): string
    {
        error_clear_last();

        if (($content = @file_get_contents($filePath)) === false) {
            $errorMsg = error_get_last();
            $errorMsg = $errorMsg['message'] ?? 'n/a';

            throw new \RuntimeException("Could not load file contents. File path: `{$filePath}`, Error: {$errorMsg}");
        }

        return $content;
    }

    /**
     * @param non-empty-string $filePath
     *
     * @return non-negative-int
     */
    public static function filePutContents(string $filePath, string $contents): int
    {
        error_clear_last();

        if (($result = @file_put_contents($filePath, $contents, LOCK_EX)) === false) {
            $errorMsg = error_get_last();
            $errorMsg = $errorMsg['message'] ?? 'n/a';

            throw new \RuntimeException("Could not write file contents. File path: `{$filePath}`, Error: {$errorMsg}");
        }

        return $result;
    }

    /**
     * @param non-empty-string $haystack
     * @param non-empty-string $needle
     */
    public static function isStringStartsWith(string $haystack, string $needle): bool
    {
        return $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }

    /**
     * @throws \RuntimeException
     *
     * @return array<mixed>
     */
    public static function jsonDecode(string $string): array
    {
        $result = json_decode($string, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Could not decode JSON string. Error: ' . json_last_error_msg());
        }

        if (!is_array($result)) {
            throw new \RuntimeException('Could not decode JSON string. Result is not an array');
        }

        return $result;
    }

    /**
     * @param non-empty-string $string
     *
     * @return non-empty-string
     */
    public static function hash(string $string): string
    {
        return md5($string);
    }

    /**
     * @param array<string> $array
     */
    public static function naturalSort(array &$array): void
    {
        uasort($array, static function (string $a, string $b): int {
            if (($cmp = strnatcmp($a, $b)) !== 0) {
                return $cmp;
            }

            return strcmp($a, $b);
        });
    }
}
