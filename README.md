# Laravel SaaS Skeleton

A multi-tenant SaaS boilerplate built with Laravel 11, demonstrating production-ready architecture patterns.

## Features

- **Multi-tenancy** — each company has its own isolated data workspace
- **Invitation-only registration** — users cannot self-register; access is granted via signed invitation links
- **Role-based access control** — admin and member roles via Spatie Laravel Permission
- **Token-based API authentication** — Laravel Sanctum
- **Tiered user management** — companies with multiple sub-users
- **Soft deletes** — companies are archived, not permanently removed

## Tech Stack

- PHP 8.x / Laravel 11
- Laravel Sanctum (API auth)
- Spatie Laravel Permission (RBAC)
- MySQL

## API Endpoints

| Method | Endpoint | Auth | Role |
|--------|----------|------|------|
| POST | /api/login | No | — |
| POST | /api/logout | Yes | any |
| GET | /api/user | Yes | any |
| GET/POST | /api/companies | Yes | admin |
| GET/PUT/DELETE | /api/companies/{id} | Yes | admin |
| POST | /api/invitations | Yes | admin |
| GET | /api/invitations | Yes | any |
| POST | /api/invitations/{token}/accept | No | — |

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

Default admin: `admin@demo.com` / `password`