<p align="center"><img src="/art/logo.svg" alt="Logo Laravel Breeze Api"></p>

<p align="center">
    <a href="https://packagist.org/packages/stephenjude/breeze-api">
        <img src="https://img.shields.io/packagist/dt/stephenjude/breeze-api" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/stephenjude/breeze-api">
        <img src="https://img.shields.io/packagist/v/stephenjude/breeze-api" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/stephenjude/breeze-api">
        <img src="https://img.shields.io/packagist/l/stephenjude/breeze-api" alt="License">
    </a>
</p>

## Introduction

Breeze Api provides a minimal and simple starting point for building a Laravel application with API authentication. APIs
are documented with [Enlighten](https://github.com/StydeNet/enlighten) and transformed
with [Laravel Responder](https://github.com/flugg/laravel-responder), Breeze Api publishes authentication controllers, routes
and data transformers to your application that can be easily customized based on your own application's needs.

Laravel Breeze is powered by Sanctum, [Laravel Responder](https://github.com/flugg/laravel-responder)
and [Enlighten](https://github.com/StydeNet/enlighten). .

Getting started couldn't be easier:

```bash
laravel new my-app

cd my-app

composer require stephenjude/breeze-api --dev

php artisan breeze-api:install
```

## Contributing

Thank you for considering contributing to Breeze! You can read the contribution guide [here](.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by
the [Code of Conduct](.github/CODE_OF_CONDUCT.md).

## Security Vulnerabilities

Please review [our security policy](https://github.com/stephenjude/breeze-api/security/policy) on how to report security
vulnerabilities.

## License

Laravel Breeze Api is open-sourced software licensed under the [MIT license](LICENSE.md).
