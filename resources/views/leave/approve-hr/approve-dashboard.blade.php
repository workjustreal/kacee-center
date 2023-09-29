@extends('layouts.masterpreloader-layout', ['page_title' => "ปฏิทินการลางานของพนักงาน"])
@section('css')
<link href="{{ asset('assets/css/calendar/fullcalendar.min.css') }} " rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/calendar/toastr.min.css') }} " rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/css/approve-dashboard.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Kacee</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Leave</a></li>
                            <li class="breadcrumb-item active">ปฏิทินการลางานของพนักงาน</li>
                        </ol>
                    </div>
                    <h4 class="page-title">ปฏิทินการลางานของพนักงาน</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-auto col-sm-12 d-flex align-items-baseline">
                                <label for="dept" class="form-label me-2">ฝ่าย</label>
                                <div class="form-group">
                                    <select class="form-select" name="dept" id="dept">
                                        <option value="all" selected>==ทั้งหมด==</option>
                                        @foreach ($level1 as $level)
                                        <option value="{{ $level->dept_id }}">
                                            --{{ $level->dept_name }}
                                        </option>
                                        @endforeach
                                        @foreach ($level2 as $level)
                                        <option value="{{ $level->dept_id }}">
                                            ----{{ $level->dept_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xl-12 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/fullcalendar.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/locale/th.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/toastr.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/calendar-leave-approve-hr.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/bootstrap-tables.init.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-table-style.js') }}"></script>
@endsection
