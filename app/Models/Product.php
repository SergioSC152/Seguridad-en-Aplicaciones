<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['user_id', 'name', 'kind', 'purpose', 'price', 'description', 'media_id', 'published', 'active'];
    protected function casts(): array { return ['price' => 'decimal:2', 'published' => 'boolean', 'active' => 'boolean']; }
    public function media(): \Illuminate\Database\Eloquent\Relations\BelongsTo { return $this->belongsTo(Media::class); }
}

