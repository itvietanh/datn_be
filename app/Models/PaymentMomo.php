<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMomo extends Model
{
    protected $table = 'payment_momo';

    protected $fillable = [
        'partner_code',
        'order_id',
        'amount',
        'order_info',
        'order_type',
        'trans_id',
        'pay_type',
    ];

    public $timestamps = true;
}
