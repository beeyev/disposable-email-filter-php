<?php
/**
 * @author Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Adapters\Laravel;
use Beeyev\DisposableEmailFilter\DisposableEmailFilter;
use Illuminate\Support\ServiceProvider;

final class DisposableEmailFilterServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(DisposableEmailFilter::class, static function () {
            return new DisposableEmailFilter();
        });
    }
}
