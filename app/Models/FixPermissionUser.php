<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixPermissionUser extends Model
{
    protected $table = 'fix_permission_user';
    protected $fillable = ['permission_id', 'emp_id'];
}