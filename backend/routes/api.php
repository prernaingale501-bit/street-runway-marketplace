<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\VendorApplicationController;
use App\Http\Controllers\Api\VendorOrderController;
use App\Http\Controllers\Api\VendorProductController;
use App\Http\Controllers\Api\VendorStoreController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Middleware\EnsureApprovedVendor;
use App\Http\Middleware\EnsureUserRole;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');
});

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);
Route::get('/brands', [ProductController::class, 'brands']);
Route::get('/stores/{slug}', [VendorStoreController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/vendor-applications', [VendorApplicationController::class, 'store']);
    Route::get('/vendor-applications/me', [VendorApplicationController::class, 'mine']);

    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart/items', [CartController::class, 'store']);
    Route::patch('/cart/items/{item}', [CartController::class, 'update']);
    Route::delete('/cart/items/{item}', [CartController::class, 'destroy']);

    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy']);

    Route::apiResource('/addresses', AddressController::class)->except(['show']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
    Route::post('/orders/{orderNumber}/cancel', [OrderController::class, 'cancel']);
    Route::get('/orders/{orderNumber}/payment', [OrderController::class, 'payment']);
    Route::post('/payments', [PaymentController::class, 'initiate']);
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store']);
});

Route::prefix('vendor')->middleware(['auth:sanctum', EnsureApprovedVendor::class])->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $vendor = $request->user();

        return response()->json(['orders' => OrderItem::where('vendor_id', $vendor->id)->distinct('order_id')->count('order_id'), 'revenue' => (float) OrderItem::where('vendor_id', $vendor->id)->whereHas('order', fn ($q) => $q->where('payment_status', 'paid'))->sum(DB::raw('unit_price * quantity')), 'products' => $vendor->products()->count(), 'pending_items' => OrderItem::where('vendor_id', $vendor->id)->where('fulfillment_status', 'pending')->count()]);
    });
    Route::apiResource('/products', VendorProductController::class)->except(['show']);
    Route::get('/orders', [VendorOrderController::class, 'index']);
    Route::patch('/orders/{order}/items/{item}', [VendorOrderController::class, 'updateItem']);
});

Route::prefix('admin')->middleware(['auth:sanctum', EnsureUserRole::class.':admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'overview']);
    Route::get('/vendor-applications', [AdminController::class, 'applications']);
    Route::patch('/vendor-applications/{application}', [AdminController::class, 'reviewApplication']);
    Route::get('/vendor-applications/{application}/documents/{document}', [AdminController::class, 'vendorDocument']);
    Route::get('/products', [AdminController::class, 'products']);
    Route::patch('/products/{product}/status', [AdminController::class, 'updateProductStatus']);
    Route::get('/orders', [AdminController::class, 'orders']);
    Route::get('/customers', [AdminController::class, 'customers']);
    Route::patch('/users/{user}', [AdminController::class, 'updateUser']);
    Route::get('/categories', [AdminController::class, 'categories']);
    Route::post('/categories', [AdminController::class, 'storeCategory']);
    Route::patch('/categories/{category}', [AdminController::class, 'updateCategory']);
    Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory']);
    Route::get('/brands', [AdminController::class, 'brands']);
    Route::post('/brands', [AdminController::class, 'storeBrand']);
    Route::patch('/brands/{brand}', [AdminController::class, 'updateBrand']);
    Route::delete('/brands/{brand}', [AdminController::class, 'deleteBrand']);
    Route::get('/coupons', [AdminController::class, 'coupons']);
    Route::post('/coupons', [AdminController::class, 'storeCoupon']);
    Route::patch('/coupons/{coupon}', [AdminController::class, 'updateCoupon']);
    Route::delete('/coupons/{coupon}', [AdminController::class, 'deleteCoupon']);
});
