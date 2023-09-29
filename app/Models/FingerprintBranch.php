<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FingerprintBranch extends Model
{
    use HasFactory;
    protected $table = 'fingerprint_branch';
    protected $fillable = ['fpbranch_id', 'fpbranch_name'];
}