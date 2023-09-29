<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingHistory extends Model
{
    use HasFactory;
    protected $table = 'shipping_history';
    protected $fillable = ['trackingnumber', 'delivery_date', 'ordernumber', 'order_date', 'so', 'packages', 'platform_id', 'userid', 'userip'];
}