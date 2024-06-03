<?php
/**
 * @author Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Adapters\Laravel;

use Beeyev\DisposableEmailFilter\Adapters\Laravel\ValidationRules\NotDisposableEmail;
use Beeyev\DisposableEmailFilter\DisposableEmailFilter;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Config\Repository;

final class DisposableEmailFilterServiceProvider extends ServiceProvider
{
    public const PACKAGE_NAMESPACE = 'disposable-email-filter';
    private const TRANSLATIONS_PATH = __DIR__ . '/stubs/lang';
    private const CONFIG_FILE_NAME = self::PACKAGE_NAMESPACE . '.php';
    private const CONFIG_PATH = __DIR__ . '/stubs/config/' . self::CONFIG_FILE_NAME;

    /**
     * Bootstrap services.
     */
    public function boot(ValidationFactory $validationFactory, Translator $translator): void
    {
        // load translation files
        $this->loadTranslationsFrom(self::TRANSLATIONS_PATH, self::PACKAGE_NAMESPACE);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::CONFIG_PATH => config_path(self::CONFIG_FILE_NAME),
            ], self::PACKAGE_NAMESPACE);

            $this->publishes([
                self::TRANSLATIONS_PATH => $this->app->langPath('vendor/' . self::PACKAGE_NAMESPACE),
            ], self::PACKAGE_NAMESPACE);
        }

        $validationFactory->extend(NotDisposableEmail::RULE_NAME, static function (string $attribute, string $emailAddress): bool {
            return !NotDisposableEmail::isDisposable($emailAddress);
        }, $translator->get(NotDisposableEmail::TRANSLATION_KEY));

        //        AboutCommand::add('My Package', static function (): array {
        //            return ['Version' => '1.0.0'];
        //        });
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, self::PACKAGE_NAMESPACE);

        $this->app->singleton(DisposableEmailFilter::class, static function (Application $app): DisposableEmailFilter {
            $config = $app->get('config');
            assert($config instanceof Repository);

            if (!isset($config->get(self::PACKAGE_NAMESPACE)['whitelist'])) {
                throw new \RuntimeException(self::PACKAGE_NAMESPACE . ': Whitelist array is not defined in configuration file or configuration file is not loaded');
            }

            if (!isset($config->get(self::PACKAGE_NAMESPACE)['blacklist'])) {
                throw new \RuntimeException(self::PACKAGE_NAMESPACE . ': Blacklist array is not defined in configuration file or configuration file is not loaded');
            }

            $whitelistedDomains = $config->get(self::PACKAGE_NAMESPACE)['whitelist'];
            $blacklistedDomains = $config->get(self::PACKAGE_NAMESPACE)['blacklist'];

            $disposableEmailFilter = new DisposableEmailFilter();
            $disposableEmailFilter->whitelistedDomains()->addMultiple($whitelistedDomains);
            $disposableEmailFilter->blacklistedDomains()->addMultiple($blacklistedDomains);

            return $disposableEmailFilter;
        });
    }
}
