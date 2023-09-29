<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EXCustomer extends Model
{
    use HasFactory;
    protected $table = 'ex_customer';
    protected $fillable = [
        'No', 'cuscod', 'custyp', 'prenam', 'cusnam', 'addr01', 'addr02', 'zipcod', 'telnum', 'slmcod', 'areacod', 'paytrm', 'paycond', 'crline'
    ];
}