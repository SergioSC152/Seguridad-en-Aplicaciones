<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivestockBatch extends Model
{
    use HasFactory;

    public const STATUSES = ['active', 'sold', 'inactive'];

    protected $fillable = [
        'livestock_category_id',
        'code',
        'ear_tag',
        'head_count',
        'average_weight_kg',
        'farm_name',
        'paddock',
        'status',
        'notes',
        'image_path',
        'image_url',
        'purpose','availability','published','price_per_kg','rfid',
    ];

    protected function casts(): array
    {
        return [
            'head_count' => 'integer',
            'average_weight_kg' => 'decimal:2',
            'published'=>'boolean','price_per_kg'=>'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LivestockCategory::class, 'livestock_category_id');
    }
}
