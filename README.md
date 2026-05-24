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

## Tech Stack

- **Backend:** PHP 8.5 / Laravel 12
- **Auth:** Laravel Sanctum (API) + Session (Web)
- **Permissions:** Spatie Laravel Permission
- **Database:** MySQL
- **Frontend:** Blade + Tailwind CSS CDN
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

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

Configure Gmail SMTP in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
```

Default admin credentials: `admin@demo.com` / `password`
