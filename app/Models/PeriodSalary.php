<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodSalary extends Model
{
    protected $table = 'period_salary';
    protected $fillable = ['year', 'month', 'start', 'end', 'last', 'remark'];
}