<?php

namespace Parables\CountryPhoneValidator;

use Parables\CountryPhoneValidator\Commands\CountryPhoneValidatorCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CountryPhoneValidatorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('country-phone-validator')
            ->hasConfigFile();
    }
}
