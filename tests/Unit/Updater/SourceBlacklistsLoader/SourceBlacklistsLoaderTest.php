<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\SourceBlacklistsLoader;

use Beeyev\DisposableEmailFilter\Tests\AbstractTestCase;
use Beeyev\DisposableEmailFilter\Updater\DataManipulation\ContentsLoaderInterface;
use Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\SourceBlacklistsLoader;

/**
 * @covers \Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\SourceBlacklistsLoader
 *
 * @uses \Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto\SourceBlacklistsDto
 * @uses \Beeyev\DisposableEmailFilter\Updater\SourceBlacklistsLoader\Dto\SourceBlacklistItemDto
 * @uses  \Beeyev\DisposableEmailFilter\Updater\Support\DomainsExtractor
 * @uses \Beeyev\DisposableEmailFilter\Updater\Support\Utils
 *
 * @internal
 */
final class SourceBlacklistsLoaderTest extends AbstractTestCase
{
    public function testLoadSuccess(): void
    {
        $sourceBlacklistsPath = [
            'test1' => 'test1-source-path',
            'test2' => 'test2-source-path',
        ];

        $sourceBlacklistsData = [
            'test1' => implode(PHP_EOL, ['abc2.com', 'abc1.com']),
            'test2' => implode(PHP_EOL, ['efg2.com', 'efg1.com']),
        ];

        $contentsLoaderMock = $this->createMock(ContentsLoaderInterface::class);
        $contentsLoaderMock->expects(self::exactly(count($sourceBlacklistsPath)))
            ->method('load')
            ->withConsecutive(
                [$sourceBlacklistsPath['test1']],
                [$sourceBlacklistsPath['test2']]
            )
            ->willReturnOnConsecutiveCalls(
                $sourceBlacklistsData['test1'],
                $sourceBlacklistsData['test2']
            );

        $sourceBlacklistsLoader = new SourceBlacklistsLoader($contentsLoaderMock, $sourceBlacklistsPath);
        $sourceBlacklistsDto = $sourceBlacklistsLoader->load();

        self::assertEqualsWithDelta(new \DateTimeImmutable(), $sourceBlacklistsDto->dateTime, 1);
        self::assertCount(count($sourceBlacklistsPath), $sourceBlacklistsDto->items);

        $item1 = $sourceBlacklistsDto->items[0];
        self::assertSame('test1', $item1->sourceName);
        self::assertSame(['abc1.com', 'abc2.com'], $item1->domains);

        $item2 = $sourceBlacklistsDto->items[1];
        self::assertSame('test2', $item2->sourceName);
        self::assertSame(['efg1.com', 'efg2.com'], $item2->domains);
    }

    public function testLoadThrowsExceptionOnEmptySource(): void
    {
        $contentsLoaderMock = $this->createMock(ContentsLoaderInterface::class);
        $contentsLoaderMock->expects(self::once())
            ->method('load')->willReturn('');

        $sourceBlacklistsLoader = new SourceBlacklistsLoader($contentsLoaderMock, ['test' => 'test-source-path']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Source blacklist `test` file is empty: `test-source-path`');
        $sourceBlacklistsLoader->load();
    }

    public function testLoadThrowsExceptionOnSourceWithoutDomains(): void
    {
        $contentsLoaderMock = $this->createMock(ContentsLoaderInterface::class);
        $contentsLoaderMock->expects(self::once())
            ->method('load')->willReturn(implode(PHP_EOL, ['incorrect domain.com', '#comment', '']));

        $sourceBlacklistsLoader = new SourceBlacklistsLoader($contentsLoaderMock, ['test' => 'test-source-path']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Source blacklist `test` file: `test-source-path`, does not contain any domains');
        $sourceBlacklistsLoader->load();
    }
}
