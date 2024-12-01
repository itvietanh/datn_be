<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DmQuocTich extends Model
{
    use HasFactory;
    protected $table = 'dm_quoc_tich';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'ma',
        'ten',
        'mo_ta',
        'hieu_luc',
        'do_uu_tien',
        'gia_tri',
        'nguoi_tao',
        'ngay_tao',
        'ngay_sua_cuoi',
        'nguoi_sua_cuoi',
        'ten_tieng_anh',
        'ten_viet_tat',
        'ma_quoc_te',
        'ma_quoc_te_int',
    ];

    protected $casts = [
        'ngay_tao' => 'datetime',
        'ngay_sua_cuoi' => 'datetime',
        'do_uu_tien' => 'integer',
    ];
}
