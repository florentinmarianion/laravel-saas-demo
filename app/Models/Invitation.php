<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    protected $fillable = [
        'company_id',
        'email',
        'token',
        'role',
        'permissions',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'accepted_at' => 'datetime',
        'permissions' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isPending(): bool
    {
        return is_null($this->accepted_at) && $this->expires_at->isFuture();
    }
}