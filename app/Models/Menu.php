<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $fillable = ['id', 'uuid', 'code', 'description', 'icon', 'idx', 'is_show', 'name', 'parent_uid', 'url', 'hotel_id'];

    public $timestamps = false;

    public $incrementing = false;
    public $keyType = 'string';
}
