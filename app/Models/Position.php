<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;
    protected $table = 'position';
    protected $fillable = ['position_id', 'position_name', 'position_name_en', 'user_manage', 'ip_address'];
}