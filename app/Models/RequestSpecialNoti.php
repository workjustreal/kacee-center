<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestSpecialNoti extends Model
{
    use HasFactory;
    protected $table = 'request_special_noti';
    protected $fillable = [
        'id',
        'personal_read',
        'manager_read',
        'secretary_action',
    ];
}
