<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FingerprintDevice extends Model
{
    use HasFactory;
    protected $table = 'fingerprint_devices';
    protected $fillable = ['fpdevice_id', 'fpbranch_id', 'fpdevice_name'];
}