<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordWorkingLog extends Model
{
    protected $table = 'record_working_log';
    protected $fillable = ['rw_id', 'description', 'emp_id', 'ip_address'];
}