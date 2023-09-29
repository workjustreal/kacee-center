<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDiscountNoti extends Model
{
    use HasFactory;
    protected $table = 'request_discount_noti';
    protected $fillable = [
        'doc_id',
        'personal_read',
        'manager_read',
        'secretary_action',
    ];
}
