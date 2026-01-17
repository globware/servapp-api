<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

use App\Rules\UniqueService;

class UpdateService extends BaseRequest
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
            "name" => ["nullable", "string", new UniqueService()],
            "serviceId" => "nullable|integer|exists:services,id",
            "address" => "nullable|string",
            "locationId" => "nullable|integer|exists:locations,id",
            "coverPhotoId" => "nullable|integer|exists:files,id",
            "email" => "nullable|email",
            "phoneNumbers" => "nullable|string",
            "long" => "nullable|string",
            "lat" => "nullable|string",
            "minPrice" => "nullable|numeric",
            "maxPrice" => "nullable|numeric",
            "allDay" => "nullable|boolean",
            "openingTime" => "nullable|date_format:h:i A",
            "closingTime" => "nullable|date_format:h:i A",
            "description" => "nullable|string",
        ];
    }
}
