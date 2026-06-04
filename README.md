# Laravel SaaS Platform

Multi-tenant cloud workspace platform where users can belong to multiple companies and access role-specific business applications. Similar in concept to Google Workspace, but oriented toward vertical business apps.

**Stack:** PHP 8.5 / Laravel 12 / MySQL / Vue 3 + Vite / Tailwind CSS / Alpine.js

---

## Requirements

- PHP 8.5+
- Composer
- Node.js 18+
- MySQL 8+
- WAMP / XAMPP / Laravel Herd

---

## Installation

```bash
git clone https://github.com/florentinmarianion/laravel-saas-demo
cd laravel-saas-demo

composer install

cp .env.example .env
php artisan key:generate
```

Configure `.env`:
```env
DB_DATABASE=saas_demo
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_FROM_ADDRESS=your@gmail.com
```

```bash
php artisan migrate
php artisan db:seed

npm install
npm run build

cd Modules/CurrencyExchange
npm install
npm run build
```

**Default admin:** `admin@demo.com` / `password`

---

## Architecture

### Multi-Tenancy

- Users are **global** — one account, multiple companies
- All business data scoped to `company_id`
- Active company stored in session (`active_company_id`)
- Users with multiple companies see a company selector on login

### Database Schema

| Table | Purpose |
|-------|---------|
| `users` | Platform users |
| `companies` | Tenant workspaces |
| `company_user` | Users ↔ Companies (role, is_active) |
| `apps` | Available platform apps |
| `app_company` | Apps enabled per company |
| `app_user_company` | App access per user per company |
| `app_user_permissions` | Granular permissions per user/company/app |
| `invitations` | Invitation tokens (email, role, permissions, expires_at) |
| `audit_logs` | Immutable action log (who, what, when, IP) |

### Permission System

Two layers:

**1. Spatie Roles & Permissions** — platform-level
- `admin` — bypasses all checks via `Gate::before`
- `member` — base role
- Named permissions: `companies.create`, `users.read`, `currency.view`, etc.

**2. App Permissions** (`app_user_permissions`) — granular per app
- Pattern: `{resource}.{action}` — e.g. `invoices.approve`, `salary.view`
- Checked via `AppContext::hasAppPermission('accounting', 'invoices.approve')`

### AppContext Service

`App\Services\AppContext` — central session helper:

```php
AppContext::user()                                    // current user
AppContext::company()                                 // active company
AppContext::companyId()                               // active company ID
AppContext::isAdmin()                                 // admin check
AppContext::hasAppAccess('currency-exchange')         // app access check
AppContext::hasAppPermission('accounting', 'invoices.approve') // granular check
AppContext::userCompanies()                           // all user's companies
AppContext::setCompany($id)                           // switch company
```

### Navigation (Circular Links)

```
Users → user.companies → apps.user → app.permissions.show
  ↑                                           ↓
users.index ←── companies.users ←── apps.company
```

---

## Modules

### CurrencyExchange (live)

**Routes:** `/currency`, `/currency/rates`, `/currency/historical?date=YYYY-MM-DD`

**Data source:** BNR XML — `curs.bnr.ro`
- Today's rates: cached 6h
- Year XML: cached 2h (current year), 30 days (past years)
- Historical date: cached 24h
- Weekend fallback: walks back up to 7 working days

**Frontend:** Vue 3 + Vite + Chart.js
- Summary cards with variation badges (▲▼→)
- Variation chart (% or absolute)
- Nominal rate chart
- Currency converter
- Area / Line chart toggle

### Future Modules

| Module | Status |
|--------|--------|
| HR Management | Planned |
| Accounting | Planned |
| Beauty Salon | Planned |
| Forex | Planned |
| Live Investments | Planned |

---

## Controllers

| Controller | Responsibility |
|-----------|---------------|
| `DashboardController` | Stats, 30-day charts, companies/invitations list |
| `CompanyController` | CRUD companies + AuditLog |
| `CompanyUserController` | Company users list, user's companies list |
| `CompanySwitchController` | Session company selector |
| `UserController` | Users list with search/filter, toggle, delete |
| `AppController` | CRUD apps, sync company apps, sync user apps |
| `AppPermissionController` | Granular per-app permissions per user/company |
| `UserPermissionController` | Spatie direct permissions per user |
| `PermissionController` | Spatie permission CRUD |
| `InvitationController` | Send/cancel invitations |
| `AcceptInvitationController` | Public token acceptance, account creation |
| `AuditLogController` | Paginated audit trail |
| `NotificationController` | In-app notifications |
| `ProfileController` | Edit profile, change password |
| `ExportController` | CSV export (companies, users) |

---

## Views

All views extend `resources/views/layouts/app.blade.php` which provides:
- Top navbar with dropdowns (Alpine.js)
- Company switcher (session-based)
- Notification bell
- Breadcrumbs slot
- Flash messages
- Responsive (mobile hamburger menu)

**Auth views** (public, no layout): `auth/login`, `auth/forgot-password`, `auth/reset-password`, `invitation/accept`, `invitation/expired`

---

## Routes Summary

```
GET  /dashboard
GET  /users                              users.index
GET  /users/{user}/companies             user.companies
GET  /users/{user}/companies/{company}/apps          apps.user
PUT  /users/{user}/companies/{company}/apps          apps.user.sync
GET  /users/{user}/companies/{company}/apps/{app}/permissions  app.permissions.show
PUT  /users/{user}/companies/{company}/apps/{app}/permissions  app.permissions.update
GET  /users/{user}/permissions           users.permissions
PUT  /users/{user}/permissions           users.permissions.update

GET  /companies                          companies.index
GET  /companies/{company}/users          companies.users
GET  /companies/{company}/apps           apps.company
PUT  /companies/{company}/apps           apps.company.sync
GET  /companies/{company}/edit           companies.edit

GET  /apps                               apps.index
GET  /permissions                        permissions.index
GET  /audit                              audit.index
GET  /profile                            profile.show
GET  /notifications                      notifications.index

GET  /select-company                     company.select
POST /switch-company                     company.switch

GET  /currency                           currency.index
GET  /currency/rates                     currency.rates
GET  /currency/historical                currency.historical
```

---

## Key Commands

```bash
# Development
php artisan serve
npm run dev                              # root assets
cd Modules/CurrencyExchange && npm run dev

# Database
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed --class=AppsSeeder

# Cache
php artisan optimize:clear

# Module assets
php artisan module:publish CurrencyExchange
```

---

## Seeded Data

**Apps:** Currency Exchange, HR Management, Accounting, Reports, Salon & Spa

**Permissions:** `companies.*`, `users.*`, `invitations.*`, `audit.read`, `currency.*`

**Roles:** `admin`, `member`

---

## Project Structure

```
app/
  Http/Controllers/        Platform controllers
  Models/                  User, Company, App, Invitation, AuditLog, AppUserPermission
  Services/AppContext.php  Session context helper
  Http/Middleware/
    EnsureActiveCompany.php

Modules/
  CurrencyExchange/        Live module (Vue 3 + BNR XML)

database/
  migrations/              Including app_user_permissions
  seeders/                 AdminUserSeeder, AppsSeeder, RolesAndPermissionsSeeder

resources/views/
  layouts/app.blade.php    Master layout
  dashboard, users, companies, apps, permissions, audit, profile, notifications
```
