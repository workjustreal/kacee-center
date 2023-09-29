<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leave';
    protected $fillable = ['leave_id', 'leave_start_date', 'leave_start_time', 'leave_end_date', 'leave_end_time', 'leave_reason', 'leave_attach', 'leave_day', 'leave_minute', 'leave_type_id', 'period_salary_id', 'leave_range', 'leave_mode', 'leader_id', 'emp_id', 'emp_type', 'approve_lid', 'approve_lip', 'approve_ldate', 'approve_mid', 'approve_mip', 'approve_mdate', 'approve_hrid', 'approve_hrip', 'approve_hrdate', 'leave_reason_hr', 'leave_status', 'leave_cancel_remark'];
}