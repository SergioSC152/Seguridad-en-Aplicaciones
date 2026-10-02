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
        'hectares','carrying_capacity','budget','purpose','converted_client_id','converted_opportunity_id',
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
    public function score(): string
    {
        if ($this->hectares === null || $this->carrying_capacity === null || $this->budget === null || !$this->purpose) return 'Sin datos';
        $capacity=(float)$this->hectares*(float)$this->carrying_capacity;
        if ($capacity>=50 && (float)$this->budget>=50000000) return 'A1';
        return $capacity>=10 && (float)$this->budget>=10000000 ? 'B1' : 'B2';
    }
}
