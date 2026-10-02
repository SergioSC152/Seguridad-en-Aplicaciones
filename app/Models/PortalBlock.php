<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalBlock extends Model
{
    protected $fillable = ['user_id', 'kind', 'title', 'body', 'media_id', 'link_url', 'position', 'published'];
    protected function casts(): array { return ['published' => 'boolean', 'position' => 'integer']; }
    public function media(): \Illuminate\Database\Eloquent\Relations\BelongsTo { return $this->belongsTo(Media::class); }
}

