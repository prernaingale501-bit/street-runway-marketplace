# Street Runway Marketplace

This repository contains two independent applications:

- `frontend/` — the existing React, Vite, and TypeScript marketplace frontend.
- `backend/` — the Laravel REST API with Sanctum authentication and vendor-application approval endpoints.

The frontend source and project files were moved without redesigning or rewriting them. Its original documentation and Lovable project configuration remain in `frontend/`.

## Run the frontend

```sh
cd frontend
npm install
npm run dev
```

Production build:

```sh
cd frontend
npm run build
```

## Run the backend

After Laravel is installed and its dependencies are available:

```sh
cd backend
php artisan serve
```

The frontend API base URL is configured in `frontend/.env.example`. Backend environment settings belong in `backend/.env`; never commit real credentials.

## Local demo logins

The following demo accounts are created by `php artisan db:seed` only when the Laravel environment is `local` or `testing`:

| Account | Email | Password | Starting state |
| --- | --- | --- | --- |
| Admin | `admin@streetrunway.test` | `AdminDemo123!` | Can review vendor applications |
| Vendor applicant | `seller@streetrunway.test` | `SellerDemo123!` | Pending admin approval |
| Buyer | `buyer@streetrunway.test` | `BuyerDemo123!` | Customer account |

### Test vendor approval

1. Start Laravel from `backend/` with `php artisan serve --host=127.0.0.1 --port=8000` and start the frontend from `frontend/` with `npm run dev`.
2. Sign in at `/portal/login` with the admin account, then open `/portal/admin` and approve the pending seller application.
3. Sign in with the vendor applicant account and open `/portal/vendor`. After approval, this account has vendor access.
4. To test a brand-new registration, submit `/portal/vendor-apply` with a new email, then review that application in the admin page.

Use separate browser profiles for admin and vendor sessions. These credentials are for local testing only; do not use them in production. The [GitHub Pages frontend preview](https://prernaingale501-bit.github.io/street-runway-marketplace/) does not currently have a hosted Laravel API, so these accounts only work when the frontend is connected to a running local backend.

## Git

The single Git repository remains at this project root. Root `.gitignore` covers dependency folders, generated output, caches, logs, and environment secrets for both applications.
