<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGroupReport extends Model
{
    use HasFactory;
    protected $table = 'product_group_report';
    protected $fillable = [
        'id', 'stkcod', 'sale_category', 'main_category', 'sec_category', 'online_category', 'daily_category', 'model', 'color_code', 'size', 'remark', 'userid', 'userip'
    ];
}