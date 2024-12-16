<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentMethod extends Model
{

    protected $primaryKey = 'id';
    public $incrementing = true;


    protected $table = 'payment_method';

    protected static function booted()
    {
        static::creating(function ($paymentMethod) {
            if (!$paymentMethod->uuid) {
                $paymentMethod->uuid = (string) Str::uuid();
            }
        });
    }

    protected $fillable = ['name', 'pr_code', 'description', 'uuid'];
}
