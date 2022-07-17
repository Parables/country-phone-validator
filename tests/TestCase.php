<?php

namespace Parables\CountryPhoneValidator\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Parables\CountryPhoneValidator\CountryPhoneValidatorServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            CountryPhoneValidatorServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('country-phone-validator.countries', [
            '233' => [
                'country' => 'Ghana',
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
        ]);
    }
}
