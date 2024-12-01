<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $table = 'guest';

    protected $fillable = ['uuid', 'name', 'gender', 'birth_date', 'contact_details', 'id_number', 'passport_number', 'representative', 'created_at', 'updated_at', 'created_by', 'updated_by', 'province_id', 'district_id', 'ward_id', 'phone_number', 'nat_id'];

    public $timestamps = true;
}
