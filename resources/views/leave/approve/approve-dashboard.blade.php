@extends('layouts.masterpreloader-layout', ['page_title' => "อนุมัติการลางาน"])
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
                            <li class="breadcrumb-item active">อนุมัติการลางาน</li>
                        </ol>
                    </div>
                    <h4 class="page-title">อนุมัติการลางาน</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xl-3 hvr-bob">
                <div class="widget-rounded-circle card bg-pending">
                    <a href="#boxPending">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="avatar-lg rounded-circle bg-white mt-1">
                                        <i class="fas fa-clock avatar-title icon-pending"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="text-end">
                                        <h1 class="text-white mt-1"><span data-plugin="counterup">{{ $pending->count() + $pendingRecordWorking->count() }}</span></h1>
                                        <h3 class="text-white mb-1 text-truncate">รออนุมัติ</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 hvr-bob">
                <div class="widget-rounded-circle card bg-approved">
                    <a href="#boxApproved">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="avatar-lg rounded-circle bg-white mt-1">
                                        <i class="fas fa-check avatar-title icon-approved"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="text-end">
                                        <h1 class="text-white mt-1"><span data-plugin="counterup">{{ $approved->count() + $approvedRecordWorking->count() }}</span></h1>
                                        <h3 class="text-white mb-1 text-truncate">อนุมัติแล้ว</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 hvr-bob">
                <div class="widget-rounded-circle card bg-completed">
                    <a href="#boxApproved">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="avatar-lg rounded-circle bg-white mt-1">
                                        <i class="fas fa-check-double avatar-title icon-completed"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="text-end">
                                        <h1 class="text-white mt-1"><span data-plugin="counterup">{{ $completed->count() + $completedRecordWorking->count() }}</span></h1>
                                        <h3 class="text-white mb-1 text-truncate">เสร็จสมบูรณ์</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 hvr-bob">
                <div class="widget-rounded-circle card bg-canceled">
                    <a href="#boxCanceled">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="avatar-lg rounded-circle bg-white mt-1">
                                        <i class="fas fa-window-close avatar-title icon-canceled"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="text-end">
                                        <h1 class="text-white mt-1"><span data-plugin="counterup">{{ $canceled->count() + $canceledRecordWorking->count() }}</span></h1>
                                        <h3 class="text-white mb-1 text-truncate">ไม่อนุมัติ</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xl-12 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="page-title" id="boxPending">รออนุมัติ <span class="text-info">(ใบลางาน)</span></h4>
                        <table id="tablePending" data-toggle="table" data-pagination="true" data-ajax="ajaxRequestPending" data-query-params="queryParamsPending" class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th data-field="state" data-checkbox="true" data-formatter="stateFormatter"></th>
                                    <th data-field="leave_id" data-visible="false">ID</th>
                                    <th data-field="leave_user" data-sortable="true">ชื่อผู้ลา</th>
                                    <th data-field="create_date" data-sortable="true">วันบันทึก</th>
                                    <th data-field="leave_date" data-sortable="true">วันที่ลา</th>
                                    <th data-field="leave_amount" data-sortable="true">ว./ชม./น.</th>
                                    <th data-field="leave_type" data-sortable="true">ประเภทการลา</th>
                                    <th data-field="leave_reason" data-sortable="true">เหตุผล</th>
                                    <th data-field="leave_status" data-sortable="true">สถานะ</th>
                                    <th data-field="leave_manage" data-sortable="false">อนุมัติ / ไม่อนุมัติ</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="mt-2">
                            <button type="button" class="btn btn-primary" id="btnApprove" disabled>อนุมัติ</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xl-12 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="page-title" id="boxRecordWorkingPending">รออนุมัติ <span class="text-pink">(บันทึกวันทำงาน)</span></h4>
                        <table id="tableRecordWorkingPending" data-toggle="table" data-pagination="true" data-ajax="ajaxRequestRecordWorkingPending" data-query-params="queryParamsRecordWorkingPending" class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th data-field="state" data-checkbox="true" data-formatter="stateFormatter"></th>
                                    <th data-field="id" data-visible="false">ID</th>
                                    <th data-field="user" data-sortable="true">ชื่อผู้ลา</th>
                                    <th data-field="create_date" data-sortable="true">วันบันทึก</th>
                                    <th data-field="work_date" data-sortable="true">วันทำงาน</th>
                                    <th data-field="remark" data-sortable="true">หมายเหตุ</th>
                                    <th data-field="status" data-sortable="true">สถานะ</th>
                                    <th data-field="manage" data-sortable="false">อนุมัติ / ไม่อนุมัติ</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="mt-2">
                            <button type="button" class="btn btn-primary" id="btnApproveRecordWorking" disabled>อนุมัติ</button>
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
        <div class="row">
            <div class="col-md-12 col-xl-12 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="page-title" id="boxApproved">อนุมัติแล้ว <span class="text-info">(ใบลางาน)</span></h4>
                        <table id="tableApproved" data-toggle="table" data-pagination="true" data-ajax="ajaxRequestApproved" data-query-params="queryParamsApproved" class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th data-field="leave_user" data-sortable="true">ชื่อผู้ลา</th>
                                    <th data-field="create_date" data-sortable="true">วันบันทึก</th>
                                    <th data-field="leave_date" data-sortable="true">วันที่ลา</th>
                                    <th data-field="leave_amount" data-sortable="true">ว./ชม./น.</th>
                                    <th data-field="leave_type" data-sortable="true">ประเภทการลา</th>
                                    <th data-field="leave_reason" data-sortable="true">เหตุผล</th>
                                    <th data-field="leave_status" data-sortable="true">สถานะ</th>
                                    <th data-field="leave_manage" data-sortable="false"></th>
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
                        <h4 class="page-title" id="boxRecordWorkingApproved">อนุมัติแล้ว <span class="text-pink">(บันทึกวันทำงาน)</span></h4>
                        <table id="tableRecordWorkingApproved" data-toggle="table" data-pagination="true" data-ajax="ajaxRequestRecordWorkingApproved" data-query-params="queryParamsRecordWorkingApproved" class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th data-field="user" data-sortable="true">ชื่อผู้ลา</th>
                                    <th data-field="create_date" data-sortable="true">วันบันทึก</th>
                                    <th data-field="work_date" data-sortable="true">วันทำงาน</th>
                                    <th data-field="remark" data-sortable="true">หมายเหตุ</th>
                                    <th data-field="status" data-sortable="true">สถานะ</th>
                                    <th data-field="manage" data-sortable="false"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xl-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="float-end">
                            <button type="button" class="btn btn-soft-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#empLeaveModal"><i class="mdi mdi-plus-circle me-1"></i> ลางานให้พนักงาน</button>
                        </div>
                        <h4 class="page-title">พนักงานภายใต้สิทธิ์การอนุมัติ</h4>
                        <table id="tableUsers" data-toggle="table" data-pagination="true" data-ajax="ajaxRequestUsers" data-query-params="queryParamsUsers" class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th data-field="emp_id" data-sortable="true" data-visible="false">รหัสพนักงาน</th>
                                    <th data-field="emp_name" data-sortable="true">ชื่อ-นามสกุล</th>
                                    <th data-field="emp_dept" data-sortable="true">หน่วยงาน/แผนก</th>
                                    <th data-field="emp_position" data-sortable="true">ตำแหน่ง</th>
                                    <th data-field="emp_type" data-sortable="true">ประเภท</th>
                                    <th data-field="emp_status" data-sortable="true">สถานะ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xl-6 mb-3">
                <div id="boxCanceled" class="card h-100">
                    <div class="card-body">
                        <h4 class="page-title">รายการถูกยกเลิก</h4>
                        <table id="tableCancel" data-toggle="table" data-pagination="true" data-ajax="ajaxRequestCancel" data-query-params="queryParamsCancel" class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th data-field="leave_user" data-sortable="true">ชื่อผู้ลา</th>
                                    <th data-field="leave_date" data-sortable="true">วันที่ลา</th>
                                    <th data-field="leave_amount" data-sortable="true">ว./ชม./น.</th>
                                    <th data-field="leave_type" data-sortable="true">ประเภทการลา</th>
                                    <th data-field="leave_status" data-sortable="true">สถานะ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div id="empLeaveModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="empLeaveModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="empLeaveModalLabel">เลือกพนักงานภายใต้สิทธิ์การอนุมัติ (เฉพาะรายวัน)</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table id="tableUsersModal" data-toggle="table" data-pagination="true" data-search-align="left" data-search="true" data-ajax="ajaxRequestUsersD" data-query-params="queryParamsUsersD" class="table">
                            <thead>
                                <tr>
                                    <th data-field="emp_id" data-sortable="true" data-visible="false">รหัสพนักงาน</th>
                                    <th data-field="emp_name" data-sortable="true" data-formatter="nameFormatterUsersModal">ชื่อ-นามสกุล</th>
                                    <th data-field="emp_dept" data-sortable="true">หน่วยงาน/แผนก</th>
                                    <th data-field="emp_position" data-sortable="true">ตำแหน่ง</th>
                                    <th data-field="emp_type" data-sortable="true">ประเภท</th>
                                    <th data-field="emp_status" data-sortable="true">สถานะ</th>
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
<script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/fullcalendar.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/locale/th.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/toastr.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/calendar-leave-approve.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/bootstrap-tables.init.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-table-style.js') }}"></script>
<script src="{{ asset('assets/js/pages/leave-approve-dashboard.init.js') }}"></script>
@endsection
