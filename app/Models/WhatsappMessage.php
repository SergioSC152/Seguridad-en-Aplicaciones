<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WhatsappMessage extends Model { protected $fillable=['user_id','lead_id','provider_id','direction','phone','body','status']; }
