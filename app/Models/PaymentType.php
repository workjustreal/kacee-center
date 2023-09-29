<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasFactory;
    protected $table = 'payment_type';
    protected $fillable = ['payment_type', 'payment_desc', 'user_manage', 'ip_address'];
}