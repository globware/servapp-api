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
        if ($this->has('productId') && !$this->has('product_id')) {
            $this->merge([
                'product_id' => $this->productId,
            ]);
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
            'product_id' => 'required|exists:products,id',
            'mediaIds' => 'nullable|array',
            'mediaIds.*' => 'integer',
        ];
    }
}
