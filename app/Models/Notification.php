<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notification';
    protected $fillable = ['app_id', 'app_name', 'title', 'description', 'url', 'job_id', 'from_uid', 'from_uname', 'to_uid', 'to_uname', 'type', 'status'];
}