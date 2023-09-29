<?php

namespace App\Models\Middleware;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $connection = 'mysql_mw';
    protected $table = 'orders';
    protected $fillable = [
        'order_number',
        'order_id',
        'create_time',
        'shop_id',
        'so_number',
        'total_amount',
        'total_quantity',
        'total_shipping_fee',
        'total_discount',
        'items_count',
        'package_count',
        'payment_method',
        'customer_name',
        'billing_address',
        'shipping_address',
        'tax_invoice_requested',
        'tax_invoice_info',
        'remarks',
        'order_status',
        'status',
        'created_by',
        'updated_by',
    ];

    // Cast column to array
    protected $casts = [
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'tax_invoice_info' => 'array',
    ];
}