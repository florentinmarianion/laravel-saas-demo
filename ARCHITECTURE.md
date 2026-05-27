# Architecture — Laravel SaaS Cloud Platform

## Vision

A multi-tenant, multi-app cloud workspace platform where users can belong to multiple companies
and access role-specific business applications. Similar in concept to Google Workspace or
Microsoft 365, but oriented toward vertical business apps (Beauty Salon, Dental, HR, Accounting, etc.).

Each app is an independent Laravel Module with its own logic, database migrations, frontend
assets and routes — but connected to the shared platform for auth, permissions, notifications and inter-app data.

---

## Platform Layers

```
┌─────────────────────────────────────────────────────┐
│            Cloud Admin (root project)               │
│  Companies · Users · Apps · Invitations · Audit     │
│  Global Permissions · Notifications · Dashboard     │
└─────────────────────────────────────────────────────┘
              │                    │
    ┌─────────┴──────┐    ┌────────┴────────┐
    │  Company A     │    │   Company B     │
    │  CurrencyEx    │    │  Beauty Salon   │
    │  Accounting    │    │  HR             │
    │  HR            │    │  Accounting     │
    └────────────────┘    └─────────────────┘
```

---

## Database Schema

### Core Tables

| Table | Purpose |
|-------|---------|
| `users` | Platform users (email, name, password) |
| `companies` | Tenants — each company is an isolated workspace |
| `company_user` | Many-to-many: which users belong to which company |
| `apps` | Available platform apps (Beauty Salon, HR, Accounting, etc.) |
| `app_company` | Which apps are enabled per company |
| `app_user_company` | Granular permissions per user per company per app |
| `invitations` | Invitation tokens to join a company |
| `audit_logs` | Every action recorded (who, what, when, from which IP) |
| `notifications` | In-app notifications (Laravel standard) |

### Permission Tables (Spatie)

| Table | Purpose |
|-------|---------|
| `roles` | Named roles (Admin, Accountant, HR Manager, custom) |
| `permissions` | Named permissions |
| `model_has_roles` | Role assigned to a user |
| `model_has_permissions` | Direct permission on a user |
| `role_has_permissions` | Permissions belonging to a role |

### Key Relationships

```
User ──< company_user >── Company
Company ──< app_company >── App
User ──< app_user_company >── App (per Company context)
```

---

## Multi-Tenancy Model

- Users are **global** (one account, multiple companies)
- All business data is **scoped to company_id**
- A user logs in globally, then **selects the active company context**
- Within a company context, the user sees only apps they have access to
- Admins of the root platform can manage all companies and apps

**Session structure (proposed):**
```php
session([
    'active_company_id' => $company->id,
    'active_company'    => $company->name,
]);
```

---

## Application (App/Module) Architecture

Each app lives in `Modules/{AppName}/` and follows nwidart/laravel-modules structure:

```
Modules/
  CurrencyExchange/          ← live, deployed
  BeautySalon/               ← future
  Accounting/                ← future
  HR/                        ← future
  Forex/                     ← future
  LiveInvestments/           ← future
```

Each module has:
- Its own routes, controllers, models, migrations
- Its own Vue 3 + Vite frontend (or Blade)
- Its own permissions registered at boot
- A `module.json` with metadata (name, version, dependencies)

**Module registration in `apps` table:**
```json
{
  "name": "Currency Exchange",
  "slug": "currency-exchange",
  "module": "CurrencyExchange",
  "icon": "currency",
  "version": "1.0.0",
  "permissions": ["view", "convert", "export"]
}
```

---

## Permission System

### Roles (Templates)

Roles are **permission templates**, not fixed system roles.
Examples: `Admin`, `Accountant`, `HR Manager`, `Dentist`, `Receptionist`, `Dark Priest` (custom)

Each role has a set of default permissions per app. When assigning a role to a user,
the permissions are copied — but can be overridden individually per user.

### Granular Permissions per App

Permissions follow the pattern: `{app}.{action}`

| Example | Meaning |
|---------|---------|
| `accounting.view` | Read-only access to Accounting |
| `accounting.create` | Can create entries |
| `accounting.invoices.approve` | Custom: can approve invoices |
| `hr.view_salaries` | Custom: can see salary data |
| `forex.live_trade` | Custom: can execute live trades |

**`app_user_company` proposed schema:**
```sql
user_id       BIGINT
company_id    BIGINT
app_id        BIGINT
permission    VARCHAR(100)   -- e.g. "accounting.invoices.approve"
granted       BOOLEAN
PRIMARY KEY (user_id, company_id, app_id, permission)
```

**Permission check helper (proposed):**
```php
// In any controller or blade
if (userHasAppPermission('accounting', 'invoices.approve')) {
    // show approve button
}
```

---

## Inter-App Communication

Apps communicate through **Laravel Events** and a **shared data layer**.

