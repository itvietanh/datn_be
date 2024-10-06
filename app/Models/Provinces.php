<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinces extends Model
{
    use HasFactory;
    protected $table = 'provinces';
    protected $fillable = ['code', 'name', 'name_en', 'full_name', 'full_name_en', 'code_name'];
}
