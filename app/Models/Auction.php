<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    protected $fillable = ['user_id', 'livestock_batch_id', 'title', 'reserve_cents', 'status', 'winner_client_id', 'winning_cents'];
    protected function casts(): array { return ['reserve_cents' => 'integer', 'winning_cents' => 'integer']; }
    public function batch(): \Illuminate\Database\Eloquent\Relations\BelongsTo { return $this->belongsTo(LivestockBatch::class, 'livestock_batch_id'); }
public function bids(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(AuctionBid::class); }
public function winner(): \Illuminate\Database\Eloquent\Relations\BelongsTo { return $this->belongsTo(Client::class, 'winner_client_id'); }
}

