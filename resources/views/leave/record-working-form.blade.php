@extends('layouts.master-layout', ['page_title' => 'บันทึกวันทำงานพิเศษ'])
@section('css')
    <link href="{{ asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Kacee</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Leave</a></li>
                            <li class="breadcrumb-item active">บันทึกวันทำงานพิเศษ</li>
                        </ol>
                    </div>
                    <h4 class="page-title">บันทึกวันทำงานพิเศษ</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-box">
                            <form action="{{ route('record-working.store') }}" class="wow fadeInLeft" method="POST"
                            enctype="multipart/form-data" onsubmit="return SubmitForm(this);">
                            {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-5">
                                        <h4 class="text-pink">บันทึกข้อมูล</h4>
                                    </div>
                                    <div class="col-7">
                                        <h4 class="text-primary float-end">วันที่: {{ thaidate('d/m/Y') }}</h4>
                                    </div>
                                </div>
                                <div class="p-lg-3">
                                    <div class="row mb-1">
                                        <div class="d-flex align-items-start mb-2 mt-1">
                                            <img class="d-flex me-2 rounded-circle" src="{{url('assets/images/users/thumbnail/'.$user->image)}}" onerror="this.onerror=null;this.src='{{url('assets/images/users/thumbnail/user-1.jpg')}}';" alt="placeholder image" width="32" height="32">
                                            <div class="w-100">
                                                <h6 class="m-0 font-14">{{ $user->name }} {{ $user->surname }} ({{ $user->emp_id }})</h6>
                                                @if ($dept_level)
                                                    @foreach ($dept_level as $list)
                                                        @if ($list->level == 0 && $list->detail->dept_id != "")
                                                        <small class="text-muted">บริษัท: {{ $list->detail->dept_name }}</small>&nbsp;
                                                        @endif
                                                        @if ($list->level == 1 && $list->detail->dept_id != "")
                                                        <small class="text-muted">ส่วน: {{ $list->detail->dept_name }}</small>&nbsp;
                                                        @endif
                                                        @if ($list->level == 2 && $list->detail->dept_id != "")
                                                        <small class="text-muted">ฝ่าย: {{ $list->detail->dept_name }}</small>&nbsp;
                                                        @endif
                                                        @if ($list->level == 3 && $list->detail->dept_id != "")
                                                        <small class="text-muted">แผนก: {{ $list->detail->dept_name }}</small>&nbsp;
                                                        @endif
                                                        @if ($list->level == 4 && $list->detail->dept_id != "")
                                                        <small class="text-muted">หน่วยงาน: {{ $list->detail->dept_name }}</small>&nbsp;
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <small class="text-muted">ตำแหน่ง: @if ($user->position_id!=0) {{ $user->position_name }} @else - @endif</small>&nbsp;
                                                <h6 class="float-end font-13">ระยะเวลาทำงาน: {{ $work_date }}</h6>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5 col-12">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text text-pink bg-soft-pink border-pink" id="text_start">วันที่</span>
                                                <input type="text" class="form-control leave-datepicker" id="date_start" name="date_start"
                                                    placeholder="กรอกวันที่" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-7 col-md-7 col-12">
                                            <div class="row">
                                                <div class="col-lg-9 col-md-7 col-12">
                                                    <div class="input-group mb-3 sum_day">
                                                        <span class="input-group-text text-pink bg-soft-pink border-pink">ถึงวันที่</span>
                                                        <input type="text" class="form-control leave-datepicker"
                                                            id="date_end" name="date_end" placeholder="กรอกวันที่">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-5 col-12">
                                                    <div class="input-group mb-3 sum_day">
                                                        <input type="text" class="form-control" id="sum_day" name="sum_day" readonly>
                                                        <span class="input-group-text text-primary bg-soft-primary border-primary">วัน</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="date_detail"></div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text text-pink bg-soft-pink border-pink" id="remark">หมายเหตุ</span>
                                                <input type="text" class="form-control" id="remark" name="remark"
                                                    placeholder="ระบุหมายเหตุ" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-soft-secondary waves-effect waves-light" onclick="history.back();"><i class="mdi mdi-keyboard-backspace me-1"></i> ย้อนกลับ</button>
                                            <button type="submit" class="btn btn-soft-primary waves-effect waves-light"><i class="mdi mdi-content-save me-1"></i>บันทึกวันทำงาน</button>
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
@endsection
@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-record-working.init.js') }}"></script>
    <!-- third party js ends -->
@endsection