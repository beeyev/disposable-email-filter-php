# 🗑 Disposable email detection

[![Latest Version on Packagist](https://img.shields.io/packagist/v/beeyev/disposable-email-filter-php)](https://packagist.org/packages/beeyev/disposable-email-filter-php)
[![Supported PHP Versions](https://img.shields.io/packagist/dependency-v/beeyev/disposable-email-filter-php/php.svg)](https://packagist.org/packages/beeyev/disposable-email-filter-php)


PHP package that detects disposable (temporary/throwaway/fake) email addresses.  
It validates email addresses to ensure they are genuine,
which is useful for managing account sign-ups and assessing the number of legitimate email addresses in your system.
This tool also helps to avoid communication errors and blocks spam addresses.  
The lookup is superfast because disposable email domains are stored locally as a native php hash set.

> The list of disposable email domains is regularly updated automatically from trusted external sources.

Supported PHP versions: `v7.2 - v8.3`

## Installation and Usage examples

Require this package with composer using the following command:

```bash
composer require beeyev/disposable-email-filter-php
```

### Basic usage

Simple check if the email address is disposable:

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use Beeyev\DisposableEmailFilter\DisposableEmailFilter;

$disposableEmailFilter = new DisposableEmailFilter();

// Check if email address is disposable
$disposableEmailFilter->isDisposableEmailAddress('ostap@gmail.com'); // false
$disposableEmailFilter->isDisposableEmailAddress('john@tempmailer.com'); // true
```

### Adding custom disposable(blacklisted) and whitelisted domains:

> Note: Whitelisted domains have higher priority than blacklisted domains.

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use Beeyev\DisposableEmailFilter\DisposableEmailFilter;

$disposableEmailFilter = new DisposableEmailFilter();

// Add multiple custom domains to blacklist
$disposableEmailFilter->blacklistedDomains()->addMultiple(['mailinator.com', '10minute-mail.org']);

// Add one domain to whitelist
$disposableEmailFilter->whitelistedDomains()->add('tempmailer.com');

$disposableEmailFilter->isDisposableEmailAddress('john@tempmailer.com'); // false (because it's whitelisted now)
$disposableEmailFilter->isDisposableEmailAddress('john@mailinator.com'); // true
$disposableEmailFilter->isDisposableEmailAddress('john@10minute-mail.org'); // true
$disposableEmailFilter->isDisposableEmailAddress('john@gmail.com'); // false
```

It is also possible to add blacklisted and whitelisted domains using constructor dependency:

```php
use Beeyev\DisposableEmailFilter\CustomEmailDomainFilter\CustomEmailDomainFilter;
use Beeyev\DisposableEmailFilter\DisposableEmailFilter;

$blacklistedDomains = new CustomEmailDomainFilter(['blacklisted1.com', 'blacklisted2.com', 'blacklisted3.com']);
$whitelistedDomains = new CustomEmailDomainFilter(['abc2.com', 'whitelisted1.com']);
$disposableEmailFilter = new DisposableEmailFilter($blacklistedDomains, $whitelistedDomains);

$disposableEmailFilter->isDisposableEmailAddress('test@whitelisted1.com'); // false - whitelisted
```

> Note: You can develop your own filtration logic by creating a custom class that implements the [`CustomEmailDomainFilterInterface`](https://github.com/beeyev/disposable-email-filter-php/blob/master/src/CustomEmailDomainFilter/CustomEmailDomainFilterInterface.php)

### Handling exceptions:

> If you try to pass empty string or string with invalid email format, an exception will be thrown.

```php
use Beeyev\DisposableEmailFilter\DisposableEmailFilter;
use Beeyev\DisposableEmailFilter\Exceptions\InvalidEmailAddressException;

$disposableEmailFilter = new DisposableEmailFilter();

try {
    $disposableEmailFilter->isDisposableEmailAddress('john:gmail.com'); // Exception will be thrown because of invalid email format
} catch (InvalidEmailAddressException $e) {
    echo $e->getMessage();
}
```

If you expect that email address might be invalid and don't want to wrap the code into try/catch,  
you can use `isDisposableEmailAddressSafe` method to check if email address is correctly formatted before checking if it's disposable:

```php
$disposableEmailFilter = new DisposableEmailFilter();

$emailAddress = 'john:gmail.com';

if ($disposableEmailFilter->isEmailAddressValid($emailAddress)) {
    $disposableEmailFilter->isDisposableEmailAddress($emailAddress);
}
```
## Updating

Since the list of disposable email domains is regularly updated, it is important to keep the package up to date.  
A new `PATCH` version of the package is [released](https://github.com/beeyev/disposable-email-filter-php/releases/) whenever the list is updated.  
These updates are safe and non-breaking, allowing you to update the package via this composer command:  
```bash
composer update beeyev/disposable-email-filter-php
```
> You can run this command every time in CI/CD pipelines before the project is deployed.

## Contribution

Feel free to contribute to this project if you want to add new features, improve existing ones, or fix bugs.  
To add new disposable domains, you can update the [`blacklist.txt`](https://github.com/beeyev/disposable-email-filter-php/blob/master/blacklist.txt) file and create a pull request.  
Alternatively, you can [open an issue](https://github.com/beeyev/disposable-email-filter-php/issues) with the domains you want to be added to the blacklist or whitelist.

## External Sources:

- [`7c/fakefilter`](https://github.com/7c/fakefilter)
- [`FGRibreau/mailchecker`](https://github.com/FGRibreau/mailchecker)
- [`disposable-email-domains`](https://github.com/disposable-email-domains/disposable-email-domains)

> Local blacklist and whitelist are stored in the [blacklist.txt](https://github.com/beeyev/disposable-email-filter-php/blob/master/blacklist.txt) and [whitelist.txt](https://github.com/beeyev/disposable-email-filter-php/blob/master/whitelist.txt) files.

## Issues

Bug reports and feature requests can be submitted on the [Github Issue Tracker](https://github.com/beeyev/disposable-email-filter-php/issues).

## License

The MIT License (MIT). Please see [License File](https://github.com/beeyev/disposable-email-filter-php/blob/master/LICENSE.md) for more information.

---

If you love this project, please consider giving me a ⭐

![](https://visitor-badge.laobi.icu/badge?page_id=beeyev.disposable-email-filter-php)
