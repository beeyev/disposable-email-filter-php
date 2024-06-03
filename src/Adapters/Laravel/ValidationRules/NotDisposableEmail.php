<?php
/**
 * @author Alexander Tebiev - https://github.com/beeyev
 */

namespace Beeyev\DisposableEmailFilter\Adapters\Laravel\ValidationRules;

use Beeyev\DisposableEmailFilter\Adapters\Laravel\DisposableEmailFilterServiceProvider;
use Beeyev\DisposableEmailFilter\Adapters\Laravel\Facades\DisposableEmail;
use Illuminate\Contracts\Validation\ValidationRule;

final class NotDisposableEmail implements ValidationRule
{
    public const RULE_NAME = 'not_disposable_email';

    public const TRANSLATION_KEY = DisposableEmailFilterServiceProvider::PACKAGE_NAMESPACE . '::validation.disposable_email_validation_message';

    /**
     * Run the validation rule.
     *
     * @param string $emailAddress
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, $emailAddress, \Closure $fail): void
    {
        if (self::isDisposable($emailAddress)) {
            $fail(self::TRANSLATION_KEY)->translate();
        }
    }

    public static function isDisposable(string $emailAddress): bool
    {
        if (DisposableEmail::isEmailAddressValid($emailAddress)) {
            return DisposableEmail::isDisposableEmailAddress($emailAddress);
        }

        return false;
    }
}
