<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomUsingGuest extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'room_using_guest';
    protected $fillable = ['uuid', 'guest_id', 'room_using_id', 'check_in', 'check_out', 'created_at', 'updated_at', 'created_by', 'updated_by'];

    public $timestamps = true;

    public function roomUsing()
    {
        return $this->belongsTo(RoomUsing::class, 'room_using_id');
    }
}
