<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

use App\Models\UserService;

class UniqueService implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $userServiceId = request()->route('serviceId');
        $service = UserService::where("name", $value)->where("user_id", Auth::user()->id)->first();
        
        if ($service && (!$userServiceId || $service->id != $userServiceId)) {
            $message = "Name already exist";
            $fail($message);
        }
    }
}
