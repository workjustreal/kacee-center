<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OdLocation extends Model
{
    use HasFactory;
    protected $table = 'od_location';
    protected $fillable = ['code', 'name', 'group'];
}
