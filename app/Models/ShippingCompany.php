<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCompany extends Model
{
    protected $table = 'shipping_company';
    protected $fillable = ['check', 'name', 'status'];
}