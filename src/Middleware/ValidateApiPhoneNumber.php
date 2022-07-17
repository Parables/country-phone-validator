<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Closure;
use App\Traits\FormatPhoneNumbers;

class ValidateApiPhoneNumber
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
        Log::info('Validate API Phone: ', $request->all());

        $validatedPhoneNumbers =   $this->validPhoneNumbers($request);

        if (
            $request->anyFilled($this->requestKeys)  &&
            empty($validatedPhoneNumbers)
        ) {
            return $this->handleApiResponse($request);
        }

        // else
        return $next($this->mergeWithRequest($request, $validatedPhoneNumbers));
    }
}
