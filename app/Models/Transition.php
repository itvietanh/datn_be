<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transition extends Model
{
    use HasFactory;

    protected $table = 'transition';

    protected $fillable = ['uuid', 'guest_id', 'transition_date', 'note', 'payment_status', 'created_at', 'updated_at', 'created_by', 'updated_by'];

    public $timestamps = true;

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }
    public function roomUsings()
    {
        return $this->hasMany(RoomUsing::class, 'trans_id');
    }
}
