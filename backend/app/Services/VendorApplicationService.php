<?php

namespace App\Services;

use App\Models\User;
use App\Models\VendorApplication;
use App\Notifications\VendorApplicationReviewed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorApplicationService
{
    public function submit(User $user, array $data): VendorApplication
    {
        $documents = [];
        foreach (['business_logo', 'gst_certificate', 'identity_proof'] as $key) {
            if (isset($data[$key])) {
                $documents[$key] = $data[$key]->store('vendor-applications', 'local');
            }
        }

        $base = Str::slug($data['store_name']);
        $slug = $base;
        for ($suffix = 2; VendorApplication::where('slug', $slug)->exists(); $suffix++) {
            $slug = $base.'-'.$suffix;
        }

        return VendorApplication::updateOrCreate(
            ['user_id' => $user->id],
            [...collect($data)->except(['business_logo', 'gst_certificate', 'identity_proof', 'accept_terms'])->all(), 'slug' => $slug, 'documents' => $documents, 'status' => 'pending', 'reviewed_by' => null, 'reviewed_at' => null, 'rejection_reason' => null],
        );
    }

    public function review(VendorApplication $application, User $admin, string $status, ?string $reason = null): VendorApplication
    {
        return DB::transaction(function () use ($application, $admin, $status, $reason) {
            $application->update(['status' => $status, 'reviewed_by' => $admin->id, 'reviewed_at' => now(), 'rejection_reason' => $status === 'rejected' ? $reason : null]);
            $vendor = $application->user;
            $vendor->forceFill([
                'role' => $status === 'approved' || $status === 'suspended' ? 'vendor' : 'customer',
                'vendor_approved_at' => $status === 'approved' ? now() : null,
                'is_suspended' => $status === 'suspended',
            ])->save();
            $vendor->notify(new VendorApplicationReviewed($application));

            return $application->refresh()->load('user');
        });
    }
}
