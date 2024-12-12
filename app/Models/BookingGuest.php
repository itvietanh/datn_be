<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingGuest extends Model
{
    use HasFactory;
    protected $table = 'bookings_guest';
    protected $fillable = [
        'booking_id',
        'guest_id',
        'status'
    ];
}
