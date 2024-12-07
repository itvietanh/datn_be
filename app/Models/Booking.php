<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $table = 'bookings';
    protected $fillable = [
        'id',
        'room_type_id',
        'mo_ta',
        'guest_count',
        'check_in',
        'check_out',
        'created_at',
        'updated_at',
        'status',
        'representative_id',
        'room_using_id'
    ];
}
