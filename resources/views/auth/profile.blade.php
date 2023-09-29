@extends('layouts.master-layout', ['page_title' => "โปรไฟล์"])
@section('css')
<style>
.avatar-container {
  width: 150px;
  height: 150px;
  display: block;
  margin: 0 auto;
}
.avatar-outer {
  width: 100% !important;
  height: 100% !important;
  max-width: 150px !important; /* any size */
  max-height: 150px !important; /* any size */
  margin: auto;
  border-radius: 100%;
  position: relative;
}
.avatar-outer > img {
    display: inline-block;
    overflow: hidden;
}
.avatar-inner {
  background-color: #dee2e6;
  width: 40px;
  height: 40px;
  border: 2px solid #ffffff;
  border-radius: 100%;
  position: absolute;
  bottom: 0;
  right: 0;
}
.avatar-inner:hover {
  background-color: #cccccc;
}
.avatar-inner > i {
    font-size: 1.25rem;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: inline-block;
    overflow: hidden;
    width: 36px;
    height: 36px;
    cursor: pointer;
    line-height: 36px;
    text-align: center;
}
.avatar-profile {
    width:150px;
    height:150px;
}
.avatar-preview {
    width: 200px;
}
.input-upload-custom {
    background: #e4e0fa;
    color: #7536f9;
    border-bottom-left-radius: 35px;
    cursor: pointer;
}
.input-upload-custom::file-selector-button {
    background: #7536f9;
    color: #fff;
    padding: 8px 16px;
    border: none;
    border-top-right-radius: 35px;
    cursor: pointer;
}
</style>
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
                        <li class="breadcrumb-item active">โปรไฟล์</li>
                    </ol>
                </div>
                <h4 class="page-title">โปรไฟล์</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-lg-4 col-xl-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="card-box">
                        <div class="avatar-container">
                            <div class="avatar-outer">
                                <img src="{{url('assets/images/users/'.$user->image)}}" onerror="this.onerror=null;this.src='{{url('assets/images/users/thumbnail/user-1.jpg')}}'" class="rounded-circle avatar-profile">
                                <div class="avatar-inner">
                                    <i class="fe-camera" data-bs-toggle="modal" data-bs-target="#updateAvatarModal"></i>
                                </div>
                            </div>
                        </div>

                        <h4 class="mb-0">{{ $user->name . ' ' . $emp->surname }} @if ($user->is_flag == 1)<i class="mdi mdi-check-decagram text-success" title="ยืนยันตัวตนแล้ว"></i> @endif</h4>
                        <p class="text-primary">{!! '@'.$user->emp_id !!}</p>

                        <div class="text-start mt-3">
                            @if ($emp->detail != "")
                            <h4 class="font-14 text-uppercase">About Me :</h4>
                            <p class="text-muted font-14 mb-3">
                                {{ $emp->detail }}
                            </p>
                            @endif
                            <p class="text-muted mb-2 font-14"><strong>ชื่อ-สกุล :</strong> <span class="ms-2">{{
                                    $emp->name . ' ' . $emp->surname }} @if($emp->nickname!="") ({{ $emp->nickname }}) @endif</span></p>
                            <p class="text-muted mb-2 font-14"><strong>เบอร์สำนักงาน :</strong><span class="ms-2">@if($emp->tel!=""){{
                                    $emp->tel }}@endif @if($emp->tel2!=""), {{ $emp->tel2 }}@endif</span></p>
                            <p class="text-muted mb-2 font-14"><strong>เบอร์มือถือ :</strong><span class="ms-2">{{
                                    $emp->phone }}@if($emp->phone2!=""), {{ $emp->phone2 }}@endif</span></p>
                            <p class="text-muted mb-2 font-14"><strong>อีเมล :</strong> <span class="ms-2">{{
                                    $emp->email }}</span></p>
                            <p class="text-muted mb-2 font-14"><strong>แผนก :</strong> <span class="ms-2">{{
                                    $emp->dept_name }}</span></p>
                            <p class="text-muted mb-2 font-14"><strong>ตำแหน่ง :</strong> <span class="ms-2">{{
                                    $emp->position_name }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <ul class="nav nav-pills nav-fill navtab-bg">
                            <li class="nav-item">
                                <a href="#aboutme" data-bs-toggle="tab" aria-expanded="false"
                                    class="nav-link @if (blank($errors)) active @endif">
                                    <i class="mdi mdi-account-circle me-1"></i>เกี่ยวกับฉัน
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#personal" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                    <i class="mdi mdi-square-edit-outline me-1"></i>แก้ไขข้อมูลส่วนตัว
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#changepassword" data-bs-toggle="tab" aria-expanded="false"
                                    class="nav-link @if (!blank($errors)) active @endif">
                                    <i class="mdi mdi-account-key-outline me-1"></i>เปลี่ยนรหัสผ่าน
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane @if (blank($errors)) show active @endif" id="aboutme">
                                <div class="border border-light p-2 mb-3">
                                    <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle me-1"></i>
                                        ข้อมูลพนักงาน</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="firstname" class="form-label">ชื่อ</label>
                                                <input type="text" class="form-control" value="{{ $emp->name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">นามสกุล</label>
                                                <input type="text" class="form-control" value="{{ $emp->surname }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">ชื่อเล่น</label>
                                                <input type="text" class="form-control" value="{{ $emp->nickname }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">เพศ</label>
                                                <input type="text" class="form-control"
                                                    value="{{ ($emp->gender=='M') ? 'ชาย' : 'หญิง' }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">เชื้อชาติ</label>
                                                <input type="text" class="form-control" value="{{ $emp->ethnicity }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">สัญชาติ</label>
                                                <input type="text" class="form-control" value="{{ $emp->nationality }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">ศาสนา</label>
                                                <input type="text" class="form-control" value="{{ $emp->religion }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="firstname" class="form-label">วัน/เดือน/ปีเกิด</label>
                                                <input type="text" class="form-control"
                                                    value="{{\Carbon\Carbon::parse($emp->birth_date)->thaidate('d/m/Y')}}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">รหัสพนักงาน</label>
                                                <input type="text" class="form-control" value="{{ $emp->emp_id }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">ประเภทพนักงาน</label>
                                                <input type="text" class="form-control"
                                                    value="{{ ($emp->emp_type=='M') ? 'รายเดือน' : 'รายวัน' }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">สถานะพนักงาน</label>
                                                @if ($emp->emp_status=='1')
                                                <input type="text" class="form-control" value="ปกติ" readonly>
                                                @endif
                                                @if ($emp->emp_status=='2')
                                                <input type="text" class="form-control" value="ทดลอง" readonly>
                                                @endif
                                                @if ($emp->emp_status=='0')
                                                <input type="text" class="form-control" value="ลาออก" readonly>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">วันที่เข้าทำงาน</label>
                                                <input type="text" class="form-control"
                                                    value="{{\Carbon\Carbon::parse($emp->start_work_date)->thaidate('d/m/Y')}}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="firstname" class="form-label">สาขา</label>
                                                <input type="text" class="form-control" value="{{ $emp->branch_name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">หน่วยงาน/แผนก</label>
                                                <input type="text" class="form-control" value="{{ $emp->dept_name }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">ตำแหน่ง</label>
                                                <input type="text" class="form-control" value="{{ $emp->position_name }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="firstname" class="form-label">อีเมล</label>
                                                <input type="text" class="form-control" value="{{ $emp->email }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="firstname" class="form-label">เบอร์สำนักงาน</label>
                                                <input type="text" class="form-control" value="{{ $emp->tel }}@if($emp->tel2!=""), {{ $emp->tel2 }}@endif" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="firstname" class="form-label">เบอร์มือถือ</label>
                                                <input type="text" class="form-control" value="{{ $emp->phone }}@if($emp->phone2!=""), {{ $emp->phone2 }}@endif" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border border-light p-2 mb-3">
                                    <h5 class="mb-4 text-uppercase"><i class="mdi mdi-home-circle me-1"></i> ข้อมูลที่อยู่ตามทะเบียนบ้าน
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">ที่อยู่</label>
                                                <textarea class="form-control" rows="3"
                                                    readonly>{{ $emp->address }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="firstname" class="form-label">ตำบล/แขวง</label>
                                                <input type="text" class="form-control" value="{{ $emp->subdistrict }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">อำเภอ/เขต</label>
                                                <input type="text" class="form-control" value="{{ $emp->district }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">จังหวัด</label>
                                                <input type="text" class="form-control" value="{{ $emp->province }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">รหัสไปรษณีย์</label>
                                                <input type="text" class="form-control" value="{{ $emp->zipcode }}"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border border-light p-2 mb-3">
                                    <h5 class="mb-4 text-uppercase"><i class="mdi mdi-home-circle me-1"></i> ข้อมูลที่อยู่ปัจจุบัน
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">ที่อยู่</label>
                                                <textarea class="form-control" rows="3"
                                                    readonly>{{ $emp->current_address }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="firstname" class="form-label">ตำบล/แขวง</label>
                                                <input type="text" class="form-control" value="{{ $emp->current_subdistrict }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">อำเภอ/เขต</label>
                                                <input type="text" class="form-control" value="{{ $emp->current_district }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">จังหวัด</label>
                                                <input type="text" class="form-control" value="{{ $emp->current_province }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">รหัสไปรษณีย์</label>
                                                <input type="text" class="form-control" value="{{ $emp->current_zipcode }}"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border border-light p-2 mb-3">
                                    <h5 class="mb-4 text-uppercase"><i class="mdi mdi-office-building me-1"></i>
                                        ข้อมูลบริษัท</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="firstname" class="form-label">ชื่อบริษัท</label>
                                                <input type="text" class="form-control" value="บริษัท อี.แอนด์ วี. จำกัด"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">เว็บไซต์</label>
                                                <input type="text" class="form-control" value="https://www.kaceebest.com/"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="lastname" class="form-label">ที่อยู่บริษัท</label>
                                                <textarea class="form-control" rows="3"
                                                    readonly>สาขาสำนักงานใหญ่&#13;&#10;259 ถนนเลียบคลองภาษีเจริญฝั่งใต้ แขวงหนองแขม เขตหนองแขม กรุงเทพมหานคร 10160</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="personal">
                                <div class="float-end me-2">
                                    <button type="button" id="btn-edit-personal-data" class="btn btn-light waves-effect waves-light mt-2"><i
                                        class="mdi mdi-square-edit-outline"></i> แก้ไข</button>
                                    <button type="button" id="btn-close-personal-data" class="btn btn-light waves-effect waves-light mt-2 d-none"><i
                                        class="mdi mdi-window-close"></i> ปิด</button>
                                </div>
                                <div class="_view">
                                    <div class="border border-light p-2 mb-3">
                                        <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle me-1"></i>
                                            ข้อมูลส่วนตัว</h5>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="firstname" class="form-label">ชื่อ ภาษาไทย</label>
                                                    <input type="text" class="form-control" value="{{ $emp->name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="lastname" class="form-label">นามสกุล ภาษาไทย</label>
                                                    <input type="text" class="form-control" value="{{ $emp->surname }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="lastname" class="form-label">ชื่อเล่น</label>
                                                    <input type="text" class="form-control" value="{{ $emp->nickname }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="firstname" class="form-label">เบอร์สำนักงาน</label>
                                                    <input type="text" class="form-control" value="{{ $emp->tel }}@if($emp->tel2!=""), {{ $emp->tel2 }}@endif" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="firstname" class="form-label">เบอร์มือถือ</label>
                                                    <input type="text" class="form-control" value="{{ $emp->phone }}@if($emp->phone2!=""), {{ $emp->phone2 }}@endif" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="lastname" class="form-label">อีเมล</label>
                                                    <input type="text" class="form-control" value="{{ $emp->email }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="lastname" class="form-label">รายละเอียดเพิ่มเติม</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $emp->detail }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border border-light p-2 mb-3">
                                        <h5 class="mb-4 text-uppercase"><i class="mdi mdi-home-circle me-1"></i> ข้อมูลที่อยู่ปัจจุบัน
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="lastname" class="form-label">ที่อยู่</label>
                                                    <textarea class="form-control" rows="3"
                                                        readonly>{{ $emp->current_address }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="firstname" class="form-label">ตำบล/แขวง</label>
                                                    <input type="text" class="form-control" value="{{ $emp->current_subdistrict }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="lastname" class="form-label">อำเภอ/เขต</label>
                                                    <input type="text" class="form-control" value="{{ $emp->current_district }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="lastname" class="form-label">จังหวัด</label>
                                                    <input type="text" class="form-control" value="{{ $emp->current_province }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="lastname" class="form-label">รหัสไปรษณีย์</label>
                                                    <input type="text" class="form-control" value="{{ $emp->current_zipcode }}"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <form name="personal-data-form" method="POST" action="{{ route('profile.update.personal-data') }}" onsubmit="return SubmitPersonalDataForm(this);">
                                    {{ csrf_field() }}
                                    <div class="_edit d-none">
                                        <input type="hidden" class="form-control" id="input-update-personal-data" name="input-update-personal-data" value="0">
                                        <div class="border border-light p-2 mb-3">
                                            <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle me-1"></i>
                                                ข้อมูลส่วนตัว</h5>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="nickname" class="form-label">ชื่อเล่น</label>
                                                        <input type="text" class="form-control input-edit" name="nickname" value="{{ $emp->nickname }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="tel" class="form-label">เบอร์สำนักงาน 1</label>
                                                        <input type="text" class="form-control input-edit" name="tel" value="{{ $emp->tel }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="tel2" class="form-label">เบอร์สำนักงาน 2</label>
                                                        <input type="text" class="form-control input-edit" name="tel2" value="{{ $emp->tel2 }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="phone" class="form-label">เบอร์มือถือ 1</label>
                                                        <input type="text" class="form-control input-edit" name="phone" value="{{ $emp->phone }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="phone2" class="form-label">เบอร์มือถือ 2</label>
                                                        <input type="text" class="form-control input-edit" name="phone2" value="{{ $emp->phone2 }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">อีเมล</label>
                                                        <input type="email" class="form-control input-edit" name="email" value="{{ $emp->email }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="detail" class="form-label">รายละเอียดเพิ่มเติม</label>
                                                        <input type="text" class="form-control input-edit" name="detail" value="{{ $emp->detail }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border border-light p-2 mb-3">
                                            <h5 class="mb-4 text-uppercase"><i class="mdi mdi-home-circle me-1"></i> ข้อมูลที่อยู่ปัจจุบัน
                                            </h5>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="current_address" class="form-label">ที่อยู่</label>
                                                        <textarea class="form-control input-edit" rows="3" name="current_address"
                                                            readonly>{{ $emp->current_address }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="current_subdistrict" class="form-label">ตำบล/แขวง</label>
                                                        <input type="text" class="form-control input-edit" name="current_subdistrict" value="{{ $emp->current_subdistrict }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="current_district" class="form-label">อำเภอ/เขต</label>
                                                        <input type="text" class="form-control input-edit" name="current_district" value="{{ $emp->current_district }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="current_province" class="form-label">จังหวัด</label>
                                                        <input type="text" class="form-control input-edit" name="current_province" value="{{ $emp->current_province }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="current_zipcode" class="form-label">รหัสไปรษณีย์</label>
                                                        <input type="text" class="form-control input-edit" name="current_zipcode" value="{{ $emp->current_zipcode }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="float-end">
                                        <button type="submit" id="btn-submit-personal-data" class="btn btn-primary waves-effect waves-light mt-2 d-none"><i
                                            class="mdi mdi-content-save"></i> อัปเดต</button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane @if (!blank($errors)) show active @endif" id="changepassword">
                                <form name="password-form" method="POST" action="{{ route('profile.change.password') }}" onsubmit="return SubmitForm(this);">
                                    {{ csrf_field() }}
                                    <div class="border border-light p-2 mb-3">
                                        <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-key me-1"></i>
                                            เปลี่ยนรหัสผ่าน</h5>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="firstname" class="form-label">รหัสผ่านปัจจุบัน</label>
                                                    <div class="input-group input-group-merge">
                                                        <input type="password"
                                                            class="form-control @error('current_password') is-invalid @enderror"
                                                            id="current_password" name="current_password"
                                                            placeholder="Current Password" autocomplete="off"
                                                            value="{{ old('current_password') }}" onfocus="removeError('current_password')" required>
                                                        <div class="input-group-text" data-password="false">
                                                            <span class="password-eye"></span>
                                                        </div>
                                                        @error('current_password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="lastname" class="form-label">รหัสผ่านใหม่</label>
                                                    <div class="input-group input-group-merge">
                                                        <input type="password"
                                                            class="form-control @error('new_password') is-invalid @enderror"
                                                            id="new_password" name="new_password" placeholder="New Password"
                                                            autocomplete="off" value="{{ old('new_password') }}" onfocus="removeError('new_password')" required>
                                                        <div class="input-group-text" data-password="false">
                                                            <span class="password-eye"></span>
                                                        </div>
                                                        @error('new_password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                    <i id="8char" class="mdi mdi-close-thick" style="color:#FF0004;"></i> รหัสผ่าน 8-20 หลัก<br>
                                                    <i id="ucase" class="mdi mdi-close-thick" style="color:#FF0004;"></i> ตัวอักษรพิมพ์ใหญ่<br>
                                                    <i id="lcase" class="mdi mdi-close-thick" style="color:#FF0004;"></i> ตัวอักษรพิมพ์เล็ก<br>
                                                    <i id="num" class="mdi mdi-close-thick" style="color:#FF0004;"></i> ตัวเลข
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="lastname" class="form-label">ยืนยัน รหัสผ่านใหม่</label>
                                                    <div class="input-group input-group-merge">
                                                        <input type="password"
                                                            class="form-control @error('confirm_new_password') is-invalid @enderror"
                                                            id="confirm_new_password" name="confirm_new_password"
                                                            placeholder="Confirm New Password" autocomplete="off"
                                                            value="{{ old('confirm_new_password') }}" onfocus="removeError('new_password')" required>
                                                        <div class="input-group-text" data-password="false">
                                                            <span class="password-eye"></span>
                                                        </div>
                                                        @error('confirm_new_password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                    <strong class="form-text confirm-message"></strong>
                                                    <i id="pwmatch" class="mdi mdi-close-thick" style="color:#FF0004;"></i> รหัสผ่านตรงกัน
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8 col-sm-12">
                                                <small class="text-pink">
                                                    * รหัสผ่าน 8-20 หลัก ( a-z, A-Z, 0-9 ) อักขระพิเศษ ( @, #, -, _ )<br>
                                                    * ต้องมีตัวอักษรพิมพ์ใหญ่ อย่างน้อย 1 ตัวอักษร<br>
                                                    * ต้องมีตัวอักษรพิมพ์เล็ก อย่างน้อย 1 ตัวอักษร<br>
                                                    * ต้องมีตัวเลข อย่างน้อย 1 ตัว
                                                </small>
                                            </div>
                                            <div class="col-md-4 col-sm-12 text-end">
                                                <button type="submit" class="btn btn-success waves-effect waves-light mt-2"><i
                                                    class="mdi mdi-content-save"></i> เปลี่ยนรหัสผ่าน</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateAvatarModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="fullWidthModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form name="avatar-form" action="{{ route('profile.change.avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header border-bottom">
                            <h4 class="modal-title">เปลี่ยนรูปโปรไฟล์</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 mb-4 text-center">
                                    <img id="avatarPreview" src="{{url('assets/images/users/'.$user->image)}}" onerror="this.onerror=null;this.src='{{url('assets/images/users/thumbnail/user-1.jpg')}}'" class="rounded-circle avatar-preview" alt="profile-image">
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="text-center">
                                        <input class="input-upload-custom border border-primary rounded-pill" id="avatar" name="avatar" type="file" accept="image/*" onchange="showAvatarPreview(event);" required>
                                    </div>
                                    @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-danger float-start" onclick="removeAvatarConfirmation();">ลบรูปโปรไฟล์</button>
                            <div class="float-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                <button type="submit" class="btn btn-primary">บันทึก</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div>
@endsection
@section('script')
<script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/password-validation.js') }}"></script>
<script type="text/javascript">
    var myUpdateAvatarModalModal = new bootstrap.Modal(document.getElementById('updateAvatarModal'));
    $(document).ready(function(){
            var change_avatar_error = "{{ count($errors) }}";
            if (change_avatar_error > 0) {
                myUpdateAvatarModalModal.show();
            }
            var success = "{{ session('success') ? session('success') : '' }}";
            if (success == 'password_changed') {
                Swal.fire({
                    icon: "success",
                    title: "เปลี่ยนรหัสผ่านเรียบร้อย! กรุณาเข้าสู่ระบบด้วยรหัสผ่านใหม่",
                    showConfirmButton: false,
                    timer: 3000,
                }).then(function() {
                    document.getElementById('logout-form').submit();
                });
            }
            $("#btn-edit-personal-data").click(function() {
                $("._view").addClass('d-none');
                $("._edit").removeClass('d-none');
                $("#btn-edit-personal-data").addClass('d-none');
                $("#btn-close-personal-data").removeClass('d-none');
                $("#btn-submit-personal-data").removeClass('d-none');
                $("#input-update-personal-data").val("1");
                $(".input-edit").addClass("border border-primary");
                $(".input-edit").attr("readonly", false);
            });
            $("#btn-close-personal-data").click(function() {
                $("._view").removeClass('d-none');
                $("._edit").addClass('d-none');
                $("#btn-close-personal-data").addClass('d-none');
                $("#btn-edit-personal-data").removeClass('d-none');
                $("#btn-submit-personal-data").addClass('d-none');
                $("#input-update-personal-data").val("0");
                $(".input-edit").removeClass("border border-primary");
                $(".input-edit").attr("readonly", true);
            });
        });
        function SubmitPersonalDataForm(form){
            var error = 0;
            if ($("#input-update-personal-data").val() <= 0) {
                error++;
            }
            if (error > 0) {
                return false;
            }
        }
        function SubmitForm(form){
            var error = 0;
            let current_password=$('#current_password').val();
            let password=$('#new_password').val();
            if(current_password.length <= 0){
                Swal.fire({
                    icon: "warning",
                    title: "โปรดโปรดตรวจสอบข้อมูลให้ถูกต้อง",
                    html: '<span class="text-danger">กรุณาระบุรหัสผ่านปัจจุบัน</span>',
                    timer: 3000,
                    showConfirmButton: false,
                });
                error++;
            } else {
                if (validatePassword() === false) {
                    var msg = '<span class="text-danger">รหัสผ่าน 8-20 หลัก ใช้ได้เฉพาะตัวเลข 0-9 ตัวอักษร a-z, A-Z, และอักขระพิเศษเฉพาะ @ # _ และ (-) ขีด เท่านั้น และห้ามมี "ค่าว่าง" !</span>';
                    var msg2 = '<br><small class="text-pink">\
                                    * รหัสผ่าน 8-20 หลัก ( a-z, A-Z, 0-9 ) อักขระพิเศษ ( @, #, -, _ )<br>\
                                    * ต้องมีตัวอักษรพิมพ์ใหญ่ อย่างน้อย 1 ตัวอักษร<br>\
                                    * ต้องมีตัวอักษรพิมพ์เล็ก อย่างน้อย 1 ตัวอักษร<br>\
                                    * ต้องมีตัวเลข อย่างน้อย 1 ตัว\
                                </small>';
                    Swal.fire({
                        icon: "warning",
                        title: "โปรดโปรดตรวจสอบข้อมูลให้ถูกต้อง",
                        html: msg+msg2,
                        showConfirmButton: true,
                    });
                    error++;
                }
            }
            if (error > 0) {
                return false;
            }
        }
    function showAvatarPreview(event){
        var preview = document.getElementById("avatarPreview");
        if(event.target.files.length > 0){
            var src = URL.createObjectURL(event.target.files[0]);
            preview.src = src;
        }else{
            preview.src = '';
        }
    }
    function removeAvatarConfirmation() {
        Swal.fire({
            icon: "warning",
            title: "คุณต้องการลบรูปโปรไฟล์ ใช่ไหม?",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการลบ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                const params = {
                    emp_id: "{{auth()->user()->emp_id}}",
                };
                const options = {
                    credentials: 'same-origin',
                    method: 'POST',
                    body: JSON.stringify( params ),
                    headers: new Headers({
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    }),
                };
                return fetch(`/remove-avatar`, options)
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .catch((error) => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.success == true) {
                    Swal.fire({
                        icon: "success",
                        title: result.value.message,
                        timer: 2000,
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    Swal.fire({
                        icon: "warning",
                        title: result.value.message,
                        timer: 2000,
                    });
                }
            }
        });
    }
</script>
@endsection