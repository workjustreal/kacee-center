<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'leave_type';
    protected $fillable = ['leave_type_id', 'leave_type_name', 'leave_type_detail', 'leave_type_note', 'leave_type_monthly', 'leave_type_daily', 'leave_type_status'];
}