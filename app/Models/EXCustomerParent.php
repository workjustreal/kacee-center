<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EXCustomerParent extends Model
{
    protected $table = 'ex_customer_parent';
    protected $fillable = ['cuscod', 'group_id', 'user_manage', 'ip_address'];
}