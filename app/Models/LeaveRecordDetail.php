<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRecordDetail extends Model
{
    protected $table = 'leave_record_detail';
    protected $fillable = ['lr_id', 'emp_id', 'sat', 'rw_id', 'leave_id'];
}