<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['user_id', 'client_id', 'sales_opportunity_id', 'title', 'due_at', 'status', 'notes'];
    protected function casts(): array { return ['due_at' => 'datetime']; }
    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo { return $this->belongsTo(Client::class); }
}

