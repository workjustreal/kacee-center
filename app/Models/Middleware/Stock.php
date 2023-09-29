<?php

namespace App\Models\Middleware;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $connection = 'mysql_mw';
    protected $table = 'stocks';
    protected $fillable = ['id', 'sku', 'name', 'storage', 'storage_des', 'qty', 'unit', 'unit_des', 'sale_category', 'lmov_date'];
}