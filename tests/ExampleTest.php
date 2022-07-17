<?php

it('can test', function () {
    expect(true)->toBeTrue();
});

    //returns a formatted string given a string
    (new CountryPhoneValidator())
    ->validate('0241234567')
    ->forCountry('Ghana')
    ->format();
// returns '233241234567'


// without format
(new CountryPhoneValidator())
    ->validate('233241234567')
    ->forCountry('Ghana');
    // return '233241234567'

    // works on Garbage In Garbage Out GIGO
    //returns a string given a string
    (new CountryPhoneValidator())
    ->validate('233241234567')
    ->forCountry('Ghana');
    //  return '233241234567';

    //returns a array given a array
    (new CountryPhoneValidator())
    ->validate('[233241234567]')
    ->forCountry('Ghana');
    //  return ['233241234567'];

    //returns a array given a array
    (new CountryPhoneValidator())
    ->validate([
        'phone1' => '233241234567'
        'something else' => 'sdhkjsdh',
        ])
    ->forCountry('Ghana');
    //  return [
        // 'phone1' => '233241234567'
        // 'something else' => null
        // ];

    //returns a formatted array given a array
    // returns null for invalid phone numbers
