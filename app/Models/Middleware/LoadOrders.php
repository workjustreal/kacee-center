<?php

namespace App\Models\Middleware;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadOrders extends Model
{
    use HasFactory;
    protected $connection = 'mysql_mw';
    protected $table = 'load_orders';
    protected $fillable = ['ordernumber', 'order_create', 'sku', 'name', 'price', 'qty', 'shop', 'category', 'start', 'end'];
}