<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Employee extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'employee';

    protected $fillable = ['uuid', 'name', 'email',  'password', 'phone', 'address', 'status', 'hotel_id', 'created_at', 'updated_at', 'created_by', 'updated_by'];
    public $timestamps = true;
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
