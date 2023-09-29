@extends('layouts.masterpreloader-layout', ['page_title' => "แดชบอร์ดลางาน"])
@section('css')
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/css/leave-dashboard.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right d-block">
                        <form class="d-flex align-items-center mb-3">
                            <div class="input-group input-group-sm">
                                <select class="form-select" id="dash_year" name="dash_year">
                                    <option value="{{ date('Y')-1 }}" @if ($leave_dash['dash_year']==(date('Y')-1)) selected @endif>{{ (date('Y')+543)-1 }}</option>
                                    <option value="{{ date('Y') }}" @if ($leave_dash['dash_year']==(date('Y'))) selected @endif>{{ (date('Y')+543) }}</option>
                                    <option value="{{ date('Y')+1 }}" @if ($leave_dash['dash_year']==(date('Y')+1)) selected @endif>{{ (date('Y')+543)+1 }}</option>
                                </select>
                                <span class="input-group-text bg-secondary border-secondary text-white">
                                    <i class="mdi mdi-calendar-range"></i>
                                </span>
                            </div>
                        </form>
                    </div>
                    <h4 class="page-title">แดชบอร์ดลางาน</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xl-3 mb-3 hvr-bob">
                <div class="card h-100">
                    <a href="#">
                        <div class="card-header bg-leave-private">
                            <div class="row">
                                <div class="col-4">
                                    <div class="avatar-lg rounded-circle bg-white">
                                        <i class="fas fa-user-clock avatar-title icon-leave-private"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div>
                                        <h3 class="text-white mt-3">ลากิจ</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <h2>{{ sprintf('%02d', $private_leave["d"]) }}/<span class="text-muted">{{ sprintf('%02d', $private_total) }} วัน</span></h2>
                                <span class="text-muted">{{ $private_leave["h"] }} ชม. {{ $private_leave["m"] }} น.</span><br>
                                <span class="text-muted">ใช้งานไปแล้ว</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-3 hvr-bob">
                <div class="card h-100">
                    <a href="#">
                        <div class="card-header bg-leave-sick">
                            <div class="row">
                                <div class="col-4">
                                    <div class="avatar-lg rounded-circle bg-white">
                                        <i class="fas fa-head-side-cough avatar-title icon-leave-sick"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div>
                                        <h3 class="text-white mt-3">ลาป่วย</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <h2>{{ sprintf('%02d', $sick_leave["d"]) }}/<span class="text-muted">{{ sprintf('%02d', $sick_total) }} วัน</span></h2>
                                <span class="text-muted">{{ $sick_leave["h"] }} ชม. {{ $sick_leave["m"] }} น.</span><br>
                                <span class="text-muted">ใช้งานไปแล้ว</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-3 hvr-bob">
                <div class="card h-100">
                    <a href="#">
                        <div class="card-header bg-leave-vacation">
                            <div class="row">
                                <div class="col-4">
                                    <div class="avatar-lg rounded-circle bg-white">
                                        <i class="fas fa-umbrella-beach avatar-title icon-leave-vacation"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div>
                                        <h3 class="text-white mt-3">ลาพักร้อน</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <h2>{{ sprintf('%02d', $vacation_leave["d"]) }}/<span class="text-muted">{{ sprintf('%02d', $vacation_total) }} วัน</span></h2>
                                <span class="text-muted">{{ $vacation_leave["h"] }} ชม. {{ $vacation_leave["m"] }} น.</span><br>
                                <span class="text-muted">ใช้งานไปแล้ว</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-3 hvr-bob">
                <div class="card h-100">
                    <a href="#">
                        <div class="card-header bg-leave-unpaid">
                            <div class="row">
                                <div class="col-4">
                                    <div class="avatar-lg rounded-circle bg-white d-flex">
                                        <i class="fas fa-hand-holding-usd avatar-title icon-leave-unpaid"></i>
                                        <i class="fas fa-slash icon-leave-unpaid" style="position: absolute;left: 40px;top: 35px;"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div>
                                        <h3 class="text-white mt-3">ลาไม่รับค่าจ้าง</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <h2>{{ sprintf('%02d', $unpaid_leave["d"]) }}<span class="text-muted"> วัน</span></h2>
                                <span class="text-muted">{{ $unpaid_leave["h"] }} ชม. {{ $unpaid_leave["m"] }} น.</span><br>
                                <span class="text-muted">ใช้งานไปแล้ว</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xl-3 hvr-bob">
                <div class="card">
                    <a href="#">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center">
                                        <h1 class="text-dark mt-1"><span data-plugin="counterup">{{ $urgent_leave_amount }}</span> ครั้ง</h1>
                                        <span class="text-muted mb-1 text-truncate">ลาเร่งด่วน (2 ครั้ง/เดือน)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 hvr-bob">
                <div class="card">
                    <a href="#">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center">
                                        <h1 class="text-dark mt-1">
                                            <span>{{ sprintf('%02d', $compensation_leave["d"]) }}</span>/<span class="text-muted">{{ sprintf('%02d', $record_working_total) }} วัน</span>
                                        </h1>
                                        <span class="text-muted mb-1 text-truncate">ลาหยุดชดเชย</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 hvr-bob">
                <div class="card">
                    <a href="#">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center">
                                        <h1 class="text-dark mt-1">
                                            <span data-plugin="counterup">{{ $other_leave["d"] }}</span> วัน
                                            <span class="fs-3"><span data-plugin="counterup"> {{ $other_leave["h"] }}</span> ชม.</span>
                                            <span class="fs-4"><span data-plugin="counterup"> {{ $other_leave["m"] }}</span> น.</span>
                                        </h1>
                                        <span class="text-muted mb-1 text-truncate">ลาอื่นๆ (บวช,คลอด,ทหาร,แต่งงาน,อบรม)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 hvr-bob">
                <div class="card">
                    <a href="#">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center">
                                        <h1 class="text-dark mt-1"><span data-plugin="counterup">{{ number_format($worked_days["worked_days"], 0) }}</span> วัน</h1>
                                        <span class="text-muted mb-1 text-truncate">ระยะเวลาทำงาน ({{ $worked_days["worked_text"] }})</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xl-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="float-end">
                            <a href="{{ url('leave/form') }}" class="btn btn-soft-primary waves-effect waves-light"><i class="mdi mdi-plus-circle me-1"></i> ลางาน</a>
                        </div>
                        <h4 class="page-title">การลางานล่าสุด</h4>
                        <table id="tableLeave" data-toggle="table" data-pagination="true" data-ajax="ajaxRequestLeave" data-query-params="queryParamsLeave" class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th data-field="leave_date" data-sortable="true">วันที่ลา</th>
                                    <th data-field="leave_amount" data-sortable="true">ว./ชม./น.</th>
                                    <th data-field="leave_type" data-sortable="true">ประเภทการลา</th>
                                    <th data-field="leave_status" data-sortable="true">สถานะ</th>
                                    <th data-field="leave_manage" data-sortable="false"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="card-body d-flex justify-content-end align-items-end pt-0 mt-0">
                        <a href="{{ url('leave/history') }}" class="text-primary card-link"><i class="mdi mdi-history me-1"></i>ดูประวัติ</a>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xl-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="float-end">
                            <a href="{{ url('leave/record-working-form') }}" class="btn btn-soft-primary waves-effect waves-light"><i class="mdi mdi-plus-circle me-1"></i> บันทึกวันทำงาน</a>
                        </div>
                        <h4 class="page-title float-start">บันทึกวันทำงานพิเศษ</h4>
                        <table id="tableRecordWorking" data-toggle="table" data-pagination="true" data-ajax="ajaxRequestRecordWorking" data-query-params="queryParamsRecordWorking" class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th data-field="date" data-sortable="true">วันที่ทำงาน</th>
                                    <th data-field="use_status" data-sortable="true">การใช้งาน</th>
                                    <th data-field="close_status" data-sortable="true">สถานะใช้งาน</th>
                                    <th data-field="approve_status" data-sortable="true">สถานะอนุมัติ</th>
                                    <th data-field="manage" data-sortable="false"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="card-body d-flex justify-content-end align-items-end pt-0 mt-0">
                        <a href="{{ url('leave/rw-history') }}" class="text-primary card-link"><i class="mdi mdi-history me-1"></i>ดูประวัติ</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xl-12 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="float-end">
                            <small class="text-muted">อัปเดต {{ $attendance_latest }}</small>
                        </div>
                        <h4 class="page-title float-start">ประวัติการมาทำงาน (30 วันล่าสุด)</h4>
                        <table id="tableLog" data-toggle="table" data-pagination="true" data-row-style="rowStyleLog" data-ajax="ajaxRequestLog" data-query-params="queryParamsLog" class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th data-field="day" data-sortable="true" data-visible="false">วัน</th>
                                    <th data-field="raw_date" data-sortable="true" data-visible="false">ปี-เดือน-วัน</th>
                                    <th data-field="date" data-sortable="true">วันที่</th>
                                    <th data-field="time1" data-sortable="true">ครั้งที่ 1</th>
                                    <th data-field="time2" data-sortable="true">ครั้งที่ 2</th>
                                    <th data-field="time3" data-sortable="true">ครั้งที่ 3</th>
                                    <th data-field="time4" data-sortable="true">ครั้งที่ 4</th>
                                    <th data-field="time5" data-sortable="true">ครั้งที่ 5</th>
                                    <th data-field="status" data-sortable="true">สถานะ</th>
                                    <th data-field="holiday" data-sortable="true" data-visible="false">วันหยุด</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xl-12 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="page-title">สถิติการลางานประจำงวดนี้ วัน-ชม.-นาที</h4>
                        <table id="tableStatisticPeriod" data-toggle="table" data-pagination="true" data-ajax="ajaxRequestStatisticPeriod" class="table text-nowrap">
                            <thead class="table-warning">
                                <tr>
                                    <th data-field="late_qty" class="text-center">จน.ครั้งที่สาย</th>
                                    <th data-field="late" class="text-center">มาสาย</th>
                                    <th data-field="sick" class="text-center">ป่วย</th>
                                    <th data-field="private" class="text-center">กิจ</th>
                                    <th data-field="absence" class="text-center">ขาดงาน</th>
                                    <th data-field="vacation" class="text-center">พักร้อน</th>
                                    <th data-field="compensation" class="text-center">หยุดชดเชย</th>
                                    <th data-field="urgent" class="text-center">เร่งด่วน</th>
                                    <th data-field="unpaid" class="text-center">ไม่รับค่าจ้าง</th>
                                    <th data-field="maternity" class="text-center">คลอด</th>
                                    <th data-field="ordination" class="text-center">บวช</th>
                                    <th data-field="onsite" class="text-center">ฝึกอบรม</th>
                                    <th data-field="other" class="text-center">อื่นๆ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xl-12 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="page-title">สถิติการลางานสะสม วัน-ชม.-นาที</h4>
                        <table id="tableStatistics" data-toggle="table" data-pagination="true" data-ajax="ajaxRequestStatistics" class="table text-nowrap">
                            <thead class="table-info">
                                <tr>
                                    <th data-field="late_qty" class="text-center">จน.ครั้งที่สาย</th>
                                    <th data-field="late" class="text-center">มาสาย</th>
                                    <th data-field="sick" class="text-center">ป่วย</th>
                                    <th data-field="private" class="text-center">กิจ</th>
                                    <th data-field="absence" class="text-center">ขาดงาน</th>
                                    <th data-field="vacation" class="text-center">พักร้อน</th>
                                    <th data-field="compensation" class="text-center">หยุดชดเชย</th>
                                    <th data-field="urgent" class="text-center">เร่งด่วน</th>
                                    <th data-field="unpaid" class="text-center">ไม่รับค่าจ้าง</th>
                                    <th data-field="maternity" class="text-center">คลอด</th>
                                    <th data-field="ordination" class="text-center">บวช</th>
                                    <th data-field="onsite" class="text-center">ฝึกอบรม</th>
                                    <th data-field="other" class="text-center">อื่นๆ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xl-12 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="page-title">รายละเอียดสถิติการลางานสะสม วัน-ชม.-นาที</h4>
                        <table id="tableStatisticByPeriod" data-toggle="table" data-pagination="true" data-ajax="ajaxRequestStatisticByPeriod" class="table text-nowrap">
                            <thead class="table-danger">
                                <tr>
                                    <th data-field="period" class="text-center">งวดค่าแรง</th>
                                    <th data-field="late" class="text-center">มาสาย</th>
                                    <th data-field="sick" class="text-center">ป่วย</th>
                                    <th data-field="private" class="text-center">กิจ</th>
                                    <th data-field="absence" class="text-center">ขาดงาน</th>
                                    <th data-field="vacation" class="text-center">พักร้อน</th>
                                    <th data-field="compensation" class="text-center">หยุดชดเชย</th>
                                    <th data-field="urgent" class="text-center">เร่งด่วน</th>
                                    <th data-field="unpaid" class="text-center">ไม่รับค่าจ้าง</th>
                                    <th data-field="maternity" class="text-center">คลอด</th>
                                    <th data-field="ordination" class="text-center">บวช</th>
                                    <th data-field="onsite" class="text-center">ฝึกอบรม</th>
                                    <th data-field="other" class="text-center">อื่นๆ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
