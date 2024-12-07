<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $table = 'guest';

    protected $fillable = [
        'uuid',
        'name',
        'gender',
        'birth_date',
        'contact_details',
        'id_number',
        'passport_number',
        'representative',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'province_id',
        'district_id',
        'ward_id',
        'phone_number',
        'nat_id'
    ];

    public $timestamps = true;

    /**
     * Bảng Provinces (Tỉnh)
     */
    public function province()
    {
        return $this->belongsTo(Provinces::class, 'province_id', 'code');
    }

    /**
     * Bảng Districts (Quận/Huyện)
     */
    public function district()
    {
        return $this->belongsTo(Districts::class, 'district_id', 'code');
    }

    /**
     * Bảng Wards (Phường/Xã)
     */
    public function ward()
    {
        return $this->belongsTo(Wards::class, 'ward_id', 'code');
    }
}
