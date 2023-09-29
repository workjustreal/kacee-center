<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingHistoryLog extends Model
{
    use HasFactory;
    protected $table = 'shipping_history_log';
    protected $fillable = ['trackingnumber', 'ordernumber', 'so', 'userid', 'userip'];
}