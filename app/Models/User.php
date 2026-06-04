<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active'         => 'boolean',
        ];
    }

    /**
    * All granular permissions of the user on a specific company+app.
    */
    public function appPermissions(): HasMany
    {
        return $this->hasMany(AppUserPermission::class);
    }

    /**
    * Checks if the user has a specific permission in an app+company.
    *
    * Admins bypass automatically via Gate::before in AppServiceProvider.
    *
    * Usage examples:
    * $user->hasAppPermission('currency-exchange', 'view', $companyId)
    * $user->hasAppPermission('accounting', 'invoices.approve', $companyId)
    */
    public function hasAppPermission(string $appSlug, string $permissionKey, int $companyId): bool
    {
        $app = \App\Models\App::where('slug', $appSlug)->first();

        if (! $app) {
            return false;
        }

        // First check that the user has access to the app in that company
        $hasAccess = $this->apps()
            ->wherePivot('company_id', $companyId)
            ->where('apps.id', $app->id)
            ->exists();

        if (! $hasAccess) {
            return false;
        }

        return AppUserPermission::where('user_id', $this->id)
            ->where('company_id', $companyId)
            ->where('app_id', $app->id)
            ->where('permission_key', $permissionKey)
            ->where('granted', true)
            ->exists();
    }

    /**
    * Returns all granted permissions of the user for an app+company.
    * Useful for displaying them in the UI or sending them to the frontend.
    */
    public function getAppPermissions(string $appSlug, int $companyId): array
    {
        $app = \App\Models\App::where('slug', $appSlug)->first();

        if (! $app) {
            return [];
        }

        return AppUserPermission::where('user_id', $this->id)
            ->where('company_id', $companyId)
            ->where('app_id', $app->id)
            ->where('granted', true)
            ->pluck('permission_key')
            ->toArray();
    }

    /**
    * Syncs a user's permissions for an app+company.
    * Used from AppPermissionController or when accepting an invitation.
    *
    * $permissions = ['view', 'create', 'invoices.approve']
    */
    public function syncAppPermissions(int $appId, int $companyId, array $permissions): void
    {
        // Delete all existing permissions for this context
        AppUserPermission::where('user_id', $this->id)
            ->where('company_id', $companyId)
            ->where('app_id', $appId)
            ->delete();

        // Adaugă cele noi
        foreach ($permissions as $permissionKey) {
            AppUserPermission::create([
                'user_id'        => $this->id,
                'company_id'     => $companyId,
                'app_id'         => $appId,
                'permission_key' => $permissionKey,
                'granted'        => true,
            ]);
        }
    }


    // Legacy: single company (backward compatibility)
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // New: multiple companies via pivot
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot('role', 'is_active')
            ->withTimestamps();
    }

    // Apps accessible to this user
    public function apps(): BelongsToMany
    {
        return $this->belongsToMany(App::class, 'app_user_company')
            ->withPivot('company_id')
            ->withTimestamps();
    }

    // Check if user has access to an app in a specific company
    public function hasAppAccess(App $app, Company $company): bool
    {
        return $this->apps()
            ->wherePivot('company_id', $company->id)
            ->where('apps.id', $app->id)
            ->exists();
    }
}
