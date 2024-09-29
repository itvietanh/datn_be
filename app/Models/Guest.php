<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $table = 'guest';

    protected $fillable = ['uuid', 'name', 'contact_details', 'id_number', 'passport_number', 'created_at', 'updated_at', 'created_by', 'updated_by'];

    public $timestamps = true;
}
