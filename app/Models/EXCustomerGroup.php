<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EXCustomerGroup extends Model
{
    protected $table = 'ex_customer_group';
    protected $fillable = ['name', 'display_name', 'description', 'level', 'parent_id', 'status', 'user_manage', 'ip_address'];
}