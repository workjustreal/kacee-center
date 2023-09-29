<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenerateBarcodeDetail extends Model
{
    protected $table = 'generate_barcode_detail';
    protected $fillable = ['generate_id', 'sku', 'barcode', 'description', 'runningcode', 'status', 'userid', 'userip'];
}