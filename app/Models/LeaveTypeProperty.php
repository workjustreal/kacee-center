<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveTypeProperty extends Model
{
    protected $table = 'leave_type_property';
    protected $fillable = ['leave_type_ppt_id', 'leave_type_ppt_name', 'leave_type_ppt_day', 'leave_type_monthly', 'leave_type_daily', 'leave_type_ppt_status'];
}