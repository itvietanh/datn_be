<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $table = 'room_type';

    protected $fillable = ['uuid', 'hotel_id', 'type_name', 'description', 'price_per_hour', 'price_per_day', 'price_overtime', 'vat', 'number_of_people', 'created_at', 'updated_at', 'created_by', 'updated_by'];

    public $timestamps = true;
}
