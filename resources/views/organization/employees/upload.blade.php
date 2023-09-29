@extends('layouts.master-layout', ['page_title' => "อัปโหลดข้อมูลพนักงาน"])
@section('css')
    <!-- third party css -->
    <link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/ladda/ladda.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->
    <style>
        .dropify-message > p {
            font-size: 1.75rem;
            color: #cccccc;
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
                        <li class="breadcrumb-item active">ข้อมูลพนักงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">อัปโหลดข้อมูลพนักงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <!-- file preview template -->
    <div class="d-none" id="uploadPreviewTemplate">
        <div class="card mt-1 mb-0 shadow-none border">
            <div class="p-2">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img data-dz-thumbnail src="#" class="avatar-sm rounded bg-light" alt="">
                    </div>
                    <div class="col ps-0">
                        <a href="javascript:void(0);" class="text-muted fw-bold" data-dz-name></a>
                        <p class="mb-0" data-dz-size></p>
                    </div>
                    <div class="col-auto">
                        <!-- Button -->
                        <a href="" class="btn btn-link btn-lg text-muted" data-dz-remove>
                            <i class="dripicons-cross"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        @if (!$data)
                        <form class="form-horizontal" id="upload-form" action="{{ route('employee.upload-data') }}"
                            method="POST" enctype="multipart/form-data" onsubmit="return SubmitForm(this);">
                            {{ csrf_field() }}
                            <div class="mb-3">
                                <label for="detail" class="form-label">ตัวอย่างไฟล์ฟอร์แมต Excel</label>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered w-100 fs-6">
                                        <tr class="text-center table-secondary">
                                            <th></th>
                                            <th>A</th>
                                            <th>B</th>
                                            <th>C</th>
                                            <th>D</th>
                                            <th>E</th>
                                            <th>F</th>
                                            <th>G</th>
                                            <th>H</th>
                                            <th>I</th>
                                            <th>J</th>
                                            <th>K</th>
                                            <th>L</th>
                                            <th>M</th>
                                            <th>N</th>
                                            <th>O</th>
                                            <th>P</th>
                                        </tr>
                                        <tr>
                                            <th class="table-secondary">1</th>
                                            <th>Emp ID</th>
                                            <th>Name Prefix</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>รหัสหน่วยงาน</th>
                                            <th>Nick Name</th>
                                            <th>ตำแหน่ง</th>
                                            <th>ประเภทพนักงาน</th>
                                            <th>ที่อยู่ตามทะเบียนบ้าน</th>
                                            <th>Gender</th>
                                            <th>Race</th>
                                            <th>Nationality</th>
                                            <th>Religion</th>
                                            <th>Birth Date</th>
                                            <th>IDCard Number</th>
                                            <th>Start Work Date</th>
                                        </tr>
                                        <tr>
                                            <td class="table-secondary">2</td>
                                            <td>123456</td>
                                            <td>นาย</td>
                                            <td>ชื่อจริง</td>
                                            <td>นามสกุล</td>
                                            <td>A00000000</td>
                                            <td></td>
                                            <td></td>
                                            <td>D</td>
                                            <td>90 หมู่ที่ 6 ต.ไหล่หิน อ.เกาะดา จ.ลำปาง</td>
                                            <td>M</td>
                                            <td>ไทย</td>
                                            <td>ไทย</td>
                                            <td>พุทธ</td>
                                            <td>dd/mm/yyyy</td>
                                            <td>0-0000-00000-00-0</td>
                                            <td>dd/mm/yyyy</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <label for="file" class="form-label">ไฟล์อัปเดต (.xls, .xlsx) <span class="text-danger">* อัปเดตข้อมูลได้ครั้งละไม่เกิน 200 รายการ</span></label><br>
                            <input type="file" id="file" name="file" accept=".xls,.xlsx" data-plugins="dropify" data-height="300" data-max-file-size="5M" data-allowed-file-extensions="xls xlsx" />
                            {!! $errors->first('file', '<span class="text-danger">:message</span>') !!}
                            <div class="mt-3 text-center">
                                <button id="loading" name="loading" class="btn btn-primary hidd" type="button" disabled>
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    รอสักครู่...
                                </button>
                                <button type="submit" id="submit" name="submit" class="ladda-button btn btn-primary" dir="ltr" data-style="zoom-out" title="UPLOAD">อัปโหลด</button>
                            </div>
                        </form>
                        @else
                        <hr class="mt-2">
                        <h4 class="text-center">ข้อมูลที่อัปโหลด</h4>
                        <form class="form-horizontal" id="update-form" name="update-form" action="{{ route('employee.update-data') }}"
                            method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div style="overflow-y: auto;">
                            <table data-toggle="table" data-page-size="10" data-buttons-class="xs btn-light"
                            data-pagination="false" class="table table-sm table-bordered w-100 fs-6" data-search="false">
                                <thead class="table-light">
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>Emp ID</th>
                                        <th>Name Prefix</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>รหัสหน่วยงาน</th>
                                        <th>Nick Name</th>
                                        <th>ตำแหน่ง</th>
                                        <th>ประเภทพนักงาน</th>
                                        <th>ที่อยู่ตามทะเบียนบ้าน</th>
                                        <th>Gender</th>
                                        <th>Race</th>
                                        <th>Nationality</th>
                                        <th>Religion</th>
                                        <th>Birth Date</th>
                                        <th>IDCard Number</th>
                                        <th>Start Work Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php
                                    $count = count($data);
                                    $no_data = 0;
                                    if ($count <= 0) {
                                        $no_data = 1;
                                    }
                                    $error = 0;
                                @endphp
                                @for ($i = 0; $i < $count; $i++)
                                    <tr @if ($data[$i]["msg"] != "") class="bg-soft-danger" @endif>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            {{ $data[$i]["emp_id"] }}
                                            @if ($data[$i]["action"] == "emp_distinct")
                                            <br><span class="badge badge-soft-danger">{{ $data[$i]["msg"] }}</span>
                                            @php
                                                $error++;
                                            @endphp
                                            @endif
                                        </td>
                                        <td>{{ $data[$i]["name_prefix"] }}</td>
                                        <td>{{ $data[$i]["first_name"] }}</td>
                                        <td>{{ $data[$i]["last_name"] }}</td>
                                        <td>
                                            {{ $data[$i]["dept_id"] }}
                                            @if ($data[$i]["action"] == "no_dept")
                                            <br><span class="badge badge-soft-danger">{{ $data[$i]["msg"] }}</span>
                                            @php
                                                $error++;
                                            @endphp
                                            @endif
                                        </td>
                                        <td>{{ $data[$i]["nickname"] }}</td>
                                        <td>
                                            {{ $data[$i]["position_id"] }}
                                            @if ($data[$i]["action"] == "no_position")
                                            <br><span class="badge badge-soft-danger">{{ $data[$i]["msg"] }}</span>
                                            @php
                                                $error++;
                                            @endphp
                                            @endif
                                        </td>
                                        <td>{{ $data[$i]["emp_type"] }}</td>
                                        <td>{{ $data[$i]["full_address"] }}</td>
                                        <td>{{ $data[$i]["gender"] }}</td>
                                        <td>{{ $data[$i]["race"] }}</td>
                                        <td>{{ $data[$i]["nationality"] }}</td>
                                        <td>{{ $data[$i]["religion"] }}</td>
                                        <td>{{ $data[$i]["birth_date"] }}</td>
                                        <td>{{ $data[$i]["idcard_number"] }}</td>
                                        <td>{{ $data[$i]["start_work_date"] }}</td>
                                    </tr>
                                @endfor
                                </tbody>
                            </table>
                            </div>
                            @if ($no_data > 0)
                            <h4 class="text-danger">* ไม่พบข้อมูลบางรายการ</h4>
                            @else
                                @if ($error <= 0)
                                <div class="mb-3 text-center">
                                    <button type="submit" name="submit" class="ladda-button btn btn-success mt-3 me-4" dir="ltr" data-style="zoom-out" title="SAVE">บันทึก</button>
                                    <a type="button" href="{{ url('/organization/employees/upload') }}" class="ladda-button btn btn-secondary mt-3" dir="ltr" data-style="zoom-out" title="CANCEL">ยกเลิก</a>
                                </div>
                                @else
                                <div class="mb-3 text-center">
                                    <a type="button" href="{{ url('/organization/employees/upload') }}" class="ladda-button btn btn-secondary mt-3" dir="ltr" data-style="zoom-out" title="CANCEL">ยกเลิก</a>
                                </div>
                                @endif
                            @endif
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
<script src="{{asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
<script src="{{asset('assets/libs/dropify/dropify.min.js')}}"></script>
<script src="{{ asset('assets/libs/ladda/ladda.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/loading-btn.init.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    !function(e){"use strict";var o=function(){this.$body=e("body")};o.prototype.init=function(){Dropzone.autoDiscover=!1,e('[data-plugin="dropzone"]').each((function(){var o=e(this).attr("action"),i=e(this).data("previewsContainer"),r={url:o};i&&(r.previewsContainer=i);var t=e(this).data("uploadPreviewTemplate");t&&(r.previewTemplate=e(t).html()),e(this).dropzone(r)}))},e.FileUpload=new o,e.FileUpload.Constructor=o}(window.jQuery),function(e){"use strict";window.jQuery.FileUpload.init()}(),$('[data-plugins="dropify"]').length>0&&$('[data-plugins="dropify"]').dropify({messages:{default:"Drag and drop a file here or click",replace:"Drag and drop or click to replace",remove:"Remove",error:"Ooops, something wrong appended."},error:{fileSize:"The file size is too big (5M max)."}});
    function SubmitForm(form){
        if (document.getElementById('file').value == "") {
            Swal.fire({
                icon: "warning",
                title: "ยังไม่ได้เลือกไฟล์",
                showConfirmButton: false,
                timer: 2000,
            });
            return false;
        }
        $('#loading').show();
        $('#submit').hide();
    }
</script>
@endsection