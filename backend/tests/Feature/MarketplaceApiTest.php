<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\VendorApplication;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MarketplaceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_login_and_logout_with_sanctum_tokens(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Asha Buyer', 'email' => 'asha@example.test', 'password' => 'secret-pass-123', 'password_confirmation' => 'secret-pass-123',
        ]);
        $response->assertCreated()->assertJsonPath('user.role', 'customer')->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);
        $token = $response->json('token');
        $this->withToken($token)->getJson('/api/user')->assertOk()->assertJsonPath('email', 'asha@example.test');
        $this->withToken($token)->postJson('/api/auth/logout')->assertOk();
    }

    public function test_public_catalog_filters_out_unapproved_sellers_and_supports_filters(): void
    {
        [$category, $brand] = $this->catalogFixtures();
        $vendor = $this->approvedVendor();
        $product = $this->product($vendor, $category, $brand, ['name' => 'Linen Wrap Dress', 'price' => 1200, 'mrp' => 2000, 'status' => 'active']);
        ProductVariant::create(['product_id' => $product->id, 'sku' => 'LIN-S-RED', 'size' => 'S', 'color' => 'Red', 'stock' => 5]);
        $pendingVendor = User::factory()->create();
        $hidden = $this->product($pendingVendor, $category, $brand, ['name' => 'Hidden product', 'status' => 'active']);

        $this->getJson('/api/products?category=women&brand=maison-kaya&gender=women&size=S&color=Red&min_price=1000&max_price=1500&discount=30')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.slug', $product->slug);
        $this->getJson('/api/products')->assertOk()->assertJsonMissing(['slug' => $hidden->slug]);
        $this->getJson('/api/products/'.$product->slug)->assertOk()->assertJsonPath('data.variants.0.size', 'S');
    }

    public function test_vendor_application_starts_pending_and_pending_user_cannot_use_seller_tools(): void
    {
        Storage::fake('local');
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);
        $response = $this->postJson('/api/vendor-applications', $this->applicationData())->assertCreated()->assertJsonPath('data.status', 'pending');
        $this->assertDatabaseHas('vendor_applications', ['user_id' => $customer->id, 'status' => 'pending']);
        $application = VendorApplication::firstOrFail();
        $this->get('/api/admin/vendor-applications/'.$application->id.'/documents/identity_proof')->assertForbidden();
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $this->get('/api/admin/vendor-applications/'.$application->id.'/documents/identity_proof')->assertOk();
        $this->getJson('/api/vendor/products')->assertForbidden();
    }

    public function test_admin_approval_unlocks_vendor_product_creation_and_public_listing(): void
    {
        Notification::fake();
        $customer = User::factory()->create();
        $application = VendorApplication::create($this->applicationModelData($customer));
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);
        $this->patchJson('/api/admin/vendor-applications/'.$application->id, ['status' => 'approved'])->assertOk()->assertJsonPath('data.status', 'approved');
        $this->assertNotNull($customer->fresh()->vendor_approved_at);
        [$category, $brand] = $this->catalogFixtures();
        Sanctum::actingAs($customer->fresh());
        $productResponse = $this->postJson('/api/vendor/products', $this->productData($category, $brand, 'active'));
        $productResponse->assertCreated()->assertJsonPath('data.status', 'active');
        $this->getJson('/api/products')->assertOk()->assertJsonFragment(['name' => 'Everyday Linen Shirt']);
    }

    public function test_admin_can_reject_application_and_rejection_reason_is_saved(): void
    {
        Notification::fake();
        $customer = User::factory()->create();
        $application = VendorApplication::create($this->applicationModelData($customer));
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);
        $this->patchJson('/api/admin/vendor-applications/'.$application->id, ['status' => 'rejected', 'rejection_reason' => 'Please provide a valid business certificate.'])
            ->assertOk()->assertJsonPath('data.status', 'rejected')->assertJsonPath('data.rejection_reason', 'Please provide a valid business certificate.');
        $this->assertSame('customer', $customer->fresh()->role);
        $this->getJson('/api/vendor/products')->assertForbidden();
    }

    public function test_vendor_cannot_edit_another_vendors_product(): void
    {
        [$category, $brand] = $this->catalogFixtures();
        $owner = $this->approvedVendor();
        $other = $this->approvedVendor('other-vendor@example.test');
        $product = $this->product($owner, $category, $brand);
        Sanctum::actingAs($other);
        $this->putJson('/api/vendor/products/'.$product->id, $this->productData($category, $brand))->assertNotFound();
        $this->deleteJson('/api/vendor/products/'.$product->id)->assertNotFound();
    }

    public function test_customer_can_manage_wishlist_and_addresses(): void
    {
        [$category, $brand] = $this->catalogFixtures();
        $vendor = $this->approvedVendor();
        $product = $this->product($vendor, $category, $brand, ['status' => 'active']);
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);
        $this->postJson('/api/wishlist', ['product_id' => $product->id])->assertCreated();
        $this->postJson('/api/wishlist', ['product_id' => $product->id])->assertCreated();
        $this->getJson('/api/wishlist')->assertOk()->assertJsonCount(1, 'data');
        $this->deleteJson('/api/wishlist/'.$product->id)->assertOk();
        $this->getJson('/api/wishlist')->assertOk()->assertJsonCount(0, 'data');

        $address = ['label' => 'Home', 'full_name' => 'Asha Buyer', 'phone' => '9999999999', 'line1' => '12 Main Road', 'city' => 'Pune', 'state' => 'Maharashtra', 'country' => 'India', 'pincode' => '411001'];
        $this->postJson('/api/addresses', $address)->assertCreated()->assertJsonPath('data.is_default', true);
        $this->getJson('/api/addresses')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_checkout_applies_coupon_creates_payment_reduces_stock_and_empties_cart(): void
    {
        [$category, $brand] = $this->catalogFixtures();
        $vendor = $this->approvedVendor();
        $product = $this->product($vendor, $category, $brand, ['status' => 'active', 'price' => 1200, 'mrp' => 2000]);
        $variant = ProductVariant::create(['product_id' => $product->id, 'sku' => 'SKU-M-01', 'size' => 'M', 'color' => 'Black', 'stock' => 4]);
        Coupon::create(['code' => 'WELCOME10', 'type' => 'percent', 'value' => 10, 'min_order' => 1000, 'max_discount' => 500, 'is_active' => true]);
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);
        $this->postJson('/api/cart/items', ['product_id' => $product->id, 'variant_id' => $variant->id, 'quantity' => 2])->assertCreated();
        $this->getJson('/api/cart')->assertOk()->assertJsonPath('subtotal', 2400);
        $response = $this->postJson('/api/orders', ['address' => ['full_name' => 'Asha Buyer', 'phone' => '9999999999', 'line1' => '12 Main Road', 'city' => 'Pune', 'state' => 'Maharashtra', 'country' => 'India', 'pincode' => '411001'], 'payment_method' => 'upi', 'coupon_code' => 'welcome10']);
        $response->assertCreated()->assertJsonPath('data.discount', 240)->assertJsonPath('data.shipping', 0)->assertJsonPath('data.total', 2160)->assertJsonPath('data.payment_status', 'pending');
        $this->assertDatabaseHas('product_variants', ['id' => $variant->id, 'stock' => 2]);
        $this->getJson('/api/cart')->assertOk()->assertJsonCount(0, 'items');
        $this->getJson('/api/orders/'.$response->json('data.order_number'))->assertOk();
        $this->getJson('/api/orders/'.$response->json('data.order_number').'/payment')->assertOk()->assertJsonPath('payments.0.status', 'pending');
        $this->postJson('/api/payments', ['order_id' => $response->json('data.id'), 'method' => 'upi'])->assertStatus(202)->assertJsonPath('status', 'pending')->assertJsonPath('provider', 'pending_integration');
    }

    public function test_checkout_rejects_insufficient_stock_and_invalid_coupon(): void
    {
        [$category, $brand] = $this->catalogFixtures();
        $vendor = $this->approvedVendor();
        $product = $this->product($vendor, $category, $brand, ['status' => 'active']);
        $variant = ProductVariant::create(['product_id' => $product->id, 'size' => 'S', 'stock' => 1]);
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);
        $this->postJson('/api/cart/items', ['product_id' => $product->id, 'variant_id' => $variant->id, 'quantity' => 1])->assertCreated();
        $this->postJson('/api/orders', ['address' => ['full_name' => 'Buyer', 'phone' => '1234567890', 'line1' => 'Somewhere', 'city' => 'Pune', 'state' => 'MH', 'country' => 'India', 'pincode' => '411001'], 'payment_method' => 'cod', 'coupon_code' => 'BADCODE'])->assertUnprocessable();
        $variant->update(['stock' => 0]);
        $this->postJson('/api/orders', ['address' => ['full_name' => 'Buyer', 'phone' => '1234567890', 'line1' => 'Somewhere', 'city' => 'Pune', 'state' => 'MH', 'country' => 'India', 'pincode' => '411001'], 'payment_method' => 'cod'])->assertUnprocessable();
    }

    public function test_order_and_address_data_are_scoped_to_authenticated_customer(): void
    {
        [$category, $brand] = $this->catalogFixtures();
        $vendor = $this->approvedVendor();
        $product = $this->product($vendor, $category, $brand, ['status' => 'active']);
        $variant = ProductVariant::create(['product_id' => $product->id, 'size' => 'M', 'stock' => 2]);
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);
        $this->postJson('/api/cart/items', ['product_id' => $product->id, 'variant_id' => $variant->id, 'quantity' => 1]);
        $order = app(CheckoutService::class)->place($owner, ['address' => ['full_name' => 'A', 'phone' => '1', 'line1' => 'L', 'city' => 'C', 'state' => 'S', 'country' => 'India', 'pincode' => '1'], 'payment_method' => 'cod']);
        $other = User::factory()->create();
        Sanctum::actingAs($other);
        $this->getJson('/api/orders/'.$order->order_number)->assertNotFound();
    }

    public function test_customer_can_cancel_unpaid_order_and_inventory_is_restored(): void
    {
        [$category, $brand] = $this->catalogFixtures();
        $vendor = $this->approvedVendor();
        $product = $this->product($vendor, $category, $brand, ['status' => 'active']);
        $variant = ProductVariant::create(['product_id' => $product->id, 'size' => 'M', 'stock' => 3]);
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);
        $this->postJson('/api/cart/items', ['product_id' => $product->id, 'variant_id' => $variant->id, 'quantity' => 2]);
        $order = app(CheckoutService::class)->place($customer, ['address' => ['full_name' => 'Buyer', 'phone' => '1234567890', 'line1' => 'Somewhere', 'city' => 'Pune', 'state' => 'MH', 'country' => 'India', 'pincode' => '411001'], 'payment_method' => 'cod']);
        $this->postJson('/api/orders/'.$order->order_number.'/cancel')->assertOk()->assertJsonPath('data.status', 'cancelled');
        $this->assertDatabaseHas('product_variants', ['id' => $variant->id, 'stock' => 3]);
        $this->postJson('/api/orders/'.$order->order_number.'/cancel')->assertStatus(409);
    }

    public function test_vendor_can_update_only_its_order_items_fulfillment(): void
    {
        [$category, $brand] = $this->catalogFixtures();
        $vendorA = $this->approvedVendor();
        $vendorB = $this->approvedVendor('vendor-b@example.test');
        $productA = $this->product($vendorA, $category, $brand, ['status' => 'active', 'slug' => 'vendor-a-item']);
        $productB = $this->product($vendorB, $category, $brand, ['status' => 'active', 'slug' => 'vendor-b-item']);
        $variantA = ProductVariant::create(['product_id' => $productA->id, 'size' => 'M', 'stock' => 2]);
        $variantB = ProductVariant::create(['product_id' => $productB->id, 'size' => 'M', 'stock' => 2]);
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);
        $this->postJson('/api/cart/items', ['product_id' => $productA->id, 'variant_id' => $variantA->id, 'quantity' => 1]);
        $this->postJson('/api/cart/items', ['product_id' => $productB->id, 'variant_id' => $variantB->id, 'quantity' => 1]);
        $order = app(CheckoutService::class)->place($customer, ['address' => ['full_name' => 'Buyer', 'phone' => '1234567890', 'line1' => 'Somewhere', 'city' => 'Pune', 'state' => 'MH', 'country' => 'India', 'pincode' => '411001'], 'payment_method' => 'cod']);
        $itemA = $order->items->firstWhere('vendor_id', $vendorA->id);
        $itemB = $order->items->firstWhere('vendor_id', $vendorB->id);
        Sanctum::actingAs($vendorA);
        $this->patchJson("/api/vendor/orders/{$order->id}/items/{$itemA->id}", ['fulfillment_status' => 'shipped'])->assertOk();
        $this->patchJson("/api/vendor/orders/{$order->id}/items/{$itemB->id}", ['fulfillment_status' => 'shipped'])->assertNotFound();
        Sanctum::actingAs($vendorB);
        $this->patchJson("/api/vendor/orders/{$order->id}/items/{$itemB->id}", ['fulfillment_status' => 'shipped'])->assertOk();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'shipped']);
    }

    public function test_review_requires_customer_purchase_for_verified_badge(): void
    {
        [$category, $brand] = $this->catalogFixtures();
        $vendor = $this->approvedVendor();
        $product = $this->product($vendor, $category, $brand, ['status' => 'active']);
        $customer = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($customer);
        $this->postJson('/api/products/'.$product->id.'/reviews', ['rating' => 5, 'title' => 'Lovely'])->assertCreated()->assertJsonPath('is_verified_purchase', false);
        Sanctum::actingAs($other);
        $this->postJson('/api/products/'.$product->id.'/reviews', ['rating' => 5, 'order_item_id' => 999])->assertUnprocessable();
    }

    public function test_admin_can_manage_categories_coupons_and_only_admin_can_access_admin_api(): void
    {
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);
        $this->getJson('/api/admin/dashboard')->assertForbidden();
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);
        $category = $this->postJson('/api/admin/categories', ['name' => 'Women', 'slug' => 'women'])->assertCreated()->json('id');
        $this->postJson('/api/admin/brands', ['name' => 'Maison Kaya'])->assertCreated();
        $this->postJson('/api/admin/coupons', ['code' => 'flat300', 'type' => 'flat', 'value' => 300, 'min_order' => 1499])->assertCreated()->assertJsonPath('code', 'FLAT300');
        $this->patchJson('/api/admin/categories/'.$category, ['is_active' => false])->assertOk()->assertJsonPath('is_active', false);
        $this->getJson('/api/admin/dashboard')->assertOk()->assertJsonPath('totals.customers', 1);
    }

    private function catalogFixtures(): array
    {
        $category = Category::create(['name' => 'Women', 'slug' => 'women', 'is_active' => true]);
        $brand = Brand::create(['name' => 'Maison Kaya', 'slug' => 'maison-kaya', 'is_active' => true]);

        return [$category, $brand];
    }

    private function approvedVendor(string $email = 'vendor@example.test'): User
    {
        return User::factory()->create(['email' => $email, 'role' => 'vendor', 'vendor_approved_at' => now(), 'is_suspended' => false]);
    }

    private function product(User $vendor, Category $category, Brand $brand, array $overrides = []): Product
    {
        return Product::create(array_merge(['vendor_id' => $vendor->id, 'category_id' => $category->id, 'brand_id' => $brand->id, 'name' => 'Everyday Linen Shirt', 'slug' => 'everyday-linen-shirt-'.uniqid(), 'description' => 'Breathable everyday linen.', 'price' => 1200, 'mrp' => 1800, 'gender' => 'women', 'images' => [], 'specifications' => [], 'status' => 'draft'], $overrides));
    }

    private function applicationData(): array
    {
        return ['store_name' => 'New Label', 'mobile' => '9999999999', 'business_type' => 'sole proprietor', 'business_address' => '12 Main Road', 'city' => 'Pune', 'state' => 'Maharashtra', 'country' => 'India', 'pincode' => '411001', 'pan_number' => 'ABCDE1234F', 'description' => 'Independent clothing label made in small batches.', 'categories' => ['women'], 'accept_terms' => true, 'identity_proof' => UploadedFile::fake()->image('identity.png')];
    }

    private function applicationModelData(User $user): array
    {
        return ['user_id' => $user->id, 'store_name' => 'New Label', 'slug' => 'new-label', 'mobile' => '9999999999', 'business_type' => 'sole proprietor', 'business_address' => '12 Main Road', 'city' => 'Pune', 'state' => 'Maharashtra', 'country' => 'India', 'pincode' => '411001', 'pan_number' => 'ABCDE1234F', 'description' => 'Independent clothing label made in small batches.', 'categories' => ['women'], 'status' => 'pending'];
    }

    private function productData(Category $category, Brand $brand, string $status = 'draft'): array
    {
        return ['name' => 'Everyday Linen Shirt', 'description' => 'Breathable everyday linen shirt.', 'category_id' => $category->id, 'brand_id' => $brand->id, 'price' => 1200, 'mrp' => 1800, 'gender' => 'women', 'status' => $status, 'images' => [], 'specifications' => ['Fabric' => 'Linen'], 'variants' => [['sku' => 'SKU-'.uniqid(), 'size' => 'M', 'color' => 'Black', 'stock' => 5]]];
    }
}
