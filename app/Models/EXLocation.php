<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EXLocation extends Model
{
    use HasFactory;
    protected $table = 'ex_location';
    protected $fillable = [
        'typcod', 'typdes'
    ];
}
