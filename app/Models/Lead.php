<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    public const SOURCES = [
        'manual' => 'Registro manual',
        'whatsapp' => 'WhatsApp',
        'website' => 'Sitio web',
        'referral' => 'Referido',
        'fair' => 'Feria / remate',
        'other' => 'Otro',
    ];

    public const STATUSES = [
        'new' => 'Nuevo',
        'contacted' => 'Contactado',
        'qualified' => 'Calificado',
        'disqualified' => 'No calificado',
    ];

    protected $fillable = [
        'name',
        'farm_name',
        'phone',
        'email',
        'municipality',
        'department',
        'source',
        'status',
        'livestock_interest',
        'estimated_heads',
        'follow_up_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'estimated_heads' => 'integer',
            'follow_up_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
