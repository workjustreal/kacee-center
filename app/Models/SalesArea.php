<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesArea extends Model
{
    use HasFactory;
    protected $table = 'sales_area';
    protected $fillable = ['area_code', 'dept_id', 'area_description', 'user_manage', 'ip_address'];
}