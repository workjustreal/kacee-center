<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NocnocApi extends Model
{
    use HasFactory;
    protected $table = 'nocnoc_api';
    protected $fillable = ['code', 'access_token', 'refresh_token', 'expires_in', 'refresh_expires_in', 'country', 'account', 'account_platform', 'user_id', 'seller_id', 'short_code', 'client_id'];
}