<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySale extends Model
{
    use HasFactory;
    protected $table = 'daily_sales';
    protected $fillable = ['doc_date', 'doc_num', 'cuscod', 'stkcod', 'barcod', 'stkdes', 'daily_category', 'unit', 'qty', 'unitpr', 'netval', 'year', 'month', 'day', 'header'];
}