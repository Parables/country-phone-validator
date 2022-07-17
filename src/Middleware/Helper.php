<?php

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * for record data that needs to be restored when session resumes,
 * call this helper function to prefix the key with `config('ussd.record_key_prefix')`
 * so that the record data will be preserved by the `PreserveUssdSessionData` Event Listener
 *
 * @param string $key
 * @return string
 */
function getRecordKey(string $key)
{
    Log::info('this record will be preserved: ' . $key);
    return config('ussd.record_key_prefix') . $key;
}

/**
 * to resume session, we store the sessionId in the cache with a key obtained by
 * prefixing the phoneNumber with `config('ussd.session_key_prefix')`
 *
 * @param string $phoneNumber
 * @return string
 */
function getTemporalCurrentSessionKey(string $phoneNumber)
{
    return config('ussd.session_key_prefix') . $phoneNumber;
}

/**
 * only call this when the session has ended with a action === 'prompt'
 *
 * @param string $phoneNumber
 * @return void
 */
function markSessionAsComplete(string $phoneNumber)
{
    $cacheKey = getTemporalCurrentSessionKey(phoneNumber: $phoneNumber);
    Cache::forget($cacheKey);
}

/**
 * get the key used to preserve the session data
 *
 * @param string $phoneNumber
 * @param string $sessionId
 * @return string
 */
function getPreservedUssdSessionDataKey(string $phoneNumber, string $sessionId)
{
    return config('ussd.preserved_key_prefix') . $phoneNumber . '_' . $sessionId;
}

/**
 * when user decides not to resume from a previous session, there is no need to keep the preserved data
 *
 * @param string $phoneNumber
 * @param string $sessionId
 * @return void
 */
function deletePreservedData(string $phoneNumber, string $sessionId)
{
    $sessionKey = getPreservedUssdSessionDataKey(phoneNumber: $phoneNumber, sessionId: $sessionId);
    Cache::forget($sessionKey);

    Log::info('Deleting preserved data with key: ' . $sessionKey);
}


// TODO: document this helper function
function extractInput(string $argument)
{
    $arrInput = explode('*', $argument);
    $input = end($arrInput);
    Log::info('User selected option ' . $input);
    return $input;
}

// TODO: document this helper function
function getInputPrefix(string $argument): string
{
    return implode('*', array_slice(explode('*', $argument), 0, -1));
}

/**
 * With AfricasTalking, you need to include all the data entered following by a * and the option selected
 * africas_talking: option = 2; returns *842*23#1*4*3*2 where the last digit is the selected option
 * arkesel: just pass in the option
 *
 * @param string $argument
 * @param string $option
 * @return string
 */
function parseOption(string $argument, string $option): string
{
    return isGatewayProviderArkesel() ? $option ?? '' : getInputPrefix($argument) . '*' . $option;
}

/**
 * Returns true if the GATEWAY_PROVIDER env variable is set to ARKESEL
 *
 * @return string
 */
function isGatewayProviderArkesel()
{
    return config('ussd.gateway_provider') === 'arkesel';
}

/**
 * Arkesel expects a json response while AfricasTalking expects a string that starts with either CON or END
 *
 * @param string $message
 * @return array|string
 */
function gatewayResponse(Request $request, string $message, string $action): array|string
{
    $continueSession = $action === 'input';
    Log::info("USSD Message: $message \n Action: $action");
    if (isGatewayProviderArkesel()) {
        return [
            'sessionID' => $request->sessionID,
            'userID' => $request->userID,
            'msisdn' => $request->msisdn,
            'message' => $message,
            'continueSession' => $continueSession,
        ];
    }
    // prefix message with 'CON' or 'END' when using 'africas_talking' as the gateway provider
    return $continueSession ? 'CON' . $message : 'END' . $message;
}


// TODO: document this helper function
function isValidPhoneNumber(string $input)
{
    $input = trim($input);
    $matches = [];

    preg_match(generateCountryPhoneNetworkPattern($input), $input, $matches);

    return isset($matches) && !empty($matches[0]);
}

// TODO: document this helper function
function nineOrTen($input)
{
    $input = trim($input);

    return strlen($input) >= 9 && strlen($input) <= 10;
}

// TODO: document this helper function
function generateCountryPhoneNetworkPattern(string $input)
{
    $input = trim($input);

    // TODO: include missing ext for mtn
    // TODO: make this extendable though env vars
    $supportedCountryNetworks = [
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
    ];

    $pattern = "/(\+?)(";

    $patternForNineTenDigits = nineOrTen(trim($input));

    foreach ($supportedCountryNetworks as $countryCode => $data) {
        $arrCountryNetworkExt =  array_map(
            fn ($ext) =>
            $patternForNineTenDigits
                ? $ext
                : '(' . $countryCode . '(' . $ext . ')' . ')', //E.g: (233(024|24))
            $data['networks_extension']
        );
        $pattern .= implode('|', $arrCountryNetworkExt);
    }

    $pattern .= ')(\d{7})/';

    return $pattern;
}

// TODO: document this helper function
function parsePhoneNumber(string $input)
{
    // remove the country code  `+` and network code `0` from the phone number
    $input = str_replace(['+2330', '2330', '+233',], '233', trim($input));

    // format valid 10 digit GH phone number to use the country code
    if (nineOrTen($input)) {
        $input = '233' . substr($input, -9);
    }
    return trim($input);
}

// TODO: document this helper function
function maybeValue(mixed $value, mixed $default = null)
{
    Log::info('maybeValue', ['value' => $value, 'default' => $default]);
    if (is_callable($default)) {
        return $default($value);
    }
    return  empty($value) ? $default : $value;
}


// TODO: document this helper function
function arrayFilled(array $data, array|string $key, bool $allFilled = false)
{
    Log::info('arrayFilled', ['key' => $key, 'allFilled' => $allFilled, 'data' => $data,]);
    return  array_reduce(Arr::wrap($key), function ($carry, $item) use ($data, $allFilled) {
        $isFilled = array_key_exists($item, $data) && !empty($data[$item]);
        return $allFilled
            ? $carry && $isFilled
            : $carry || $isFilled;
    }, $allFilled);
}
