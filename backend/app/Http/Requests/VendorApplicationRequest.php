<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:160'], 'mobile' => ['required', 'string', 'max:30'],
            'business_type' => ['required', 'string', 'max:100'], 'business_address' => ['required', 'string', 'max:2000'],
            'city' => ['required', 'string', 'max:120'], 'state' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:120'], 'pincode' => ['required', 'string', 'max:20'],
            'gst_number' => ['nullable', 'string', 'max:80'], 'pan_number' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'categories' => ['required', 'array', 'min:1'], 'categories.*' => ['string', 'max:80'],
            'business_logo' => ['nullable', 'image', 'max:5120'],
            'gst_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'identity_proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'accept_terms' => ['required', 'accepted'],
        ];
    }
}
