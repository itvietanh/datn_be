<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $table = 'room_type';

    protected $fillable = ['uuid', 'hotel_id', 'type_name', 'type_price', 'description','created_at', 'updated_at', 'created_by', 'updated_by'];

    public $timestamps = true;
}
