<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenerateBarcodeHeader extends Model
{
    protected $table = 'generate_barcode_header';
    protected $fillable = ['generate_id', 'remark', 'userid', 'userip'];
}