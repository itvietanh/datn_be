<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    use HasFactory;
    protected $table = 'bookings_details';
    protected $fillable = [
        'id',
        'room_type_id',
        'booking_id',
        'quantity',
        'created_at',
        'updated_at'
    ];
}
