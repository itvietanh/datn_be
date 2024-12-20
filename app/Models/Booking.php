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
        'mo_ta',
        'guest_count',
        'check_in',
        'check_out',
        'created_at',
        'updated_at',
        'status',
        'representative_id',
        'group_name',
        'room_quantity',
        'order_date',
        'total_amount',
        'contract_type',
        'data_guest_id'
    ];
}
