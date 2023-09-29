<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $table = 'department';
    protected $fillable = ['dept_id', 'dept_name', 'dept_name_en', 'level', 'dept_parent', 'user_manage', 'ip_address'];
}