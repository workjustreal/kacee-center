<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDiscountRepair extends Model
{
    use HasFactory;
    protected $table = 'request_discount_repair';
    protected $fillable = [
        'id',
        'doc_id',
        'customer_code',
        'customer_name',
        'customer_status',
        'limit',
        'staf',
        'emp_id',
        'product_list',
        'invoice',
        'customer_request',
        'mistake',
        'note',
        'sec_approve',
        'sec_approve_date',
        'mn_approve',
        'mn_approve_date',
        'doc_status',
        'image',
        'description',
        'file',
        'created_at',
        'updated_at',
    ];
}
