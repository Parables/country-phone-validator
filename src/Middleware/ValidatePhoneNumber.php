<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use App\Traits\FormatPhoneNumbers;

class ValidatePhoneNumber
{
    use FormatPhoneNumbers;

    /**
     * Format and validates only Ghanaian phone number. Regex Playground with test: regexr.com/6lu4j
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $validatedPhoneNumbers =   $this->validPhoneNumbers($request);

        // if none of the filled phone number fields is valid
        if (
            $request->anyFilled($this->requestKeys)  &&
            empty($validatedPhoneNumbers)
        ) {
            $message = 'Invalid phone number or service is unavailable in your country';
            return back()->withErrors(['phone_number' => $message])->withInput();
        }

        // else
        return $next($this->mergeWithRequest($request, $validatedPhoneNumbers));
    }
}
