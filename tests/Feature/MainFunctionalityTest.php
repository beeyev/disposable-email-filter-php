<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Feature;

use Beeyev\DisposableEmailFilter\DisposableEmailFilter;
use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Updater\Config;
use Beeyev\DisposableEmailFilter\Updater\Support\DomainsExtractor;

/**
 * @internal
 * @coversNothing
 */
final class MainFunctionalityTest extends AbstractTestCase
{
    /** @var non-empty-string */
    private $randomBlackListedEmail;

    /** @var non-empty-string */
    private $randomWhitelistedEmail;

    /** @var non-empty-string */
    private $randomDisposableEmail;

    /** @var DisposableEmailFilter */
    private $disposableEmailFilter;

    protected function setUp(): void
    {
        $this->randomBlackListedEmail = 'abc@' . $this->getRandomDomain(Config::LOCAL_BLACKLIST_PATH);
        $this->randomWhitelistedEmail = 'abc@' . $this->getRandomDomain(Config::WHITELIST_PATH);
        $this->randomDisposableEmail = 'abc@' . $this->getRandomDomain(Config::TXT_BLACKLIST_PATH);

        $this->disposableEmailFilter = new DisposableEmailFilter();
    }

    public function testWhitelistedDomain(): void
    {
        $result = $this->disposableEmailFilter->isDisposableEmailAddress($this->randomWhitelistedEmail);

        self::assertFalse($result);
    }

    public function testBlacklistedDomain(): void
    {
        $result = $this->disposableEmailFilter->isDisposableEmailAddress($this->randomBlackListedEmail);

        self::assertTrue($result);
    }

    public function testDisposableDomainIsDisposable(): void
    {
        $result = $this->disposableEmailFilter->isDisposableEmailAddress($this->randomDisposableEmail);

        self::assertTrue($result);
    }

    public function testNonDisposableDomain(): void
    {
        $result = $this->disposableEmailFilter->isDisposableEmailAddress('abc@gmail.com');

        self::assertFalse($result);
    }

    /**
     * @param non-empty-string $filePath
     *
     * @return non-empty-string
     */
    private function getRandomDomain(string $filePath): string
    {
        $fileContents = $this->loafFirstLinesOfFile($filePath, 50);
        $domains = DomainsExtractor::toArray($fileContents);

        $result = $domains[array_rand($domains)];
        assert(is_string($result) && $result !== '');

        return $result;
    }

    /**
     * @param non-empty-string $filePath
     * @param positive-int     $linesCount
     *
     * @return non-empty-string
     */
    private function loafFirstLinesOfFile(string $filePath, int $linesCount): string
    {
        $lines = [];
        $fh = fopen($filePath, 'r');
        assert($fh !== false, 'Could not open file: ' . $filePath);

        for ($i = 0; $i < $linesCount; ++$i) {
            $line = fgets($fh);
            if ($line === false) {
                break;
            }

            $lines[] = str_replace(["\r", "\n"], '', $line);
        }

        if (fclose($fh) === false) {
            throw new \RuntimeException('Could not close file: ' . $filePath);
        }

        assert(count($lines) > 0);

        return implode(PHP_EOL, $lines);
    }
}
