<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'media_id',
        'published',
    ];

    /**
     * Get the media associated with this news.
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
