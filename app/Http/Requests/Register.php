<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;

use App\EnumClass;

class Register extends BaseRequest
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
            "type" => ["required", Rule::in(EnumClass::userTypes())],
            "firstname" => "required|string",
            "surname" => "required|string",
            "email" => "required|unique:users,email",
            "phoneNumber" => "required|string",
            "locationId" => "integer|exists:locations,id",
            "password" => "required|string|min:7|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/"
        ];
    }
}
