<?php
/**
 * @author Alexander Tebiev - https://github.com/beeyev
 */
namespace Beeyev\DisposableEmailFilter\Adapters\Laravel\ValidationRules;

use Beeyev\DisposableEmailFilter\Adapters\Laravel\Facades\DisposableEmail;
use Illuminate\Contracts\Validation\ValidationRule;

final class DisposableEmailRule implements ValidationRule
{
    public const NAME = 'disposable_email';

    public const TRANSLATION_KEY = 'disposable-email-filter-php::validation.disposable_email_validation_message';

    /**
     * Run the validation rule.
     *
     * @param string                                                                $value
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, $value, \Closure $fail): void
    {
        if (DisposableEmail::isDisposableEmailAddress($value)) {
            $fail(self::TRANSLATION_KEY)->translate();
        }
    }

    public static function validatorExtension(string $emailAddress): bool
    {
        return !DisposableEmail::isDisposableEmailAddress($emailAddress);
    }
}
