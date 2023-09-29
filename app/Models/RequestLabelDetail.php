<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestLabelDetail extends Model
{
    protected $table = 'request_label_detail';
    protected $fillable = ['request_id', 'sku', 'barcode', 'description', 'qty'];
}