<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\Support;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Updater\Support\DomainsExtractor;

/**
 * @covers \Beeyev\DisposableEmailFilter\Updater\Support\DomainsExtractor
 *
 * @uses \Beeyev\DisposableEmailFilter\Updater\Support\Utils
 *
 * @internal
 */
final class DomainsExtractorTest extends AbstractTestCase
{
    public function testSuccessfullyExtractsDomains(): void
    {
        $domains = DomainsExtractor::toArray($this->sourceDomains());

        self::assertSame(
            [
                '1dcv.com',
                '01dcv.com',
                'abc02.com',
                'abc03.com',
                'abc1.com',
                'abc2.com',
                'abc3.com',
                'abc4.com',
            ],
            $domains
        );
    }

    public function testReturnsEmptyArray(): void
    {
        $domains = DomainsExtractor::toArray($this->emptyResultExpected());

        self::assertEmpty($domains);
    }

    /**
     * @return non-empty-string
     */
    private function sourceDomains(): string
    {
        return <<<'EOD'
# Lorem ipsum dolor sit amet
abc03.com

abc3.com
abc4.com
abc02.com
ABC1.com

# Lorem ipsum dolor sit amet

abc1.com
abc2.com

1dcv.com
01dcv.com

icnorrect val2
домен.рф
EOD;
    }

    /**
     * @return non-empty-string
     */
    private function emptyResultExpected(): string
    {
        return <<<'EOD'
# Lorem ipsum dolor sit amet
incorrect domain.com

icnorrect val2


домен.рф
EOD;
    }
}
