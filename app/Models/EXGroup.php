<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EXGroup extends Model
{
    use HasFactory;
    protected $table = 'ex_group';
    protected $fillable = [
        'No', 'typcod', 'typdes'
    ];
}