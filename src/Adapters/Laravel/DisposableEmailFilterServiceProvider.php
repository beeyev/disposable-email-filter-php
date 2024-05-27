<?php
/**
 * @author Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Adapters\Laravel;
use Beeyev\DisposableEmailFilter\Adapters\Laravel\ValidationRules\DisposableEmailRule;
use Beeyev\DisposableEmailFilter\DisposableEmailFilter;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidationFactory;

final class DisposableEmailFilterServiceProvider extends ServiceProvider
{
    private const TRANSLATIONS_PATH = __DIR__ . '/stubs/lang';
    private const TRANSLATIONS_NAMESPACE = 'disposable-email-filter-php';

    /**
     * Bootstrap services.
     */
    public function boot(ValidationFactory $validationFactory, Translator $translator): void
    {


        // load translation files
        $this->loadTranslationsFrom(self::TRANSLATIONS_PATH,self::TRANSLATIONS_NAMESPACE);

        $this->publishes([
            self::TRANSLATIONS_PATH => $this->app->langPath('vendor/' . self::TRANSLATIONS_NAMESPACE),
        ]);

//        $this->callAfterResolving('validator', function (ValidationFactory $validator) {
//            $validator->extend(DisposableEmailRule::NAME, function ($attribute, string $emailAddress) {
//                return DisposableEmailRule::extendValidate($emailAddress);
//            }, trans('disposable-email-filter-php::validation.disposable_email_validation_message'));
//        });

        $validationFactory->extend(DisposableEmailRule::NAME, static function (string $attribute, string $emailAddress) {
            return DisposableEmailRule::validatorExtension($emailAddress);
        }, $translator->get(DisposableEmailRule::TRANSLATION_KEY));


//        if ($this->app->runningInConsole()) {
//            $this->commands(UpdateDisposableDomainsCommand::class);
//        }
//        $this->publishes([
//            $this->config => config_path('disposable-email.php'),
//        ], 'laravel-disposable-email');

//        $this->publishes([
//            __DIR__ . '/../config/captcha.php' => config_path('captcha.php')
//        ], 'config');
//        AboutCommand::add('My Package', static function (): array {
//            return ['Version' => '1.0.0'];
//        });
    }

    /**
     * Register services.
     */
    public function register(): void
    {
//        $this->mergeConfigFrom(
//            __DIR__ . '/../config/captcha.php',
//            'captcha'
//        );

        $this->app->singleton(DisposableEmailFilter::class, static function (): DisposableEmailFilter {
            return new DisposableEmailFilter();
        });
    }
}
