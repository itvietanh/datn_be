<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class RoomUsing extends Model
// {
//     use HasFactory;

//     protected $table = 'room_using';

//     protected $fillable = ['uuid', 'trans_id', 'room_id', 'check_in', 'check_out', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'];

//     public $timestamps = true;

//     public function transition()
//     {
//         return $this->belongsTo(Transition::class, 'trans_id');
//     }

//     public function guests()
//     {
//         return $this->hasMany(RoomUsingGuest::class, 'room_using_id');
//     }
// }



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class RoomUsing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'room_using';

    protected $fillable = [
        'uuid',
        'trans_id',
        'room_id',
        'check_in',
        'check_out',
        'is_deleted',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'room_change_fee',
        'total_amout',
        'prepaid',
        'booking_id',
        'room_type_id'
    ];

    public $timestamps = true;

    protected $dates = ['deleted_at'];

    // Scope lấy danh sách phòng quá hạn
    public function scopeOverdue($query)
    {
        return $query->where('check_out', '<', Carbon::now())
            ->where('is_deleted', false);
    }

    // Quan hệ với Transition
    public function transition()
    {
        return $this->belongsTo(Transition::class, 'trans_id');
    }

    // Quan hệ với RoomUsingGuest
    public function guests()
    {
        return $this->hasMany(RoomUsingGuest::class, 'room_using_id');
    }
}
