<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixPermission extends Model
{
    protected $table = 'fix_permissions';
    protected $fillable = ['permission', 'description'];
}