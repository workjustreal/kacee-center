<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manual extends Model
{
    use HasFactory; 
    protected $table = 'manual';
    protected $fillable = [
        'manual_id',
        'manual_name',
        'manual_file',
        'created_at',
        'updated_at',
    ];
}
