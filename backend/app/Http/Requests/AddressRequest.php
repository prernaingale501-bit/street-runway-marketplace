<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['label' => ['nullable', 'string', 'max:80'], 'full_name' => ['required', 'string', 'max:160'], 'phone' => ['required', 'string', 'max:30'], 'line1' => ['required', 'string', 'max:255'], 'line2' => ['nullable', 'string', 'max:255'], 'city' => ['required', 'string', 'max:120'], 'state' => ['required', 'string', 'max:120'], 'country' => ['nullable', 'string', 'max:120'], 'pincode' => ['required', 'string', 'max:20'], 'is_default' => ['sometimes', 'boolean']];
    }
}
