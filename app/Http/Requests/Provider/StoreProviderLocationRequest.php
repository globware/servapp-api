<?php

namespace App\Http\Requests\Provider;

use App\Http\Requests\BaseRequest;

class StoreProviderLocationRequest extends BaseRequest
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

        if ($this->has('countryId') && !$this->has('country_id')) {
            $merge['country_id'] = $this->countryId;
        }
        if ($this->has('stateId') && !$this->has('state_id')) {
            $merge['state_id'] = $this->stateId;
        }
        if ($this->has('locationId') && !$this->has('location_id')) {
            $merge['location_id'] = $this->locationId;
        }
        if ($this->has('lat') && !$this->has('latitude')) {
            $merge['latitude'] = $this->lat;
        }
        if ($this->has('long') && !$this->has('longitude')) {
            $merge['longitude'] = $this->long;
        } elseif ($this->has('lng') && !$this->has('longitude')) {
            $merge['longitude'] = $this->lng;
        }
        if ($this->has('isDefault') && !$this->has('is_default')) {
            $merge['is_default'] = $this->isDefault;
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
            'title' => 'nullable|string|max:255',
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'country_id' => 'nullable|integer|exists:countries,id',
            'state_id' => 'nullable|integer|exists:states,id',
            'location_id' => 'nullable|integer|exists:locations,id',
            'is_default' => 'nullable|boolean',
        ];
    }
}
