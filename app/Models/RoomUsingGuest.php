<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomUsingGuest extends Model
{
    use HasFactory;
    protected $table = 'room_using_guest';
    protected $fillable = ['uuid', 'guest_id', 'room_using_id', 'check_in','check_out', 'created_at','updated_at', 'created_by', 'updated_by'];
    public $timestamps = true;
}
