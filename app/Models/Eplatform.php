<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eplatform extends Model
{
    use HasFactory;
    protected $table = 'eplatform';
    protected $fillable = ['name', 'app_key', 'app_secret', 'api_url', 'status'];
}