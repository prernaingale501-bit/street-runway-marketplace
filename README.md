# Street Runway Marketplace

This repository contains two independent applications:

- `frontend/` — the existing React, Vite, and TypeScript marketplace frontend.
- `backend/` — the Laravel REST API application (Laravel installation pending PHP and Composer setup in this environment).

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

## Git

The single Git repository remains at this project root. Root `.gitignore` covers dependency folders, generated output, caches, logs, and environment secrets for both applications.
