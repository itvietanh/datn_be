<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'role';

    protected $fillable = ['uuid', 'role_name', 'description','created_at', 'updated_at', 'created_by', 'updated_by'];

    public $timestamps = true;
}
