<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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

        if ($this->has('productId') && !$this->has('product_id')) {
            $merge['product_id'] = $this->productId;
        }
        if ($this->has('providerLocationId') && !$this->has('provider_location_id')) {
            $merge['provider_location_id'] = $this->providerLocationId;
        }
        if ($this->has('saveLocation') && !$this->has('save_location')) {
            $merge['save_location'] = $this->saveLocation;
        }
        if ($this->has('locationTitle') && !$this->has('location_title')) {
            $merge['location_title'] = $this->locationTitle;
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'provider_location_id' => 'nullable|integer|exists:provider_locations,id',
            'save_location' => 'nullable|boolean',
            'location_title' => 'nullable|string|max:255',
            'product_id' => 'required|exists:products,id',
            'mediaIds' => 'nullable|array',
            'mediaIds.*' => 'integer',
        ];
    }
}
