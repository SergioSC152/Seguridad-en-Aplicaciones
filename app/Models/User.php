<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function livestockCategories(): HasMany
    {
        return $this->hasMany(LivestockCategory::class);
    }

    public function livestockBatches(): HasMany
    {
        return $this->hasMany(LivestockBatch::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function salesOpportunities(): HasMany
    {
        return $this->hasMany(SalesOpportunity::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function isPlatformAdmin(): bool
    {
        $adminEmail = config('cowapp.mail_settings_admin_email');

        return is_string($adminEmail)
            && $adminEmail !== ''
            && hash_equals(mb_strtolower($adminEmail), mb_strtolower($this->email));
    }

    public function hasPermissionTo(string $permission): bool
    {
        if ($this->isPlatformAdmin()) {
            return true;
        }

        return $this->role()
            ->whereHas('permissions', fn ($query) => $query->where('code', $permission))
            ->exists();
    }
}
