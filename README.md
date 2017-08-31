# Railt Laravel Adapter

<p align="center">
    <a href="https://travis-ci.org/railt/laravel-adapter"><img src="https://travis-ci.org/railt/laravel-adapter.svg?branch=master" alt="Travis CI" /></a>
    <a href="https://scrutinizer-ci.com/g/railt/laravel-adapter/?branch=master"><img src="https://scrutinizer-ci.com/g/railt/laravel-adapter/badges/quality-score.png?b=master" alt="Scrutinizer CI" /></a>
    <a href="https://scrutinizer-ci.com/g/railt/laravel-adapter/?branch=master"><img src="https://scrutinizer-ci.com/g/railt/laravel-adapter/badges/coverage.png?b=master" alt="Code coverage" /></a>
    <a href="https://packagist.org/packages/railt/laravel-adapter"><img src="https://poser.pugx.org/railt/laravel-adapter/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/railt/laravel-adapter"><img src="https://poser.pugx.org/railt/laravel-adapter/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/railt/laravel-adapter/master/LICENSE"><img src="https://poser.pugx.org/railt/laravel-adapter/license" alt="License MIT"></a>
</p>

## About

The Laravel Framework ServiceProvider for Railt.

## Installation

> Make sure that you are using at least PHP 7.1

- `composer require railt/laravel-adapter`

### Laravel 5.5+

- `php artisan vendor:publish --tag=railt`

### Laravel 5.4 or less

- Add the service provider to your `app/config/app.php` file:
```php
'providers' => [
    // ...
    Railt\Adapters\Laravel\RailtServiceProvider::class,
]
```

- Publish configs and other resources: 

```bash
php artisan vendor:publish --tag=railt
```
