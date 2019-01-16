# Laravel Provider for Railt

<p align="center">
    <a href="https://travis-ci.org/railt/laravel-provider"><img src="https://travis-ci.org/railt/laravel-provider.svg?branch=master&" alt="Travis CI" /></a>
    <a href="https://scrutinizer-ci.com/g/railt/laravel-provider/?branch=master"><img src="https://scrutinizer-ci.com/g/railt/laravel-provider/badges/quality-score.png?b=master&" alt="Scrutinizer CI" /></a>
    <a href="https://scrutinizer-ci.com/g/railt/laravel-provider/?branch=master"><img src="https://scrutinizer-ci.com/g/railt/laravel-provider/badges/coverage.png?b=master&" alt="Code coverage" /></a>
    <a href="https://packagist.org/packages/railt/laravel-provider"><img src="https://poser.pugx.org/railt/laravel-provider/version?" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/railt/laravel-provider"><img src="https://poser.pugx.org/railt/laravel-provider/v/unstable?" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/railt/laravel-provider/master/LICENSE"><img src="https://poser.pugx.org/railt/laravel-provider/license?" alt="License MIT"></a>
</p>

## About

The Laravel Framework Service Provider for Railt.

## Installation

- `composer require railt/laravel-provider`
- Add to `composer.json` the `"Railt\\Discovery\\Manifest::discover"` composer script:
```json5
{
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi",
            
            // HERE
            "Railt\\Discovery\\Manifest::discover"
        ]
    }
}
```
- `composer dump-autoload`
- `php artisan vendor:publish --tag=railt`

## Usage

Just use the description in the package configuration 
`config/railt.php` file.
