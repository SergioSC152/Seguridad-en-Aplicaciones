<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LivestockMeasurement extends Model { protected $fillable=['user_id','livestock_batch_id','average_weight_kg','rfid','source']; }
