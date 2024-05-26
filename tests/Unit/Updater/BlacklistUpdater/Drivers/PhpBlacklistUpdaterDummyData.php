<?php

declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Tests\Unit\Updater\BlacklistUpdater\Drivers;

/**
 * @internal
 */
final class PhpBlacklistUpdaterDummyData
{
    /** @return non-empty-string */
    public static function getValidPhpData(): string
    {
        return <<<'EOD'
<?php
/**
 * Lorem ipsum dolor sit amet
 */
return array(
    '0-00.usa.cc'    => true,
    '0-30-24.com' =>   true,

    '0-attorney.com' => true,
    '0-mail.com' => true,
);
EOD;
    }

    /** @return non-empty-string */
    public static function getInvalidPhpData(): string
    {
        return <<<'EOD'
<?php
return array(
    '0-00.usa.cc'    => true,
EOD;
    }

    /** @return non-empty-string */
    public static function getEmptyPhpData(): string
    {
        return <<<'EOD'
<?php
return [];
EOD;
    }
}
