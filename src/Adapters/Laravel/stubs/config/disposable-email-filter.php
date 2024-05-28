<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Whitelist Configuration
    |--------------------------------------------------------------------------
    |
    | A list of approved(whitelisted) domains.
    | These specified domains will be exempted from the list of disposable domains.
    |
    | Enter domains in the format "whitelisted-domain.com" without the "@" symbol.
    |
    | !! Note: Whitelisted domains have higher priority than blacklisted domains.
    |
    */
    'whitelist' => [],

    /*
    |--------------------------------------------------------------------------
    | Whitelist Configuration
    |--------------------------------------------------------------------------
    |
    | A list of custom blocked(blacklisted) domains.
    | These specified domains will be added to the list of disposable domains and considered invalid.
    |
    | Enter domains in the format "blacklisted-domain.com" without the "@" symbol.
    |
    */
    'blacklist' => [],
];
