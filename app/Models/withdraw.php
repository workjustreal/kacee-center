<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class withdraw extends Model
{
    use HasFactory;
    protected $table = 'withdraw';
    protected $fillable = [
        'withdraw_id',
        'repair_order_id',
        'products_name',
        'qty',
        'prices',
        'total_prices',
        'comment',
        'withdraw_date',
        'dept_id',
        'emp_id',
        'approve_id',
        'created_at',
        'updated_at',
        'status',
        'status_inventory'
    ];
}
