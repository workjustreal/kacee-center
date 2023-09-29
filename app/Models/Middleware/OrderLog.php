<?php

namespace App\Models\Middleware;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    use HasFactory;
    protected $connection = 'mysql_mw';
    protected $table = 'order_logs';
    protected $fillable = [
        'order_id',
        'title',
        'description',
        'user_by',
        'ip_address',
    ];
}