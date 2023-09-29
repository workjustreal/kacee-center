<?php

namespace App\Models\Middleware;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOnline extends Model
{
    use HasFactory;
    protected $connection = 'mysql_mw';
    protected $table = 'product_online';
    protected $fillable = ['sku', 'name', 'description', 'category', 'remark', 'created_by', 'updated_by'];
}