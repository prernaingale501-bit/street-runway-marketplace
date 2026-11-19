# Backend

Independent Laravel REST API and Filament admin application. The current backend implements a testable marketplace API foundation; external payment processing and production deployment configuration remain unconnected.

## Stack

- PHP 8.5.12 (Laravel 13 requires PHP 8.3+)
- Laravel Framework 13.43.0
- MySQL configuration in `.env.example`
- Laravel Sanctum 4.5.0
- Filament 5.11.4

## Local setup

1. Install PHP 8.3+, Composer, and the PHP `pdo_mysql` extension.
2. Install dependencies: `composer install`.
3. Copy `.env.example` to `.env` if no local `.env` exists, then set the MySQL database name, username, and password locally.
4. Create the database, for example:

   ```sql
   CREATE DATABASE fashion_marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

5. Generate the local application key and apply migrations:

   ```sh
   php artisan key:generate
   php artisan migrate
   php artisan storage:link
   ```

6. Create a local Filament user with `php artisan make:filament-user`, then promote that account from Tinker so the admin role guard permits access:

   ```sh
   php artisan tinker
   ```

   ```php
   App\Models\User::where('email', 'admin@example.test')->update(['role' => 'admin']);
   ```

   The panel is at `/admin`.

For local workflow testing, run `php artisan db:seed`. In `local` or `testing` environments only, this creates these demo accounts:

| Role / state | Email | Password |
| --- | --- | --- |
| Admin | `admin@streetrunway.test` | `AdminDemo123!` |
| Buyer | `buyer@streetrunway.test` | `BuyerDemo123!` |
| Seller applicant (pending) | `seller@streetrunway.test` | `SellerDemo123!` |

Never use these accounts outside local testing. To test a fresh vendor registration, open `/portal/vendor-apply` in the frontend and submit the form using a new email address. Sign in to `/portal/admin` with the admin account to approve or reject the resulting application, then refresh the applicant's status page. The seeded seller applicant is also available in the review queue immediately.

The checked-in `.env.example` contains placeholders only. Keep actual credentials and generated keys in the ignored `.env` file.

## Run

```sh
php artisan serve --host=127.0.0.1 --port=8000
```

The frontend uses `http://localhost:8000/api` as its local API base URL. API authentication uses Sanctum bearer tokens. CORS allows local Vite origins; update allowed origins for deployment.

## API and structure

- `routes/api.php` — API entry point for auth, catalog, vendor, cart, order, payment-intent, review, and admin routes.
- `app/Models`, `app/Http/Controllers` — Eloquent models and controllers.
- `app/Http/Requests`, `app/Http/Resources` — request validation and API serialization.
- `app/Services`, `app/Policies`, `app/Http/Middleware` — checkout/product/application services and role/approval guards.
- `app/Notifications`, `app/Jobs`, `app/Events`, `app/Enums` — review notifications, async/domain organization areas and status types.
- `app/Providers/Filament/AdminPanelProvider.php` — Filament panel at `/admin`.

## Implemented API areas

- Customer registration, login, logout, password-reset endpoints, Sanctum tokens, profile identity.
- Public catalog with category, brand, gender, price, size, color, discount, search, and sort filters; public stores and product reviews.
- Vendor application submission/status, admin approve/reject/suspend workflow, and approval-gated vendor products, inventory variants, orders, shipping status, and dashboard totals.
- Customer addresses, wishlist, persistent cart, coupons, checkout, order history/cancellation, and payment-intent records.
- Admin dashboard data and endpoints for applications, customers, products, orders, categories, brands, coupons, and account suspension.

Payment intents deliberately remain `pending_integration`; no payment provider is configured and no real charges are made. Configure a provider and webhook verification before accepting payment. Vendor documents are stored on Laravel's private local disk. Configure private document review access and production storage before deployment.

## Test scenarios

Run the suite with:

```sh
php artisan test
```

The feature suite covers registration/token logout, catalog visibility and filters, pending and approved seller access, admin approval/rejection, vendor ownership, wishlist/addresses, coupon checkout totals, stock changes, payment intent status, order ownership/cancellation/restock, vendor fulfillment scoping, reviews, and admin authorization/catalog management. Tests use in-memory SQLite from `phpunit.xml`; they do not require the local MySQL server.

Run Laravel formatting checks with `vendor/bin/pint --test app routes database tests`.
