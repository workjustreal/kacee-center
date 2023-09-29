<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveLog extends Model
{
    protected $table = 'leave_log';
    protected $fillable = ['leave_id', 'description', 'emp_id', 'ip_address'];
}