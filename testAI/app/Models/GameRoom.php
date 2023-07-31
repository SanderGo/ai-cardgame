<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class GameRoom extends Model
{
    protected $fillable = ['room_code', 'room_status'];
}
