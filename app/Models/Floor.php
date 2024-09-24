<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    protected $table = 'floor';

    protected $fillable = ['uuid', 'hotel_id', 'floor_number', 'created_at', 'updated_at', 'created_by', 'updated_by'];

    public $timestamps = true;
}
