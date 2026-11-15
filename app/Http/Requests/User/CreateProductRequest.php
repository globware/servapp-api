<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class CreateProductRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare inputs for validation to support multiple parameter naming conventions.
     */
    protected function prepareForValidation(): void
    {
        $productId = $this->input('productId')
            ?? $this->input('userProductId')
            ?? $this->input('user_product_id')
            ?? $this->input('product_id');

        if ($productId !== null) {
            $this->merge([
                'user_product_id' => $productId,
                'productId' => $productId,
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
            'user_product_id' => 'required|integer|exists:user_products,id',
            'productId' => 'sometimes|integer|exists:user_products,id',
            'userProductId' => 'sometimes|integer|exists:user_products,id',
            'message' => 'nullable|string',
            'mediaIds' => 'nullable|array',
            'mediaIds.*' => 'integer|exists:files,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'user_product_id.required' => 'The productId or user_product_id field is required.',
            'user_product_id.exists' => 'The selected product does not exist.',
        ];
    }
}
