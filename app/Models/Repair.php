<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    use HasFactory;
    protected $table = 'repair';
    protected $fillable = [
            'user_id',
            'department_id',
            'order_dept',
            'order_type',
            'car_id',
            'car_mile',
            'order_address',
            'order_tool',
            'order_detail',
            'order_date',
            'order_image',
            'approve_name',
            'approve_date',
            'manager_id',
            'manager_date',
            'manager_detail',
            'technician_name',
            'technician_detail',
            'start_date',
            'end_date',
            'price',
            'approve_detail',
            'user_comment',
            'status'
    ];
}
