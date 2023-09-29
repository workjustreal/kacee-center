@extends('layouts.master-layout', ['page_title' => "เพิ่มข้อมูลพนักงาน"])
@section('css')
<!-- third party css -->
<link href="{{ asset('assets/css/inputdate/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
@endsection
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                        <li class="breadcrumb-item active">พนักงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">เพิ่มข้อมูลพนักงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('employee.store') }}" class="wow fadeInLeft" method="POST"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="card">
                            <div class="card border">
                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                <div class="card-header">
                                    <b>ข้อมูลพนักงาน</b>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">รหัสพนักงาน</label>
                                                <input type="number" class="form-control form-control-sm form-control-required" id="emp_id"
                                                    name="emp_id" value="{{ old('emp_id') }}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">สาขา</label>
                                                <select class="form-select form-select-sm" aria-label=".form-select-sm"
                                                    id="branch_id" name="branch_id">
                                                    <option value="0" selected="selected">-</option>
                                                    @foreach ($branch as $list)
                                                    <option value="{{ $list->branch_id }}" @if(old('branch_id')==$list->
                                                        branch_id) selected @endif>{{ $list->branch_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">แผนก</label>
                                                <select class="form-select form-select-sm form-control-required" aria-label=".form-select-sm"
                                                    id="dept_id" name="dept_id" required>
                                                    <option value="" selected="selected" disabled>-</option>
                                                    @foreach ($department as $list)
                                                    <option value="{{ $list->dept_id }}" @if(old('dept_id')==$list->dept_id) selected @endif>
                                                        @if ($list->level == "1") - @endif
                                                        @if ($list->level == "2") -- @endif
                                                        @if ($list->level == "3") --- @endif
                                                        @if ($list->level == "4") ---- @endif
                                                        {{ $list->dept_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">พื้นที่การขาย</label>
                                                <select class="form-select form-select-sm" aria-label=".form-select-sm"
                                                    id="area_code" name="area_code">
                                                    <option value="" selected>ไม่ระบุ</option>
                                                    @foreach ($sales_area as $area)
                                                    <option value="{{ $area->area_code }}" @if(old('area_code')==$area->area_code) selected @endif>
                                                        {{ $area->area_code }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">ตำแหน่ง</label>
                                                <select class="form-select form-select-sm form-control-required" aria-label=".form-select-sm"
                                                    id="position_id" name="position_id" required>
                                                    <option value="0" selected="selected">-</option>
                                                    @foreach ($position as $list)
                                                    <option value="{{ $list->position_id }}"
                                                        @if(old('position_id')==$list->position_id) selected @endif>{{
                                                        $list->position_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">ประเภทพนักงาน</label>
                                                <select class="form-select form-select-sm form-control-required" aria-label=".form-select-sm"
                                                    id="emp_type" name="emp_type" required>
                                                    <option value="" selected="selected" disabled>-</option>
                                                    <option value="D" @if(old('emp_type')=='D' ) selected @endif>
                                                        รายวัน</option>
                                                    <option value="M" @if(old('emp_type')=='M' ) selected @endif>
                                                        รายเดือน</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">สถานะพนักงาน</label>
                                                <select class="form-select form-select-sm form-control-required" aria-label=".form-select-sm"
                                                    id="emp_status" name="emp_status" required>
                                                    <option value="" selected="selected" disabled>-</option>
                                                    <option value="1" @if(old('emp_status') == '1') selected
                                                        @endif>ปกติ</option>
                                                    <option value="2" @if(old('emp_status') == '2') selected
                                                        @endif>ทดลองงาน</option>
                                                    <option value="0" @if(old('emp_status') == '0') selected
                                                        @endif>ลาออก</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">เบอร์สำนักงาน</label>
                                                <input type="number" class="form-control form-control-sm" id="tel"
                                                    name="tel" value="{{ old('tel') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">เบอร์สำนักงาน 2</label>
                                                <input type="number" class="form-control form-control-sm" id="tel2"
                                                    name="tel2" value="{{ old('tel2') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">วันที่เข้างาน</label>
                                                <div class="input-group date">
                                                    <input type="text"
                                                        class="form-control form-control-sm form-control-required emp-datepicker"
                                                        id="start_work_date" name="start_work_date"
                                                        value="{{ old('start_work_date') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">วันที่ออกงาน</label>
                                                <div class="input-group date">
                                                    <input type="text"
                                                        class="form-control form-control-sm emp-datepicker"
                                                        id="end_work_date" name="end_work_date"
                                                        value="{{ old('end_work_date') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">รายละเอียดเพิ่มเติม</label>
                                                <input type="text" class="form-control form-control-sm" id="detail"
                                                    name="detail" value="{{ old('detail') }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card border">
                                <div class="card-header">
                                    <b>ข้อมูลส่วนตัวพนักงาน</b>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label class="control-label "><strong class="text-danger">*</strong> คำนำหน้า</label><br>
                                                <div class="form-check-inline">
                                                    <input class="form-check-input" type="radio" id="title1"
                                                        name="title" value="นาย" @if(old('title')=='นาย' ) checked
                                                        @endif>
                                                    <label class="form-check-label" for="title1">นาย</label>
                                                </div>
                                                <div class="form-check-inline">
                                                    <input class="form-check-input" type="radio" id="title2"
                                                        name="title" value="นางสาว" @if(old('title')=='นางสาว' ) checked
                                                        @endif>
                                                    <label class="form-check-label" for="title2">นางสาว</label>
                                                </div>
                                                <div class="form-check-inline">
                                                    <input class="form-check-input" type="radio" id="title3"
                                                        name="title" value="นาง" @if(old('title')=='นาง' ) checked
                                                        @endif>
                                                    <label class="form-check-label" for="title3">นาง</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">เลขบัตรประจำตัวประชาชน</label>
                                                <input type="number" class="form-control form-control-sm"
                                                    id="personal_id" name="personal_id"
                                                    value="{{ old('personal_id') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">ว/ด/ป เกิด</label>
                                                <div class="input-group date">
                                                    <input type="text"
                                                        class="form-control form-control-sm form-control-required emp-datepicker"
                                                        id="birth_date" name="birth_date"
                                                        value="{{ old('birth_date') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">เชื้อชาติ</label>
                                                <select class="form-select form-select-sm" aria-label=".form-select"
                                                    id="ethnicity" name="ethnicity">
                                                    <option value="" selected="selected" disabled>-</option>
                                                    <option value="ไทย" @if(old('ethnicity')=='ไทย' ) selected @endif>
                                                        ไทย</option>
                                                    <option value="อื่นๆ" @if(old('ethnicity')=='อื่นๆ' ) selected
                                                        @endif>อื่นๆ</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">สัญชาติ</label>
                                                <select class="form-select form-select-sm" aria-label=".form-select"
                                                    id="nationality" name="nationality">
                                                    <option value="" selected="selected" disabled>-</option>
                                                    <option value="ไทย" @if(old('nationality')=='ไทย' ) selected @endif>
                                                        ไทย</option>
                                                    <option value="อื่นๆ" @if(old('nationality')=='อื่นๆ' ) selected
                                                        @endif>อื่นๆ</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">ศาสนา</label>
                                                <select class="form-select form-select-sm" aria-label=".form-select"
                                                    id="religion" name="religion">
                                                    <option value="" selected="selected" disabled>-</option>
                                                    <option value="พุทธ" @if(old('religion')=='พุทธ' ) selected @endif>
                                                        พุทธ</option>
                                                    <option value="อิสลาม" @if(old('religion')=='อิสลาม' ) selected
                                                        @endif>อิสลาม</option>
                                                    <option value="คริสต์" @if(old('religion')=='คริสต์' ) selected
                                                        @endif>คริสต์</option>
                                                    <option value="ฮินดู" @if(old('religion')=='ฮินดู' ) selected
                                                        @endif>ฮินดู</option>
                                                    <option value="ซิกข์" @if(old('religion')=='ซิกข์' ) selected
                                                        @endif>ซิกข์</option>
                                                    <option value="ขงจื๊อ" @if(old('religion')=='ขงจื๊อ' ) selected
                                                        @endif>ลัทธิขงจื๊อ</option>
                                                    <option value="ไม่มี" @if(old('religion')=='ไม่มี' ) selected
                                                        @endif>ไม่มี</option>
                                                    <option value="อื่นๆ" @if(old('religion')=='อื่นๆ' ) selected
                                                        @endif>อื่นๆ</option>
                                                    <option value="ไม่ระบุ" @if(old('religion')=='ไม่ระบุ' ) selected
                                                        @endif>ไม่ระบุ</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">ชื่อ ภาษาไทย</label>
                                                <input type="text" class="form-control form-control-sm form-control-required" id="name"
                                                    name="name" value="{{ old('name') }}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">นามสกุล ภาษาไทย</label>
                                                <input type="text" class="form-control form-control-sm" id="surname"
                                                    name="surname" value="{{ old('surname') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">ชื่อเล่น</label>
                                                <input type="text" class="form-control form-control-sm" id="nickname"
                                                    name="nickname" value="{{ old('nickname') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">เพศ</label>
                                                <select class="form-select form-select-sm form-control-required" aria-label=".form-select"
                                                    id="gender" name="gender" required>
                                                    <option value="" selected="selected" disabled>-</option>
                                                    <option value="M" @if(old('gender')=='M' ) selected
                                                    @endif>ชาย</option>
                                                    <option value="F" @if(old('gender')=='F' ) selected
                                                    @endif>หญิง</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">ชื่อ ภาษาอังกฤษ</label>
                                                <input type="text" class="form-control form-control-sm" id="name_en"
                                                    name="name_en" value="{{ old('name_en') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">นามสกุล ภาษาอังกฤษ</label>
                                                <input type="text" class="form-control form-control-sm" id="surname_en"
                                                    name="surname_en" value="{{ old('surname_en') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">อีเมล</label>
                                                <input type="email" class="form-control form-control-sm" id="email"
                                                    name="email" value="{{ old('email') }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">เบอร์มือถือ</label>
                                                <input type="number" class="form-control form-control-sm" id="phone"
                                                    name="phone" value="{{ old('phone') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-2 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">เบอร์มือถือ 2</label>
                                                <input type="number" class="form-control form-control-sm" id="phone2"
                                                    name="phone2" value="{{ old('phone2') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">ทะเบียนรถ</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="vehicle_registration" name="vehicle_registration"
                                                    value="{{ old('vehicle_registration') }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border">
                                <div class="card-header">
                                    <b>ข้อมูลที่อยู่พนักงาน (ตามทะเบียนบ้าน)</b>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12 pt-2">
                                            <div class="form-group">
                                                <label class="control-label nFont">ที่อยู่</label>
                                                <textarea rows="3" class="form-control form-control-sm" id="address"
                                                    name="address" style="resize: none">{{ old('address') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">จังหวัด</label>
                                                <select class="form-select form-select-sm" aria-label=".form-select"
                                                    id="selChangwat" name="province" onchange="showAmphoes()">
                                                    <option value="" selected="selected">เลือกจังหวัด</option>
                                                    @foreach ($changwats as $key => $value)
                                                    <option value="{{ $value }}" @if(old('province')==$value) selected
                                                        @endif>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">อำเภอ/เขต</label>
                                                <select class="form-select form-select-sm" aria-label=".form-select"
                                                    id="selAmphoe" name="district" onchange="showTambons()">
                                                    <option value="" selected>เลือกอำเภอ/เขต</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">ตำบล/แขวง</label>
                                                <select class="form-select form-select-sm" aria-label=".form-select"
                                                    id="selTambon" name="subdistrict" onchange="showZipcode()">
                                                    <option value="" selected>เลือกตำบล/แขวง</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">รหัสไปรษณีย์</label>
                                                <input type="number" class="form-control form-control-sm"
                                                    id="txtZipcode" name="zipcode" value="{{ old('zipcode') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">ประเทศ</label>
                                                <select class="form-select form-select-sm" aria-label=".form-select"
                                                    id="country" name="country">
                                                    <option value="ไทย" @if(old('country')=='ไทย' ) selected @endif>ไทย
                                                    </option>
                                                    <option value="เมียนมาร์" @if(old('country')=='เมียนมาร์' ) selected
                                                        @endif>เมียนมาร์</option>
                                                    <option value="ลาว" @if(old('country')=='ลาว' ) selected @endif>ลาว
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border">
                                <div class="card-header">
                                    <b>ข้อมูลที่อยู่พนักงาน (ปัจจุบัน)</b>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12 pt-2">
                                            <div class="form-group">
                                                <label class="control-label nFont">ที่อยู่</label>
                                                <textarea rows="3" class="form-control form-control-sm" id="current_address"
                                                    name="current_address" style="resize: none">{{ old('current_address') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">จังหวัด</label>
                                                <select class="form-select form-select-sm" aria-label=".form-select"
                                                    id="selCurrentChangwat" name="current_province" onchange="showCurrentAmphoes()">
                                                    <option value="" selected="selected">เลือกจังหวัด</option>
                                                    @foreach ($changwats as $key => $value)
                                                    <option value="{{ $value }}" @if(old('current_province')==$value) selected
                                                        @endif>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">อำเภอ/เขต</label>
                                                <select class="form-select form-select-sm" aria-label=".form-select"
                                                    id="selCurrentAmphoe" name="current_district" onchange="showCurrentTambons()">
                                                    <option value="" selected>เลือกอำเภอ/เขต</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">ตำบล/แขวง</label>
                                                <select class="form-select form-select-sm" aria-label=".form-select"
                                                    id="selCurrentTambon" name="current_subdistrict" onchange="showCurrentZipcode()">
                                                    <option value="" selected>เลือกตำบล/แขวง</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">รหัสไปรษณีย์</label>
                                                <input type="number" class="form-control form-control-sm"
                                                    id="txtCurrentZipcode" name="current_zipcode" value="{{ old('current_zipcode') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2 col-6 pt-2">
                                            <div class="form-group">
                                                <label class="control-label">ประเทศ</label>
                                                <select class="form-select form-select-sm" aria-label=".form-select"
                                                    id="current_country" name="current_country">
                                                    <option value="ไทย" @if(old('current_country')=='ไทย' ) selected @endif>ไทย
                                                    </option>
                                                    <option value="เมียนมาร์" @if(old('current_country')=='เมียนมาร์' ) selected
                                                        @endif>เมียนมาร์</option>
                                                    <option value="ลาว" @if(old('current_country')=='ลาว' ) selected @endif>ลาว
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="btn btn-secondary" href="{{ url('/organization/employees') }}">ย้อนกลับ</a>
                        <button type="submit" class="btn btn-primary mx-2">บันทึก</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script src="{{ asset('assets/js/inputdate/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
<script src="{{ asset('assets/js/inputdate/form-pickers.init.js') }}"></script>
<script src="{{ asset('assets/js/province.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    $(document).ready(function() {
        $("#dept_id").change(function () {
            $("#area_code").empty();
            $("#area_code").append("<option value=''>ไม่ระบุ</option>");
            $.ajax({
                url: '{{ route("employee.sales-area") }}',
                type: "get",
                data: {
                    dept_id: $("#dept_id").val(),
                },
                dataType: "json",
                success: function (response) {
                    var len = response.sales_area.length;
                    for (var i = 0; i < len; i++) {
                        var id = response.sales_area[i]["area_code"];
                        var name = response.sales_area[i]["area_code"];
                        $("#area_code").append(
                            "<option value='" + id + "'>" + name + "</option>"
                        );
                    }
                },
            });
        });
    });
</script>
@endsection
