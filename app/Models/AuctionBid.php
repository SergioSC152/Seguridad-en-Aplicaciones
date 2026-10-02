<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionBid extends Model
{
    protected $fillable = ['auction_id', 'client_id', 'amount_cents'];
    protected function casts(): array { return ['amount_cents' => 'integer']; }
    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo { return $this->belongsTo(Client::class); }
}

