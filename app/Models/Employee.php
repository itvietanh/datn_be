<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Employee extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'employee';
    
    protected $fillable = ['uuid', 'name', 'email',  'password', 'phone', 'address', 'hotel_id', 'created_at', 'updated_at', 'created_by', 'updated_by'];
    public $timestamps = true;
    protected $hidden = [
        'password', 'remember_token',
    ];
}
