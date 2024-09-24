<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transition extends Model
{
    use HasFactory;

    protected $table = 'transtion';

    protected $fillable = ['uuid', 'guest_id', 'transition_date', 'payment_status', 'created_at', 'updated_at', 'created_by', 'updated_by'];

    public $timestamps = true;
}
