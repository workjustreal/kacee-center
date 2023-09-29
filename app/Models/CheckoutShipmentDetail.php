<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutShipmentDetail extends Model
{
    use HasFactory;
    protected $table = 'checkout_shipment_detail';
    protected $fillable = ['running', 'trackingnumber', 'ordernumber', 'so', 'packaging_qty', 'platform_id'];
}