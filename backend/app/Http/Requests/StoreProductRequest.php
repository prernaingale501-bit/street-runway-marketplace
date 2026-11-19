<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'], 'description' => ['required', 'string', 'max:10000'],
            'category_id' => ['required', 'exists:categories,id'], 'brand_id' => ['nullable', 'exists:brands,id'],
            'price' => ['required', 'numeric', 'min:0.01'], 'mrp' => ['nullable', 'numeric', 'gte:price'],
            'gender' => ['nullable', 'in:women,men,kids,unisex'], 'status' => ['sometimes', 'in:draft,active,archived'],
            'images' => ['nullable', 'array', 'max:10'], 'images.*' => ['string', 'max:2048'],
            'specifications' => ['nullable', 'array'],
            'variants' => ['required', 'array', 'min:1'], 'variants.*.size' => ['nullable', 'string', 'max:50'],
            'variants.*.color' => ['nullable', 'string', 'max:80'], 'variants.*.color_hex' => ['nullable', 'string', 'max:16'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'], 'variants.*.stock' => ['required', 'integer', 'min:0'],
        ];
    }
}
