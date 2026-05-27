# Laravel SaaS Admin Panel

A fully-featured multi-tenant SaaS admin panel built with Laravel 12 and PHP 8.5, demonstrating production-ready architecture patterns.

## Live Demo

- **Login:** `admin@demo.com` / `password`

## Features

### Authentication & Security
- Login / Logout with session management
- Forgot password & reset password via email
- Invitation-only registration — no public sign-up
- Role-based access control (admin / member) via Spatie Laravel Permission

### Multi-tenancy
- Each company has its own isolated data workspace
- Users are scoped to their company
- Admins manage all companies from a central panel

### Companies
- Create, edit, delete companies
- Soft deletes — companies are archived, not permanently removed
- View all users per company
- Export companies to CSV

### Users
- Full user list with search and filter by company
- Activate / deactivate users
- Delete users
- Export users to CSV

### Invitation System
- Admin sends invitation via email (Gmail SMTP)
- Invitation link with unique token, expires in 7 days
- User accepts invitation and creates their account
- Admin receives in-app and email notification when invitation is accepted

### Notifications
- In-app notification bell with unread count
- Email notifications via Gmail SMTP
- Mark as read / mark all as read

### Audit Log
- Every important action is recorded (who, what, when, from which IP)
- Actions tracked: company created/updated/deleted, invitation sent/cancelled, user toggled/deleted

### Dashboard
- Real-time stats: companies, users, pending invitations
- Line charts: companies and users registered in the last 30 days

### Profile
- Edit name and email
- Change password with current password verification

### Currency Exchange (Module)
- Live exchange rates from Banca Națională a României (BNR)
- Historical rates via BNR yearly XML feeds
- From / To direction toggle with currency selector
- Summary cards with variation badges (▲▼→) vs previous working day
- Variation chart (daily change %) and Nominal rate chart side by side
- Chart style toggle: Area (filled + points) or Line (thin, no fill)
- Global chart start date filter in sidebar
- Currency converter (any pair)
- In-memory cache for historical fetches (~10x faster chart loading)
- Supports 37 currencies in BNR-defined order

## Tech Stack

- **Backend:** PHP 8.5 / Laravel 12
- **Modules:** nwidart/laravel-modules
- **Auth:** Laravel Sanctum (API) + Session (Web)
- **Permissions:** Spatie Laravel Permission
- **Database:** MySQL
- **Frontend:** Blade + Tailwind CSS CDN (core), Vue 3 + Vite (modules)
- **Charts:** Chart.js
- **Email:** Gmail SMTP

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
| GET | /currency/rates | Yes | any |
| GET | /currency/historical?date=YYYY-MM-DD | Yes | any |

## Setup

```bash
git clone https://github.com/florentinmarianion/laravel-saas-demo.git
cd saas-demo
make install
```

Configure Gmail SMTP in `.env` before running:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
```

Default admin credentials: `admin@demo.com` / `password`

## Available Commands

| Command | Description |
|---------|-------------|
| `make install` | Full setup from scratch — dependencies, DB, all frontend assets |
| `make build` | Rebuild all frontend assets (core + modules) |
| `make dev` | Start all dev servers (Laravel + Vite + queue + logs) |
| `make fresh` | Wipe database and re-seed |
| `make clear` | Clear all Laravel caches |