### Event Bus (Laravel Events)

Each module fires domain events. Other modules can listen:

```php
// HR Module fires
event(new EmployeeContractUpdated($employee, $newSalary));

// Accounting Module listens
class SyncEmployeeCostOnContractUpdate {
    public function handle(EmployeeContractUpdated $event): void {
        AccountingEntry::syncSalary($event->employee, $event->salary);
    }
}
```

### Shared Data Layer (proposed)

A `shared_data` service available to all modules:

```php
// HR writes
SharedData::set('hr.employee.{id}.salary', $salary, context: $companyId);

// Accounting reads
$salary = SharedData::get('hr.employee.{id}.salary', context: $companyId);
```

Or via internal API endpoints — each module exposes internal routes
accessible only within the platform:

```
GET /internal/hr/employees/{id}          → employee data for Accounting
GET /internal/accounting/balance/{year}  → financial summary for Dashboard
```

### Practical Integration Examples

| Source App | Event | Listening App | Action |
|------------|-------|---------------|--------|
| HR | `EmployeeHired` | Accounting | Create payroll entry |
| HR | `EmployeeTerminated` | Accounting | Close payroll |
| Sales | `InvoicePaid` | Accounting | Record revenue |
| Accounting | `BudgetExceeded` | HR | Flag department |
| BeautySalon | `AppointmentBooked` | Accounting | Record expected revenue |

---

## Authentication & Security

- **Web routes**: Session-based (Laravel Session + CSRF)
- **API routes**: Token-based (Laravel Sanctum)
- **Invitation-only registration** — no public sign-up
- **Password reset** via email (Gmail SMTP)
- **Audit log** — every important action recorded automatically
- **Role-based access** via Spatie Laravel Permission
- **App-level access** via `app_user_company` + custom permission check

---

## File Structure (Key Files)

```
app/
  Http/Controllers/
    AppController.php              -- Manage platform apps
    CompanyController.php          -- CRUD companies
    CompanyUserController.php      -- Assign users to companies
    UserPermissionController.php   -- Manage per-user permissions
    PermissionController.php       -- Manage roles & permissions
    InvitationController.php       -- Send/manage invitations
    AcceptInvitationController.php -- Public: accept invite token
    AuditLogController.php         -- View audit trail
    NotificationController.php     -- In-app notifications
    DashboardController.php        -- Root dashboard stats
    ExportController.php           -- CSV exports
    ProfileController.php          -- User profile & password
    Api/AuthController.php         -- API login/logout
  Models/
    User.php                       -- HasRoles, HasApiTokens, notifications
    Company.php                    -- SoftDeletes
    App.php                        -- Platform app registry
    Invitation.php                 -- Token + expiry + permissions snapshot
    AuditLog.php                   -- Immutable action log

Modules/
  CurrencyExchange/                -- Live: BNR exchange rates, charts, converter
  {FutureModule}/                  -- Template for new business apps

database/migrations/
  create_companies_table
  create_invitations_table
  create_audit_logs_table
  create_apps_table
  create_company_user_table
  create_app_company_table
  create_app_user_company_table    -- Granular permissions per user/company/app
  add_permissions_to_invitations_table

database/seeders/
  AdminUserSeeder                  -- Default admin@demo.com / password
  RolesAndPermissionsSeeder        -- Default roles and permission definitions
  AppsSeeder                       -- Register platform apps in DB
```

---

## Current Implementation Status

| Feature | Status |
|---------|--------|
| Auth (login, logout, forgot/reset password) | ✅ Done |
| Invitation system (send, accept, expire) | ✅ Done |
| Company CRUD + soft deletes | ✅ Done |
| User management (activate/deactivate, delete) | ✅ Done |
| Role-based access (Spatie) | ✅ Done |
| Audit log | ✅ Done |
| In-app notifications + email | ✅ Done |
| CSV export (companies, users) | ✅ Done |
| Dashboard stats + charts | ✅ Done |
| Profile (edit name/email, change password) | ✅ Done |
| Apps registry + app_company + app_user_company | ✅ Schema done |
| Granular per-app permission UI | 🔄 In progress |
| CurrencyExchange Module | ✅ Done |
| Inter-app event bus | 📋 Planned |
| Shared data layer | 📋 Planned |
| BeautySalon / HR / Accounting modules | 📋 Planned |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.5 / Laravel 12 |
| Modules | nwidart/laravel-modules ^12 |
| Auth | Laravel Sanctum (API) + Session (Web) |
| Permissions | Spatie Laravel Permission ^6 |
| Database | MySQL |
| Frontend Core | Blade + Tailwind CSS CDN |
| Frontend Modules | Vue 3 + Vite |
| Charts | Chart.js |
| Email | Gmail SMTP |
| Dev Server | WAMP (Windows) |
| Repo | github.com/florentinmarianion/laravel-saas-demo |
