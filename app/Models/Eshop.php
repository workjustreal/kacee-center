<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eshop extends Model
{
    use HasFactory;
    protected $table = 'eshop';
    protected $fillable = ['name', 'seller_id', 'platform_id', 'platform_name', 'api_version', 'status'];
}