<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDecorateLog extends Model
{
    use HasFactory;
    protected $table = 'request_decorate_log';
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
