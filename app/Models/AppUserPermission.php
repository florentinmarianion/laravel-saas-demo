<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppUserPermission extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'app_id',
        'permission_key',
        'granted',
    ];

    protected $casts = [
        'granted' => 'boolean',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForContext($query, int $userId, int $companyId, int $appId)
    {
        return $query->where('user_id', $userId)
                     ->where('company_id', $companyId)
                     ->where('app_id', $appId);
    }

    public function scopeGranted($query)
    {
        return $query->where('granted', true);
    }
}
