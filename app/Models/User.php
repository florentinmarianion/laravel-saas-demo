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