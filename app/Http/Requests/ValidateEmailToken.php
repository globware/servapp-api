<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use APp\Http\Requests\BaseRequest;

class ValidateEmailToken extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "token" => "required",
            "email" => "required|email"
        ];
    }
}
