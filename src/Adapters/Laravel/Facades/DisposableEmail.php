<?php
/**
 * @author Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Adapters\Laravel\Facades;

use Beeyev\DisposableEmailFilter\DisposableEmailFilter;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Beeyev\DisposableEmailFilter\DisposableEmailDomains\DisposableEmailDomains            disposableEmailDomains()
 * @method static \Beeyev\DisposableEmailFilter\CustomEmailDomainFilter\CustomEmailDomainFilterInterface blacklistedDomains()
 * @method static \Beeyev\DisposableEmailFilter\CustomEmailDomainFilter\CustomEmailDomainFilterInterface whitelistedDomains()
 * @method static bool                                                                                   isDisposableEmailAddress(string $emailAddress)
 * @method static bool                                                                                   isEmailAddressValid(string $emailAddress)
 *
 * @see DisposableEmailFilter
 */
final class DisposableEmail extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DisposableEmailFilter::class;
    }
}
