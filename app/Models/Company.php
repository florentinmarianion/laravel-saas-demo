<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Legacy: direct users (keeping for backward compatibility)
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // New: users via pivot
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot('role', 'is_active')
            ->withTimestamps();
    }

    public function apps(): BelongsToMany
    {
        return $this->belongsToMany(App::class, 'app_company')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }
}