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
     * Prepare inputs for validation to support both camelCase and snake_case.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('provider_location_id') && !$this->has('providerLocationId')) {
            $merge['providerLocationId'] = $this->provider_location_id;
        }
        if ($this->has('save_location') && !$this->has('saveLocation')) {
            $merge['saveLocation'] = $this->save_location;
        }
        if ($this->has('location_title') && !$this->has('locationTitle')) {
            $merge['locationTitle'] = $this->location_title;
        }
        if (!empty($merge)) {
            $this->merge($merge);
        }
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
            "providerLocationId" => "nullable|integer|exists:provider_locations,id",
            "saveLocation" => "nullable|boolean",
            "locationTitle" => "nullable|string|max:255",
            "address" => "required_without:providerLocationId|nullable|string",
            "locationId" => "required_without:providerLocationId|nullable|integer|exists:locations,id",
            "email" => "nullable|email",
            "phoneNumbers" => "nullable|string",
            "long" => "nullable",
            "lat" => "nullable",
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
