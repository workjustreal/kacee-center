<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Automotive extends Model
{
    use HasFactory;
    protected $table = 'car_main';
    protected $fillable = [
        'id',
        'car_id',
        'brand',
        'type',
        'model',
        'color',
        'dept_id',
        'comment',
        'status',
    ];
}
