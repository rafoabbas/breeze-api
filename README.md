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

Breeze Api provides a minimal and simple starting point for building a Laravel application with API authentication.

Auth APIs are documented with [Enlighten](https://github.com/StydeNet/enlighten) and transformed
with [Laravel Responder](https://github.com/flugg/laravel-responder) and the tests asserted
with [Api Test Helper](https://github.com/stephenjude/api-test-helper).

Breeze Api publishes authentication controllers, routes and data transformers to your application that can be easily
customized based on your own application's needs.

Laravel Breeze is powered by [Sanctum](https://laravel.com/docs/8.x/sanctum)
, [Laravel Responder](https://github.com/flugg/laravel-responder), [Enlighten](https://github.com/StydeNet/enlighten)
and [Api Test Helper](https://github.com/stephenjude/api-test-helper).

Getting started couldn't be easier:

```bash
laravel new my-app

cd my-app

composer require stephenjude/breeze-api --dev

php artisan breeze-api:install
```

## Generating More Documentations

Breeze Api generates documentations for the scaffolded Authentication APIs but as you build your app, you will need to
generate more. All configurations for generating documentations has been scaffolded. 

Follow these simple steps:

- Execute the `php artisan enlighten:migrate` command to prepare your database for testing.
- Execute the `php artisan enlighten` command to run all your test suites.
- Execute the `php artisan enlighten:export` command to export documentation.

Check [Enlighten](https://github.com/StydeNet/enlighten#export-the-documentation-as-static-html-files) docs for more details on how to generate API documentations.

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
