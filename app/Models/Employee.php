<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $table = 'employee';
    protected $fillable = [
        'emp_id',
        'branch_id',
        'position_id',
        'dept_id',
        'area_code',
        'emp_type',
        'emp_status',
        'title',
        'title_en',
        'name',
        'name_en',
        'surname',
        'surname_en',
        'nickname',
        'gender',
        'birth_date',
        'tel',
        'tel2',
        'phone',
        'phone2',
        'email',
        'detail',
        'image',
        'photo',
        'personal_id',
        'address',
        'subdistrict',
        'district',
        'province',
        'country',
        'zipcode',
        'current_address',
        'current_subdistrict',
        'current_district',
        'current_province',
        'current_country',
        'current_zipcode',
        'start_work_date',
        'end_work_date',
        'ethnicity',
        'nationality',
        'religion',
        'vehicle_registration',
        'user_manage',
        'ip_address'
    ];
}