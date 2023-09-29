@extends('layouts.master-layout', ['page_title' => "ดูข้อมูลพนักงาน"])
@section('css')
    {{-- inputdate --}}
    <link href="{{ asset('assets/css/inputdate/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
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
                <h4 class="page-title">ดูข้อมูลพนักงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card">
                        <div class="card border">
                            <div class="card-header">
                                <b>ข้อมูลพนักงาน</b>
                            </div>
                            <div class="card-body">
                                <h5 class="mb-2">รหัสพนักงาน : <span class="text-primary me-2">{{ $employee->emp_id }}</span></h5>
                                <h5 class="mb-2">ประเภทพนักงาน : <span class="text-primary me-2">
                                    @if($employee->emp_type == 'D') รายวัน @endif
                                    @if($employee->emp_type == 'M') รายเดือน @endif
                                    </span>
                                </h5>
                                <h5 class="mb-2">สถานะพนักงาน : <span class="text-primary me-2">
                                    @if($employee->emp_status == '1') <span class="text-success">ปกติ</span> @endif
                                    @if($employee->emp_status == '2') <span class="text-info">ทดลองงาน</span> @endif
                                    @if($employee->emp_status == '0') <span class="text-danger">ลาออก</span> @endif
                                    </span>
                                </h5>
                                <h5 class="mb-2">วันที่เข้างาน : <span class="text-primary me-2">@if($employee->start_work_date!='' && $employee->start_work_date!='0000-00-00'){{\Carbon\Carbon::parse($employee->start_work_date)->format('d/m/Y')}}@endif</span></h5>
                                <h5 class="mb-2">วันที่ออกงาน : <span class="text-primary me-2">@if($employee->end_work_date!='' && $employee->end_work_date!='0000-00-00'){{\Carbon\Carbon::parse($employee->end_work_date)->format('d/m/Y')}}@endif</span></h5>
                                <h5 class="mb-2">บริษัท : <span class="text-primary me-2">@if ($deptL0){{ $deptL0->dept_name }}@else-@endif</span></h5>
                                <h5 class="mb-2">สาขา : <span class="text-primary me-2">@if ($branch){{ $branch->branch_name }}@else-@endif</span></h5>
                                <h5 class="mb-2">ส่วน : <span class="text-primary me-2">@if ($deptL1){{ $deptL1->dept_name }}@else-@endif</span></h5>
                                <h5 class="mb-2">ฝ่าย : <span class="text-primary me-2">@if ($deptL2){{ $deptL2->dept_name }}@else-@endif</span></h5>
                                <h5 class="mb-2">แผนก : <span class="text-primary me-2">@if ($deptL3){{ $deptL3->dept_name }}@else-@endif</span></h5>
                                <h5 class="mb-2">หน่วยงาน : <span class="text-primary me-2">@if ($deptL4){{ $deptL4->dept_name }}@else-@endif</span></h5>
                                @if ($employee->area_code != "")
                                <h5 class="mb-2">พื้นที่การขาย : <span class="text-primary me-2">{{ $employee->area_code }}</span></h5>
                                @endif
                                <h5 class="mb-2">ตำแหน่ง : <span class="text-primary me-2">@if ($position){{ $position->position_name }}@else-@endif</span></h5>
                                <h5 class="mb-2">เบอร์สำนักงาน : <span class="text-primary me-2">{{ $employee->tel }}</span></h5>
                                <h5 class="mb-2">เบอร์สำนักงาน 2 : <span class="text-primary me-2">{{ $employee->tel2 }}</span></h5>
                                <h5 class="mb-2">รายละเอียดเพิ่มเติม : <span class="text-primary me-2">{{ $employee->detail }}</span></h5>
                            </div>
                        </div>
                        <div class="card border">
                            <div class="card-header">
                                <b>ข้อมูลส่วนตัวพนักงาน</b>
                            </div>
                            <div class="card-body">
                                <h5 class="mb-2">คำนำหน้า : <span class="text-primary me-2">{{ $employee->title }}</span></h5>
                                <h5 class="mb-2">ชื่อ ภาษาไทย : <span class="text-primary me-2">{{ $employee->name }}</span></h5>
                                <h5 class="mb-2">นามสกุล ภาษาไทย : <span class="text-primary me-2">{{ $employee->surname }}</span></h5>
                                <h5 class="mb-2">ชื่อ ภาษาอังกฤษ : <span class="text-primary me-2">{{ $employee->name_en }}</span></h5>
                                <h5 class="mb-2">นามสกุล ภาษาอังกฤษ : <span class="text-primary me-2">{{ $employee->surname_en }}</span></h5>
                                <h5 class="mb-2">เลขบัตรประจำตัวประชาชน : <span class="text-primary me-2">{{ $employee->personal_id }}</span></h5>
                                <h5 class="mb-2">วัน/เดือน/ปี เกิด : <span class="text-primary me-2">{{ \Carbon\Carbon::parse($employee->birth_date)->format('d/m/Y') }}</span></h5>
                                <h5 class="mb-2">เชื้อชาติ : <span class="text-primary me-2">{{ $employee->ethnicity }}</span></h5>
                                <h5 class="mb-2">สัญชาติ : <span class="text-primary me-2">{{ $employee->nationality }}</span></h5>
                                <h5 class="mb-2">ศาสนา : <span class="text-primary me-2">{{ $employee->religion }}</span></h5>
                                <h5 class="mb-2">อีเมล : <span class="text-primary me-2">{{ $employee->email }}</span></h5>
                                <h5 class="mb-2">เบอร์มือถือ : <span class="text-primary me-2">{{ $employee->phone }}</span></h5>
                                <h5 class="mb-2">เบอร์มือถือ 2 : <span class="text-primary me-2">{{ $employee->phone2 }}</span></h5>
                                <h5 class="mb-2">ทะเบียนรถ : <span class="text-primary me-2">{{ $employee->vehicle_registration }}</span></h5>
                            </div>
                        </div>

                        <div class="card border">
                            <div class="card-header">
                                <b>ข้อมูลที่อยู่พนักงาน (ตามทะเบียนบ้าน)</b>
                            </div>
                            <div class="card-body">
                                <h5 class="mb-2">ที่อยู่ : <span class="text-primary me-2">{{ $employee->address }}</span></h5>
                                <h5 class="mb-2">ตำบล/แขวง : <span class="text-primary me-2">{{ $employee->subdistrict }}</span></h5>
                                <h5 class="mb-2">อำเภอ/เขต : <span class="text-primary me-2">{{ $employee->district }}</span></h5>
                                <h5 class="mb-2">จังหวัด : <span class="text-primary me-2">{{ $employee->province }}</span></h5>
                                <h5 class="mb-2">รหัสไปรษณีย์ : <span class="text-primary me-2">{{ $employee->zipcode }}</span></h5>
                                <h5 class="mb-2">ประเทศ : <span class="text-primary me-2">{{ $employee->country }}</span></h5>
                            </div>
                        </div>

                        <div class="card border">
                            <div class="card-header">
                                <b>ข้อมูลที่อยู่พนักงาน (ปัจจุบัน)</b>
                            </div>
                            <div class="card-body">
                                <h5 class="mb-2">ที่อยู่ : <span class="text-primary me-2">{{ $employee->current_address }}</span></h5>
                                <h5 class="mb-2">ตำบล/แขวง : <span class="text-primary me-2">{{ $employee->current_subdistrict }}</span></h5>
                                <h5 class="mb-2">อำเภอ/เขต : <span class="text-primary me-2">{{ $employee->current_district }}</span></h5>
                                <h5 class="mb-2">จังหวัด : <span class="text-primary me-2">{{ $employee->current_province }}</span></h5>
                                <h5 class="mb-2">รหัสไปรษณีย์ : <span class="text-primary me-2">{{ $employee->current_zipcode }}</span></h5>
                                <h5 class="mb-2">ประเทศ : <span class="text-primary me-2">{{ $employee->current_country }}</span></h5>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-secondary" onclick="history.back()">ย้อนกลับ</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<!-- third party js ends -->
@endsection