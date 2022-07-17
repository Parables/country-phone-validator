
# A highly configurable country specific phone number validator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/parables/country-phone-validator.svg?style=flat-square)](https://packagist.org/packages/parables/country-phone-validator)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/parables/country-phone-validator/run-tests?label=tests)](https://github.com/parables/country-phone-validator/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/parables/country-phone-validator/Check%20&%20fix%20styling?label=code%20style)](https://github.com/parables/country-phone-validator/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/parables/country-phone-validator.svg?style=flat-square)](https://packagist.org/packages/parables/country-phone-validator)

Validate phone numbers for a specific a country. Simply pass in the supported cellular networks extensions and the package will ensure that all phone numbers are valid for a given country.

<!-- This package also includes a middleware you can add to your `web` and `api` routes to automatically validate and format requests containing phone numbers. -->

Find and add more countries and extensions [here]()

```php
(new CountryPhoneValidator())
    ->validate('233241234567')
    ->forCountry('Ghana');
```

## Installation

You can install the package via composer:

```bash
composer require parables/country-phone-validator
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="country-phone-validator-config"
```

This is the contents of the published config file:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Supported Countries
    |--------------------------------------------------------------------------
    |
    | For each supported country, pass in the country code as the key, and  an
    | array as the value containing the names/abbreviations(case insensitive)
    | for the `country` and a list of `networks_extensions` that operates
    | in that country.
    |
    */

    'countries' => [
        '233' => [
            'country' => 'Ghana|gh',
            'networks_extension' => [ // be explicit if 2 digit network extensions should be supported
                '020|20',  // vodafone
                '023|23',  // glo
                '024|24',  // mtn
                '026|26',  // airtel
                '027|27',  //tigo
                '028|28',  //
                '050|50',  // vodafone
                '054|54',  // mtn
                '055|55',  // mtn
                '056|56',  //airtel-tigo
                '057|57',  // tigo
                '059|59',  // mtn
            ],
        ]
    ],


];
```

## Usage

```php
// instance
(new CountryPhoneValidator())
    ->validate('233241234567')
    ->forCountry('Ghana');
    
// facade
CountryPhoneValidator::validate('233241234567')
    ->forCountry('Ghana');
    
// helper function
countryPhoneValidator()
    ->validate('233241234567')
    ->forCountry('Ghana');
```

## Using as middleware

<!-- This packages provides 2 middlewares: `ValidateApiPhoneNumber` and `ValidatePhoneNumber` for your `web` and `api` routes respectively -->
TODO: A sample usage of this pacakage as a middleware for web and api routes

## Extend countries with `.env`

We recommend that you add your supported countries to the `country-phone-validator` config file.

However, you can also add more extensions for a country by adding it to your `.env` file and it would be merged with those in the config file.

To extend with the `.env` file:

1. Wrap the value of the `COUNTRY_PHONE_VALIDATOR` env variable in quotes `""`.
2. Each entry is separated by a semi-colon `;`.
3. Every entry MUST start with the country code separated by a fat arrow `=>` followed by the names or abbreviations for the country.
4. Each country name or abbreviation (case insensitive) is separated by a pipe `|`.
5. Separate the country names with a fat arrow  `=>` and then append the network extensions.
6. Each extension code is separated by a pipe `|`.

Example:

```env
# .env

COUNTRY_PHONE_VALIDATOR="
233=>gh|Ghana=>024|24|020|20;
234=>Nigeria|NG|NGA=>0803|0806|0703|0706|0813|0816|0810|0814|0903|0906";
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](<https://github.com/Parables> Boltnoel/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [parables](<https://github.com/Parables> Boltnoel)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
