<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientDocument extends Model
{
    protected $fillable = ['user_id', 'client_id', 'kind', 'reference', 'expires_at', 'path', 'mime_type', 'review_status', 'review_notes'];
    protected function casts(): array { return ['expires_at' => 'date']; }
    protected $hidden = ['path'];
public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo { return $this->belongsTo(Client::class); }
}

