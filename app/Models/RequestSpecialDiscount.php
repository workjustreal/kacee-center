<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestSpecialDiscount extends Model
{
    use HasFactory;
    protected $table = 'request_special_discount';
    protected $fillable = [
        'id',
        'doc_id',
        'customer_code',
        'customer_name',
        'customer_status',
        'limit',
        'employee',
        'emp_id',
        'sec_approve',
        'sec_approve_date',
        'mn_approve',
        'mn_approve_date',
        'doc_status',
        'product_detail',
        'image',
        'file',
        'image_mn',
        'file_mn',
        'created_at',
        'updated_at',
    ];
}
