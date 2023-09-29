<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRecordHeader extends Model
{
    protected $table = 'leave_record_header';
    protected $fillable = ['lr_id', 'dept_id', 'year', 'month', 'create_id', 'create_ip'];
}