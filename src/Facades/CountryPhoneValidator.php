<?php

namespace Parables\CountryPhoneValidator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Parables\CountryPhoneValidator\CountryPhoneValidator
 */
class CountryPhoneValidator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'country-phone-validator';
    }
}
