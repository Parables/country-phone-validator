<?php

namespace App\Traits;

use Illuminate\Http\Request;

/**
 * Validates request that contains a phone number field
 */
trait FormatPhoneNumbers
{
    protected string $ussdPhoneNumber;
    protected array $requestKeys = ['phone_number', 'phoneNumber',];

    public function __construct()
    {
        $this->ussdPhoneNumber = config('ussd.phone_number');

        if (!empty($this->ussdPhoneNumber)) {
            array_push($this->requestKeys, $this->ussdPhoneNumber);
        }
    }


    protected function validPhoneNumbers(Request $request)
    {

        if ($request->anyFilled($this->requestKeys)) {
            $input = $request->input('phone_number')
                ?? $request->input('phoneNumber')
                ?? $request->input($this->ussdPhoneNumber);

            return array_map(
                'parsePhoneNumber',
                array_filter(
                    explode(',', $input),
                    'isValidPhoneNumber'
                )
            );
        }
        return [];
    }

    protected function handleApiResponse(Request $request)
    {
        $message = 'Invalid phone number or service is unavailable in your country';

        if ($request->routeIs('ussd')) {
            return   gatewayResponse(
                request: $request,
                message: $message,
                action: 'prompt',
            );
        } elseif ($request->expectsJson()) {
            return response(['message' => $message], 400);
        }
    }

    protected function mergeWithRequest(Request $request, array $validatedPhoneNumbers)
    {
        // convert array back to string
        $strPhoneNumbers = implode(',', $validatedPhoneNumbers);

        // replace the original phone number in the request request with the formatted input
        return  $request->merge(
            array_filter([
                'phone_number' => $request->filled('phone_number') ? $strPhoneNumbers : null,
                'phoneNumber' => $request->filled('phoneNumber') ? $strPhoneNumbers : null,
                $this->ussdPhoneNumber => $request->filled($this->ussdPhoneNumber) ? $strPhoneNumbers : null
            ])
        );
    }
}
