<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'store_name' => $this->store_name, 'slug' => $this->slug, 'mobile' => $this->mobile,
            'business_type' => $this->business_type, 'business_address' => $this->business_address,
            'city' => $this->city, 'state' => $this->state, 'country' => $this->country, 'pincode' => $this->pincode,
            'gst_number' => $this->gst_number, 'pan_number' => $this->pan_number, 'description' => $this->description,
            'categories' => $this->categories ?? [], 'status' => $this->status,
            'reviewed_at' => $this->reviewed_at?->toISOString(), 'rejection_reason' => $this->rejection_reason,
            'submitted_at' => $this->created_at?->toISOString(),
            'applicant' => $this->whenLoaded('user', fn () => $this->user->only(['id', 'name', 'email'])),
        ];
    }
}
