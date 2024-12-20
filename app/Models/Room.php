<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'room';

    protected $fillable = ['uuid', 'hotel_id', 'floor_id', 'room_type_id', 'room_number', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'];

    public $timestamps = true;

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'floor_id', 'id');
    }
}
