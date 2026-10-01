<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    public const TYPES = ['individual', 'business'];

    public const STATUSES = ['active', 'inactive'];

    protected $fillable = [
        'name',
        'client_type',
        'contact_person',
        'document_number',
        'email',
        'phone',
        'municipality',
        'department',
        'address',
        'status',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salesOpportunities(): HasMany
    {
        return $this->hasMany(SalesOpportunity::class);
    }
}
