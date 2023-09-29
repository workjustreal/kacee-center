<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'ex_product';
    protected $fillable = ['No', 'stkcod', 'barcod', 'stktyp', 'stkgrp', 'names', 'images', 'stkdes', 'stkdes2', 'series', 'product_type', 'detail', 'acccod', 'qucod', 'unitpr', 'sellpr1', 'userid', 'status'];
}