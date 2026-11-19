<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VendorApplication;
use Illuminate\Database\Seeder;

class DevelopmentAccountsSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('Development demo accounts were not seeded outside local/testing.');

            return;
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@streetrunway.test'],
            ['name' => 'Street Runway Admin', 'password' => 'AdminDemo123!', 'role' => 'admin', 'is_suspended' => false],
        );
        User::updateOrCreate(
            ['email' => 'buyer@streetrunway.test'],
            ['name' => 'Demo Buyer', 'password' => 'BuyerDemo123!', 'role' => 'customer', 'is_suspended' => false],
        );
        $seller = User::updateOrCreate(
            ['email' => 'seller@streetrunway.test'],
            ['name' => 'Demo Seller', 'password' => 'SellerDemo123!', 'role' => 'customer', 'vendor_approved_at' => null, 'is_suspended' => false],
        );

        VendorApplication::updateOrCreate(
            ['user_id' => $seller->id],
            [
                'store_name' => 'Demo Seller Studio',
                'slug' => 'demo-seller-studio',
                'mobile' => '9999999999',
                'business_type' => 'Individual / sole proprietor',
                'business_address' => '10 Demo Street',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'pincode' => '400001',
                'pan_number' => 'ABCDE1234F',
                'description' => 'A demo application for testing the vendor review and approval workflow.',
                'categories' => ['women', 'accessories'],
                'documents' => [],
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'rejection_reason' => null,
            ],
        );
    }
}
