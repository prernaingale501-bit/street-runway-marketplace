# Backend

Independent Laravel REST API and Filament admin application. No marketplace domain features have been added yet.

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

6. To access the Filament panel, create a local administrator account with `php artisan make:filament-user`. The panel is at `/admin`.

The checked-in `.env.example` contains placeholders only. Keep actual credentials and generated keys in the ignored `.env` file.

## Run

```sh
php artisan serve --host=127.0.0.1 --port=8000
```

The frontend should use `http://localhost:8000/api` as its API base URL. Sanctum stateful domains and credentialed CORS are configured for the local Vite origins; update them for deployment.

## API and structure

- `routes/api.php` — API route entry point; currently only the Sanctum-protected `/api/user` starter route.
- `app/Models`, `app/Http/Controllers` — Eloquent models and controllers.
- `app/Http/Requests`, `app/Http/Resources` — request validation and API serialization.
- `app/Services`, `app/Policies`, `app/Http/Middleware` — service, authorization, and middleware layers.
- `app/Notifications`, `app/Jobs`, `app/Events`, `app/Enums` — async and domain organization areas.
- `app/Providers/Filament/AdminPanelProvider.php` — Filament panel at `/admin`.

The domain folders are intentionally empty until marketplace requirements are implemented.
