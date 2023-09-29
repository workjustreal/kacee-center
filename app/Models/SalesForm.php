<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesForm extends Model
{
    use HasFactory;
    protected $table = 'sales_form';
    protected $fillable = [
        'id',
        'gen_id',
        'customer_code',
        'customer_name',
        'invoice',
        'pay',
        'comment',
        'emp_id',
        'emp_dept_id',
        'approve_id',
        'approve_date',
        'submit_id',
        'submit_date',
        'status',
        'created_at',
        'updated_at',
    ];
}
