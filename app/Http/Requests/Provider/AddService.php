<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

use App\Rules\UniqueService;

class AddService extends BaseRequest
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
            "serviceId" => "required|integer|exists:services,id",
            "address" => "required|string",
            "locationId" => "required|integer|exists:locations,id",
            "email" => "nullable|email",
            "phoneNumbers" => "nullable|string",
            "long" => "nullable|string",
            "lat" => "nullable|string",
            "minPrice" => "nullable|numeric",
            "maxPrice" => "nullable|numeric",
            "allDay" => "required_without:openingTime|boolean",
            "openingTime" => "required_without:allDay|required_if:allDay,false|date_format:h:i A",
            "closingTime" => "nullable|date_format:h:i A",
            "description" => "nullable|string",
            "tags" => "nullable|array",
            "tags.*" => "string",
            "mediaIds" => "nullable|array",
            "mediaIds.*" => "integer|exists:files,id"
        ];
    }
}
