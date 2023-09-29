<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppPermission extends Model
{
    protected $table = 'app_permissions';
    protected $fillable = ['app_id', 'app_name', 'dept_id', 'emp_id'];
}