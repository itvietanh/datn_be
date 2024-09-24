<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomUsing extends Model
{
    use HasFactory;

    protected $table = 'room_using';

    protected $fillable = ['uuid', 'trans_id', 'room_id', 'check_in', 'check_out', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'];

    public $timestamps = true;
}
