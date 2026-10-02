<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOpportunity extends Model
{
    public const STAGES = [
        'contact' => 'Lead entrante',
        'visit' => 'Visita y pesaje',
        'negotiation' => 'Pre-puja / negociación',
        'billing' => 'Facturación y guía ICA',
        'won' => 'Despacho y ganada',
        'lost' => 'Perdida',
    ];

    public const OPEN_STAGES = ['contact', 'visit', 'negotiation', 'billing'];

    protected $fillable = [
        'client_id',
        'title',
        'livestock_summary',
        'head_count',
        'estimated_value',
        'stage',
        'expected_close_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'head_count' => 'integer',
            'estimated_value' => 'decimal:2',
            'expected_close_date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
