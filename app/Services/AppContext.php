<?php

namespace App\Services;

use App\Models\App;
use App\Models\AppUserPermission;
use App\Models\Company;
use App\Models\User;

/**
 * Central context helper for the active session.
 *
 * Usage anywhere (controller, blade, middleware):
 *   AppContext::user()
 *   AppContext::company()
 *   AppContext::hasAppAccess('currency-exchange')
 *   AppContext::hasAppPermission('accounting', 'invoices.approve')
 *
 * In Blade:
 *   @if(AppContext::isAdmin())
 *   {{ AppContext::company()->name }}
 */
class AppContext
{
    // ─── User ──────────────────────────────────────────────────────────────────

    public static function user(): ?User
    {
        return auth()->user();
    }

    public static function userId(): ?int
    {
        return auth()->id();
    }

    public static function isAdmin(): bool
    {
        return static::user()?->hasRole('admin') ?? false;
    }

    public static function isGuest(): bool
    {
        return ! auth()->check();
    }

    // ─── Active Company ────────────────────────────────────────────────────────

    public static function companyId(): ?int
    {
        return session('active_company_id')
            ? (int) session('active_company_id')
            : null;
    }

    public static function company(): ?Company
    {
        $id = static::companyId();
        return $id ? Company::find($id) : null;
    }

    public static function companyName(): string
    {
        return session('active_company_name') ?? 'No Company';
    }

    public static function hasCompany(): bool
    {
        return static::companyId() !== null;
    }

    /**
     * All companies the current user belongs to.
     * Useful for the company switcher dropdown.
     */
    public static function userCompanies()
    {
        return static::user()
            ?->companies()
            ->where('companies.is_active', true)
            ->withPivot('role', 'is_active')
            ->get()
            ?? collect();
    }

    /**
     * Set active company in session.
     */
    public static function setCompany(int $companyId, ?string $name = null): void
    {
        $name = $name ?? Company::find($companyId)?->name;

        session([
            'active_company_id'   => $companyId,
            'active_company_name' => $name,
        ]);
    }

    public static function clearCompany(): void
    {
        session()->forget(['active_company_id', 'active_company_name']);
    }

    // ─── App Access ────────────────────────────────────────────────────────────

    /**
     * Check if current user has access to an app in the active company.
     *
     * Admins always return true.
     *
     * @param string $appSlug e.g. 'currency-exchange', 'accounting', 'hr'
     */
    public static function hasAppAccess(string $appSlug): bool
    {
        if (static::isAdmin()) {
            return true;
        }

        $user      = static::user();
        $companyId = static::companyId();

        if (! $user || ! $companyId) {
            return false;
        }

        return $user->apps()
            ->wherePivot('company_id', $companyId)
            ->where('apps.slug', $appSlug)
            ->where('apps.is_active', true)
            ->exists();
    }

    /**
     * Get all app slugs accessible to the current user in the active company.
     * Useful for rendering nav items.
     */
    public static function accessibleApps()
    {
        if (static::isAdmin()) {
            return App::where('is_active', true)->orderBy('name')->get();
        }

        $user      = static::user();
        $companyId = static::companyId();

        if (! $user || ! $companyId) {
            return collect();
        }

        return $user->apps()
            ->wherePivot('company_id', $companyId)
            ->where('apps.is_active', true)
            ->orderBy('name')
            ->get();
    }

    // ─── Granular App Permissions ──────────────────────────────────────────────

    /**
     * Check if current user has a specific permission within an app.
     *
     * Admins always return true.
     *
     * @param string $appSlug      e.g. 'accounting'
     * @param string $permission   e.g. 'invoices.approve', 'view', 'delete'
     */
    public static function hasAppPermission(string $appSlug, string $permission): bool
    {
        if (static::isAdmin()) {
            return true;
        }

        $user      = static::user();
        $companyId = static::companyId();

        if (! $user || ! $companyId) {
            return false;
        }

        $app = App::where('slug', $appSlug)->first();

        if (! $app) {
            return false;
        }

        return AppUserPermission::where('user_id', $user->id)
            ->where('company_id', $companyId)
            ->where('app_id', $app->id)
            ->where('permission_key', $permission)
            ->where('granted', true)
            ->exists();
    }

    /**
     * Get all granted permission keys for a specific app in active company.
     *
     * @return array e.g. ['view', 'create', 'invoices.approve']
     */
    public static function appPermissions(string $appSlug): array
    {
        $user      = static::user();
        $companyId = static::companyId();

        if (! $user || ! $companyId) {
            return [];
        }

        if (static::isAdmin()) {
            return ['*'];
        }

        $app = App::where('slug', $appSlug)->first();

        if (! $app) {
            return [];
        }

        return AppUserPermission::where('user_id', $user->id)
            ->where('company_id', $companyId)
            ->where('app_id', $app->id)
            ->where('granted', true)
            ->pluck('permission_key')
            ->toArray();
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Current user's role in the active company (from company_user pivot).
     * Different from Spatie role — this is the company-specific role.
     */
    public static function companyRole(): ?string
    {
        $user      = static::user();
        $companyId = static::companyId();

        if (! $user || ! $companyId) {
            return null;
        }

        return $user->companies()
            ->where('companies.id', $companyId)
            ->first()
            ?->pivot
            ?->role;
    }
}
