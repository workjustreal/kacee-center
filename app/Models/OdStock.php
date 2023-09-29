<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OdStock extends Model
{
    use HasFactory;
    protected $table = 'od_stocks';
    protected $fillable = ['stkcod', 'loccod', 'stkdes'];
}
