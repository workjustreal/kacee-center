<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutShipmentHeader extends Model
{
    use HasFactory;
    protected $table = 'checkout_shipment_header';
    protected $fillable = ['running', 'vehicle_registration', 'checkout_date', 'ship_com', 'signature', 'remark', 'userid', 'userip'];
}