<script src="{{ asset('assets/js/bootstrap-table-style.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#dash_year").change(function() {
            var url = "{{ url('leave/dash-change') }}";
            $.get(url, { dash_year: $(this).val() }).then(function (res) {
                location.reload()
            });
        });
    });
    function queryParamsLeave(params) {
        return params;
    }
    function ajaxRequestLeave(params) {
        var url = "{{ url('leave/leave/search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function queryParamsRecordWorking(params) {
        return params;
    }
    function ajaxRequestRecordWorking(params) {
        var url = "{{ url('leave/record-working/search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function queryParamsLog(params) {
        return params;
    }
    function ajaxRequestLog(params) {
        var url = "{{ url('leave/attendance-log/search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function rowStyleLog(row, index) {
        if (row.holiday == true) {
            return {
                classes: 'table-light text-muted'
            }
        }
        return {
            classes: ''
        }
    }
    function ajaxRequestStatisticPeriod(params) {
        var url = "{{ url('leave/statistic-period/search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function ajaxRequestStatistics(params) {
        var url = "{{ url('leave/statistics/search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function ajaxRequestStatisticByPeriod(params) {
        var url = "{{ url('leave/statistic-byperiod/search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function deleteLeaveConfirmation(id) {
        Swal.fire({
            icon: "warning",
            title: "คุณต้องการลบการลางาน ใช่ไหม?",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการลบ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch(`/leave/del/` + id)
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .then(function(data){
                        if (data.success === false) {
                            Swal.fire({
                                icon: "warning",
                                title: data.message,
                            });
                            return false;
                        }
                    })
                    .catch((error) => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: "success",
                    title: "ลบข้อมูลเรียบร้อย!",
                });
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        });
    }
    function deleteRecordWorkingConfirmation(id) {
        Swal.fire({
            icon: "warning",
            title: "คุณต้องการลบบันทึกวันทำงานพิเศษ ใช่ไหม?",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการลบ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch(`/leave/record-working-del/` + id)
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .then(function(data){
                        if (data.success === false) {
                            Swal.fire({
                                icon: "warning",
                                title: data.message,
                            });
                            return false;
                        }
                    })
                    .catch((error) => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: "success",
                    title: "ลบข้อมูลเรียบร้อย!",
                });
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        });
    }
</script>
@endsection
