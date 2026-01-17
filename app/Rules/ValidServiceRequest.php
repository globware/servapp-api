<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use App\Services\ServiceRequestService;

class ValidServiceRequest implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $requestService = new ServiceRequestService;
        $request = $requestService->getRequest($value);
        if(!$request) $fail("This Service Request is invalid");

        // if($request->userService->user_id == Auth::user()->id) $fail("")
    }
}
