<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    use HasFactory;
    protected $table = 'printers';
    protected $fillable = ['id', 'name', 'description', 'type', 'client_ip', 'role', 'user_permission', 'status'];
}