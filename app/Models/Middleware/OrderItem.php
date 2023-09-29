<?php

namespace App\Models\Middleware;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $connection = 'mysql_mw';
    protected $table = 'order_items';
    protected $fillable = [
        'order_id',
        'item',
        'sku',
        'name',
        'variation',
        'original_price',
        'sale_price',
        'shipping_fee',
        'discount',
        'quantity',
        'package_number',
        'tracking_number',
        'status',
    ];
}