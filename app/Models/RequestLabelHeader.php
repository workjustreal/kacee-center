<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestLabelHeader extends Model
{
    protected $table = 'request_label_header';
    protected $fillable = ['request_id', 'label','label_color', 'label_detail', 'printer_id', 'printer_name', 'sort_sku', 'remark', 'userid', 'userip', 'status'];
}