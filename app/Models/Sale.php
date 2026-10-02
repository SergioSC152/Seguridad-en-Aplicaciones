<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['user_id', 'quote_id', 'client_id', 'total_cents', 'paid_cents', 'sold_at', 'ica_guide', 'dispatched_at','guide_recorded_at'];
    protected function casts(): array { return ['total_cents' => 'integer', 'paid_cents' => 'integer', 'sold_at' => 'date', 'dispatched_at' => 'datetime']; }
    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo { return $this->belongsTo(Client::class); }
public function quote(): \Illuminate\Database\Eloquent\Relations\BelongsTo { return $this->belongsTo(Quote::class); }
}
