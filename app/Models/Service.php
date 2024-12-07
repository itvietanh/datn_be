<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'service';

    protected $fillable = ['uuid', 'service_name', 'hotel_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'service_categories_id', 'price', 'status'];

    public $timestamps = true;
}
