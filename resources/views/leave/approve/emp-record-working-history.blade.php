@extends('layouts.master-layout', ['page_title' => "ประวัติบันทึกวันทำงานของพนักงาน"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Kacee</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Leave</a></li>
                        <li class="breadcrumb-item active">ประวัติบันทึกวันทำงานของพนักงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">ประวัติบันทึกวันทำงานของพนักงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-auto col-sm-12 mb-2">
                            <label for="year" class="form-label mb-0">ปี</label>
                            <div class="form-group">
                                <select class="form-select form-select-sm pt-1" id="year" name="year">
                                    <option value="{{ date('Y')-1 }}">{{ (date('Y')+543)-1 }}</option>
                                    <option value="{{ date('Y') }}" selected>{{ (date('Y')+543) }}</option>
                                    <option value="{{ date('Y')+1 }}">{{ (date('Y')+543)+1 }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12 mb-2">
                            <label for="emp_type" class="form-label mb-0">ประเภทพนักงาน</label>
                            <div class="form-group">
                                <select class="form-select form-select-sm pt-1" id="emp_type" name="emp_type">
                                    <option value="all">ทั้งหมด</option>
                                    <option value="D">รายวัน</option>
                                    <option value="M">รายเดือน</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12 mb-2">
                            <label for="approve_status" class="form-label mb-0">สถานะ</label>
                            <div class="form-group">
                                <select class="form-select form-select-sm pt-1" id="approve_status" name="approve_status">
                                    <option value="all">ทั้งหมด</option>
                                    @foreach ($approve_status as $list)
                                    <option value="{{ $list["id"] }}">{{ $list["name"] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12 mb-2">
                            <label for="use_status" class="form-label mb-0">การใช้งาน</label>
                            <div class="form-group">
                                <select class="form-select form-select-sm pt-1" id="use_status" name="use_status">
                                    <option value="all">ทั้งหมด</option>
                                    <option value="1">ใช้งานแล้ว</option>
                                    <option value="0">ยังไม่ใช้</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12 mb-2">
                            <label for="work_start_date" class="form-label mb-0">วันที่มาทำงาน(เริ่มต้น)</label>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-sm pt-1 report-datepicker" id="work_start_date" name="work_start_date" placeholder="กรอกวันที่">
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12 mb-2">
                            <label for="work_end_date" class="form-label mb-0">วันที่มาทำงาน(สิ้นสุด)</label>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-sm pt-1 report-datepicker" id="work_end_date" name="work_end_date" placeholder="กรอกวันที่">
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12 mb-2">
                            <label for="record_start_date" class="form-label mb-0">วันที่บันทึก(เริ่มต้น)</label>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-sm pt-1 report-datepicker" id="record_start_date" name="record_start_date" placeholder="กรอกวันที่">
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12 mb-2">
                            <label for="record_end_date" class="form-label mb-0">วันที่บันทึก(สิ้นสุด)</label>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-sm pt-1 report-datepicker" id="record_end_date" name="record_end_date" placeholder="กรอกวันที่">
                            </div>
                        </div>
                    </div>
                    <div id="toolbar" class="row">
                        <div class="col-md-auto col-sm-12">
                            <input type="text" class="form-control form-control-sm pt-1" id="search" name="search" autocomplete="off" placeholder="ค้นหา">
                        </div>
                    </div>
                    <table id="table"
                        data-toggle="table"
                        data-pagination="true"
                        data-buttons-class="btn btn-sm btn-secondary"
                        data-toolbar="#toolbar"
                        data-ajax="ajaxRequest"
                        data-query-params="queryParams"
                        data-search="true"
                        data-search-selector="#search"
                        data-custom-search="customSearch"
                        data-undefined-text=""
                        data-show-print="true"
                        data-show-export="true"
                        data-export-data-type="all"
                        data-export-types='["excel"]'
                        data-export-options='{
                            "fileName": "รายงานข้อมูลบันทึกวันทำงาน"
                        }'
                        class="table text-nowrap">
                        <thead>
                            <tr>
                                <th data-field="no" data-sortable="true">ลำดับ</th>
                                <th data-field="emp_id" data-sortable="true">รหัสพนักงาน</th>
                                <th data-field="name" data-sortable="true">ชื่อ-นามสกุล (ผู้ลา)</th>
                                <th data-field="dept_id" data-sortable="true">รหัสหน่วยงาน</th>
                                <th data-field="dept_name" data-sortable="true">ชื่อหน่วยงาน</th>
                                <th data-field="create_date" data-sortable="true" data-sorter="dateSorter">วันที่บันทึก</th>
                                <th data-field="work_date" data-sortable="true" data-sorter="dateSorter">วันที่มาทำงาน</th>
                                <th data-field="use_date" data-sortable="true" data-sorter="dateSorter">วันที่ใช้งาน</th>
                                <th data-field="remark" data-sortable="true">หมายเหตุ</th>
                                <th data-field="status" data-sortable="true">สถานะ</th>
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
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
<script src="{{ asset('assets/js/bootstrap-table-style.js') }}"></script>
<script src="{{asset('assets/libs/bootstrap-table/xlsx.core.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/tableExport.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table-export.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table-print.min.js')}}"></script>
<script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('assets/libs/flatpickr/dist/l10n/th.js')}}"></script>
<script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-table-option.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    var $table = $('#table');
    var objData = [];
    $(document).ready(function() {
        moment.locale("th-TH");
        flatpickr.localize(flatpickr.l10ns.th);
        $(".report-datepicker").flatpickr({
            locale: {
                firstDayOfWeek: 0,
            },
            dateFormat: "d/m/Y",
            disableMobile: true,
            onReady: function (dateObj, dateStr, instance) {
                const $clear = $(
                    '<div class="flatpickr-clear"><button class="btn btn-sm btn-link">Clear</button></div>'
                )
                    .on("click", () => {
                        instance.clear();
                        instance.close();
                    })
                    .appendTo($(instance.calendarContainer));
            },
        });
        setTimeout(() => {
            $table.bootstrapTable('refreshOptions', {year: $("#year").val()});
        }, 500);
        $("#year").change(function() {
            $table.bootstrapTable('refreshOptions', {year: $("#year").val()});
        });
        $("#emp_type").change(function() {
            $table.bootstrapTable('refreshOptions', {emp_type: $("#emp_type").val()});
        });
        $("#approve_status").change(function() {
            $table.bootstrapTable('refreshOptions', {approve_status: $("#approve_status").val()});
        });
        $("#use_status").change(function() {
            $table.bootstrapTable('refreshOptions', {use_status: $("#use_status").val()});
        });
        $("#work_start_date").change(function() {
            $table.bootstrapTable('refreshOptions', {work_start_date: $("#work_start_date").val()});
        });
        $("#work_end_date").change(function() {
            $table.bootstrapTable('refreshOptions', {work_end_date: $("#work_end_date").val()});
        });
        $("#record_start_date").change(function() {
            $table.bootstrapTable('refreshOptions', {record_start_date: $("#record_start_date").val()});
        });
        $("#record_end_date").change(function() {
            $table.bootstrapTable('refreshOptions', {record_end_date: $("#record_end_date").val()});
        });
    });
    function customSearch(data, text) {
        return data.filter(function (row) {
            let chk = 0;
            for (const [key, value] of Object.entries(row)) {
                chk += (value !== null && value !== undefined) ? (value.toString().toLowerCase().replace(/<\/?[^>]+(>|$)/g, "").indexOf(text.toLowerCase()) > -1) ? 1 : 0 : 0;
            }
            if (chk > 0) {
                return true;
            }
            return false;
        });
    }
    function queryParams(params) {
        params.year = $("#year").val();
        params.emp_type = $("#emp_type").val();
        params.approve_status = $("#approve_status").val();
        params.use_status = $("#use_status").val();
        params.work_start_date = $("#work_start_date").val();
        params.work_end_date = $("#work_end_date").val();
        params.record_start_date = $("#record_start_date").val();
        params.record_end_date = $("#record_end_date").val();
        $('button[name="print"]').html('<i class="dripicons-print mt-1"></i>');
        $('div.export > button').html('<i class="dripicons-download mt-1"></i>');
        return params;
    }
    function ajaxRequest(params) {
        var url = "{{ url('leave/approve/emp-record-working-history/search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
</script>
@endsection