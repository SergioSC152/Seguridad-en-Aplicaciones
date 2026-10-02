<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetOtp extends Model
{
    protected $fillable = ['user_id', 'code_hash', 'attempts', 'expires_at', 'expires_at_epoch', 'verified_at'];

    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'expires_at_epoch' => 'integer',
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return now()->timestamp >= ($this->expires_at_epoch ?? $this->expires_at->timestamp);
    }
}
