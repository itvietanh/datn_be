<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRole extends Model
{
    use HasFactory;

    protected $table = 'employee_role';

    protected $fillable = ['employee_id', 'role_id', 'created_at', 'updated_at', 'created_by', 'updated_by'];
    
    public $timestamps = true;
}
