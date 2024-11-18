<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategories extends Model
{
    use HasFactory;

    protected $table = 'service_categories';

    protected $fillable = ['uuid', 'name', 'price', 'service_id', 'created_at', 'updated_at'];

    public $timestamps = true;
}
