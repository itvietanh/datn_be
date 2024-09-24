<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employee';
    
    protected $fillable = ['uuid', 'name', 'email', 'phone', 'address', 'hotel_id', 'created_at', 'updated_at', 'created_by', 'updated_by'];
    public $timestamps = true;
}
