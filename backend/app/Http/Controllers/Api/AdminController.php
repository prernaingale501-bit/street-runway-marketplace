<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorApplicationResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorApplication;
use App\Services\VendorApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function overview()
    {
        return response()->json([
            'totals' => ['sales' => (float) Order::where('payment_status', 'paid')->sum('total'), 'orders' => Order::count(), 'customers' => User::where('role', 'customer')->count(), 'vendors' => User::where('role', 'vendor')->whereNotNull('vendor_approved_at')->count(), 'pending_vendor_applications' => VendorApplication::where('status', 'pending')->count(), 'products' => Product::count()],
            'recent_orders' => Order::with('user:id,name,email')->latest()->limit(10)->get(),
            'recent_vendor_applications' => VendorApplication::with('user:id,name,email')->latest()->limit(10)->get(),
        ]);
    }

    public function applications(Request $request)
    {
        $query = VendorApplication::with('user:id,name,email')->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return VendorApplicationResource::collection($query->paginate(30));
    }

    public function reviewApplication(Request $request, VendorApplication $application, VendorApplicationService $service)
    {
        $data = $request->validate(['status' => ['required', Rule::in(['approved', 'rejected', 'suspended'])], 'rejection_reason' => ['required_if:status,rejected', 'nullable', 'string', 'max:2000']]);

        return new VendorApplicationResource($service->review($application, $request->user(), $data['status'], $data['rejection_reason'] ?? null));
    }

    public function vendorDocument(VendorApplication $application, string $document)
    {
        abort_unless(in_array($document, ['business_logo', 'gst_certificate', 'identity_proof'], true), 404);
        $path = $application->documents[$document] ?? null;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }

    public function products(Request $request)
    {
        $query = Product::with(['category', 'brand', 'vendor:id,name'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return $query->paginate(30);
    }

    public function updateProductStatus(Request $request, Product $product)
    {
        $data = $request->validate(['status' => ['required', Rule::in(['draft', 'active', 'archived'])]]);
        $product->update($data);

        return response()->json(['message' => 'Product status updated.', 'product' => $product->fresh()]);
    }

    public function orders()
    {
        return Order::with(['user:id,name,email', 'items'])->latest()->paginate(30);
    }

    public function customers()
    {
        return User::where('role', 'customer')->withCount('orders')->latest()->paginate(30, ['id', 'name', 'email', 'created_at']);
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate(['is_suspended' => ['required', 'boolean']]);
        if ($user->role === 'admin' && $user->id === $request->user()->id && $data['is_suspended']) {
            return response()->json(['message' => 'You cannot suspend your own admin account.'], 422);
        }
        $user->update($data);

        return response()->json(['id' => $user->id, 'is_suspended' => $user->is_suspended]);
    }

    public function categories()
    {
        return Category::with('children')->orderBy('name')->get();
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'slug' => ['nullable', 'string', 'max:150', 'unique:categories,slug'], 'parent_id' => ['nullable', 'exists:categories,id'], 'is_active' => ['sometimes', 'boolean']]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        if (Category::where('slug', $data['slug'])->exists()) {
            return response()->json(['message' => 'The generated category slug is already in use.', 'errors' => ['slug' => ['Choose a unique slug.']]], 422);
        }

        return response()->json(Category::create($data), 201);
    }

    public function updateCategory(Request $request, Category $category)
    {
        $data = $request->validate(['name' => ['sometimes', 'string', 'max:120'], 'slug' => ['sometimes', 'string', 'max:150', Rule::unique('categories', 'slug')->ignore($category->id)], 'parent_id' => ['nullable', 'exists:categories,id'], 'is_active' => ['sometimes', 'boolean']]);
        $category->update($data);

        return $category->fresh();
    }

    public function deleteCategory(Category $category)
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted.']);
    }

    public function brands()
    {
        return Brand::orderBy('name')->get();
    }

    public function storeBrand(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'slug' => ['nullable', 'string', 'max:150', 'unique:brands,slug'], 'is_active' => ['sometimes', 'boolean']]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        if (Brand::where('slug', $data['slug'])->exists()) {
            return response()->json(['message' => 'The generated brand slug is already in use.', 'errors' => ['slug' => ['Choose a unique slug.']]], 422);
        }

        return response()->json(Brand::create($data), 201);
    }

    public function updateBrand(Request $request, Brand $brand)
    {
        $data = $request->validate(['name' => ['sometimes', 'string', 'max:120'], 'slug' => ['sometimes', 'string', 'max:150', Rule::unique('brands', 'slug')->ignore($brand->id)], 'is_active' => ['sometimes', 'boolean']]);
        $brand->update($data);

        return $brand->fresh();
    }

    public function deleteBrand(Brand $brand)
    {
        $brand->delete();

        return response()->json(['message' => 'Brand deleted.']);
    }

    public function coupons()
    {
        return Coupon::latest()->paginate(30);
    }

    public function storeCoupon(Request $request)
    {
        $request->merge(['code' => strtoupper((string) $request->input('code', ''))]);
        $data = $request->validate(['code' => ['required', 'string', 'max:80', 'unique:coupons,code'], 'type' => ['required', Rule::in(['percent', 'flat'])], 'value' => ['required', 'numeric', 'gt:0'], 'min_order' => ['sometimes', 'numeric', 'min:0'], 'max_discount' => ['nullable', 'numeric', 'gt:0'], 'starts_at' => ['nullable', 'date'], 'expires_at' => ['nullable', 'date', 'after:starts_at'], 'is_active' => ['sometimes', 'boolean']]);
        $data['code'] = strtoupper($data['code']);

        return response()->json(Coupon::create($data), 201);
    }

    public function updateCoupon(Request $request, Coupon $coupon)
    {
        if ($request->filled('code')) {
            $request->merge(['code' => strtoupper((string) $request->input('code'))]);
        }
        $data = $request->validate(['code' => ['sometimes', 'string', 'max:80', Rule::unique('coupons', 'code')->ignore($coupon->id)], 'type' => ['sometimes', Rule::in(['percent', 'flat'])], 'value' => ['sometimes', 'numeric', 'gt:0'], 'min_order' => ['sometimes', 'numeric', 'min:0'], 'max_discount' => ['nullable', 'numeric', 'gt:0'], 'starts_at' => ['nullable', 'date'], 'expires_at' => ['nullable', 'date', 'after:starts_at'], 'is_active' => ['sometimes', 'boolean']]);
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }
        $coupon->update($data);

        return $coupon->fresh();
    }

    public function deleteCoupon(Coupon $coupon)
    {
        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted.']);
    }
}
