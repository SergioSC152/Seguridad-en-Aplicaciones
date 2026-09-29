<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Media extends Model
{
    protected $fillable = [
        'name',
        'path',
        'mime_type',
        'size',
        'active',
    ];

    /**
     * Get the news associated with this media item.
     */
    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }
}
