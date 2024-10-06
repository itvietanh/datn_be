<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;
    protected $table = 'hotel';
    protected $fillable = ['uuid', 'name', 'address', 'star_rating', 'province_code', 'district_code', 'ward_code', 'created_at', 'updated_at', 'created_by', 'updated_by'];
    public $timestamps = true;
}
