<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = ['user_id', 'client_id', 'livestock_batch_id', 'number', 'title', 'gross_kg', 'shrink_percent', 'price_per_kg', 'withholding_percent', 'commission_percent', 'other_deduction', 'subtotal_cents', 'total_cents', 'status', 'valid_until', 'terms', 'contract_token_hash', 'contract_expires_epoch', 'acceptance_otp_hash', 'acceptance_expires_epoch', 'acceptance_attempts', 'accepted_at', 'accepted_ip', 'accepted_document_hash'];
    protected function casts(): array { return ['valid_until' => 'date', 'accepted_at' => 'datetime', 'subtotal_cents' => 'integer', 'total_cents' => 'integer', 'contract_expires_epoch' => 'integer', 'acceptance_expires_epoch' => 'integer', 'acceptance_attempts' => 'integer']; }
    protected $hidden = ['contract_token_hash','acceptance_otp_hash'];
public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo { return $this->belongsTo(Client::class); }
public function batch(): \Illuminate\Database\Eloquent\Relations\BelongsTo { return $this->belongsTo(LivestockBatch::class, 'livestock_batch_id'); }
public function sale(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(Sale::class); }
}

