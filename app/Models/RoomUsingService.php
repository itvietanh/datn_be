<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomUsingService extends Model
{
    use HasFactory;

    protected $table = 'room_using_service';

    protected $fillable = ['uuid', 'room_using_id', 'service_id', 'service_using_date','created_at', 'updated_at', 'created_by', 'updated_by'];

    public $timestamps = true;

    public function roomUsing()
    {
        return $this->belongsTo(RoomUsing::class, 'room_using_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function getServicePrice()
    {
        return $this->service()->first()->service_price;
    }
}
