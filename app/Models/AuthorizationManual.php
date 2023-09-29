<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorizationManual extends Model
{
    protected $table = 'authorization_manual';
    protected $fillable = ['emp_id', 'auth', 'auth2'];
}