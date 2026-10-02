<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    protected $fillable = ['user_id', 'event', 'severity', 'ip', 'route', 'http_status'];
    protected function casts(): array { return []; }
    
}

