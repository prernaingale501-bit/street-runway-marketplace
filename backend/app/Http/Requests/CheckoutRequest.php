<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'address' => ['required_without:address_id', 'array'],
            'address.full_name' => ['required_with:address', 'string', 'max:160'],
            'address.phone' => ['required_with:address', 'string', 'max:30'],
            'address.line1' => ['required_with:address', 'string', 'max:255'],
            'address.line2' => ['nullable', 'string', 'max:255'],
            'address.city' => ['required_with:address', 'string', 'max:120'],
            'address.state' => ['required_with:address', 'string', 'max:120'],
            'address.country' => ['required_with:address', 'string', 'max:120'],
            'address.pincode' => ['required_with:address', 'string', 'max:20'],
            'payment_method' => ['required', 'in:cod,upi,card,netbanking'],
            'coupon_code' => ['nullable', 'string', 'max:80'],
        ];
    }
}
