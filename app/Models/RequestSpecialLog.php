<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestSpecialLog extends Model
{
    use HasFactory;
    protected $table = 'request_special_log';
    protected $fillable = [
        'id',
        'doc_id',
        'emp_id',
        'description',
        'comment',
        'created_at',
        'updated_at',
    ];
}
