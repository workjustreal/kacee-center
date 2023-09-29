<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordWorking extends Model
{
    protected $table = 'record_working';
    protected $fillable = ['emp_id', 'work_date', 'use_status', 'leave_id', 'remark', 'leave_mode', 'leader_id', 'approve_status', 'approve_lid', 'approve_lip', 'approve_ldate', 'approve_mid', 'approve_mip', 'approve_mdate', 'approve_hrid', 'approve_hrip', 'approve_hrdate', 'cancel_remark'];
}