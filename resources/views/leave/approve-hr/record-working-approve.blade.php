@extends('layouts.master-layout', ['page_title' => "อนุมัติบันทึกวันทำงานโดยบุคคล"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
<style>
    .bootstrap-table .fixed-table-container.fixed-height:not(.has-footer) {
        border-bottom: none;
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Kacee</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Leave</a></li>
                        <li class="breadcrumb-item active">อนุมัติบันทึกวันทำงานโดยบุคคล</li>
                    </ol>
                </div>
                <h4 class="page-title">อนุมัติบันทึกวันทำงานโดยบุคคล @if ($leave_count > 0)({{ $leave_count }}) @endif</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-auto col-sm-12">
                            <label for="year" class="form-label">ปี</label>
                            <div class="form-group">
                                <select class="form-select" id="year" name="year">
                                    <option value="all" selected>ทั้งหมด</option>
                                    <option value="{{ date('Y')-1 }}">{{ (date('Y')+543)-1 }}</option>
                                    <option value="{{ date('Y') }}">{{ (date('Y')+543) }}</option>
                                    <option value="{{ date('Y')+1 }}">{{ (date('Y')+543)+1 }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12">
                            <label for="emp_type" class="form-label">ประเภทพนักงาน</label>
                            <div class="form-group">
                                <select class="form-select" id="emp_type" name="emp_type">
                                    <option value="all">ทั้งหมด</option>
                                    <option value="D">รายวัน</option>
                                    <option value="M">รายเดือน</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12">
                            <label for="approve_status" class="form-label">สถานะ</label>
                            <div class="form-group">
                                <select class="form-select" id="approve_status" name="approve_status">
                                    @foreach ($approve_status as $list)
                                    @if ($list["id"] == "A2")
                                        <option value="{{ $list["id"] }}">{{ $list["name"] }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12">
                            <label for="level2" class="form-label">ฝ่าย</label>
                            <div class="form-group">
                                <select class="form-select" name="level2" id="level2">
                                    <option value="all" selected>ทั้งหมด</option>
                                    @foreach ($level2 as $level)
                                    <option value="{{ $level->dept_id }}">
                                        {{ $level->dept_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12">
                            <label for="level3" class="form-label">แผนก</label>
                            <div class="form-group">
                                <select class="form-select" name="level3" id="level3">
                                    <option value="all" selected>ทั้งหมด</option>
                                    @foreach ($level3 as $level)
                                    <option value="{{ $level->dept_id }}">
                                        {{ $level->dept_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2 mt-3">
                        <div class="col-md-auto col-sm-12">
                            <input type="text" class="form-control" id="search" name="search" autocomplete="off" placeholder="ค้นหา">
                        </div>
                    </div>
                    <table id="table" data-toggle="table" data-height="500" data-ajax="ajaxRequest" data-query-params="queryParams" data-search="true" data-search-selector="#search" data-custom-search="customSearch" class="table text-nowrap">
                        <thead>
                            <tr>
                                <th data-field="state" data-checkbox="true" data-formatter="stateFormatter"></th>
                                <th data-field="id" data-visible="false">ID</th>
                                <th data-field="approve_status" data-visible="false">STATUS</th>
                                <th data-field="no" data-sortable="true">ลำดับ</th>
                                <th data-field="emp_id" data-sortable="true">รหัสพนักงาน</th>
                                <th data-field="name" data-sortable="true">ชื่อ-นามสกุล (ผู้ลา)</th>
                                <th data-field="dept" data-sortable="true">แผนก/หน่วยงาน</th>
                                <th data-field="create_date" data-sortable="true" data-sorter="dateSorter">วันที่บันทึก</th>
                                <th data-field="work_date" data-sortable="true" data-sorter="dateSorter">วันที่มาทำงาน</th>
                                <th data-field="remark" data-sortable="true">หมายเหตุ</th>
                                <th data-field="status" data-sortable="true">สถานะ</th>
                                <th data-field="manage">จัดการ</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="mt-4 mb-1 pt-4">
                        <button type="button" class="btn btn-primary" id="btnApprove" disabled>อนุมัติ</button>
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
<script src="{{ asset('assets/js/bootstrap-table-style.js') }}"></script>
<script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
<script src="{{ asset('assets/js/pages/manage-leave.init.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-table-option.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    var $table = $('#table');
    var objData = [];
    $(document).ready(function() {
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
        $("#level2").change(function () {
            $("#level3").empty();
            $("#level3").append("<option value='all'>ทั้งหมด</option>");
            $.ajax({
                url: '{{ route("employee.level3") }}',
                type: "get",
                data: {
                    level2: $("#level2").val(),
                },
                dataType: "json",
                success: function (response) {
                    var len = response.level3.length;
                    for (var i = 0; i < len; i++) {
                        var id = response.level3[i]["dept_id"];
                        var name = response.level3[i]["dept_name"];
                        $("#level3").append(
                            "<option value='" + id + "'>" + name + "</option>"
                        );
                    }
                    $table.bootstrapTable("refreshOptions", {
                        level2: $("#level2").val(),
                    });
                    rebuild();
                },
            });
        });
        $("#level3").change(function() {
            $table.bootstrapTable('refreshOptions', {level3: $("#level3").val()});
        });
        $("#btnApprove").click(function() {
            approveHRRecordWorkingConfirmation();
        });
        $table.on('check-all.bs.table', function (e, rowsAfter, rowsBefore) {
            objData = [];
            if (rowsAfter.length > 0) {
                for (var i = 0; i < rowsAfter.length; i++) {
                    objData.push(rowsAfter[i].id);
                }
            }
            toggleBtnApprove();
        });
        $table.on('check.bs.table', function (e, row, $element) {
            objData.push(row.id);
            toggleBtnApprove();
        });
        $table.on('uncheck-all.bs.table', function (e, rowsAfter, rowsBefore) {
            objData = [];
            toggleBtnApprove();
        });
        $table.on('uncheck.bs.table', function (e, row, $element) {
            if (objData.length > 0) {
                for (var i = 0; i < objData.length; i++) {
                    if (objData[i] === row.id) {
                        objData.splice(i, 1);
                    }
                }
            }
            toggleBtnApprove();
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
        params.level2 = $("#level2").val();
        params.level3 = $("#level3").val();
        return params;
    }
    function ajaxRequest(params) {
        var url = "{{ url('leave/approve-hr/record-working-approve/search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function stateFormatter(value, row, index) {
        if (row.approve_status !== 'A2') {
            return {
                disabled: true
            }
        }
        return value;
    }
    function toggleBtnApprove() {
        $("#btnApprove").prop("disabled", !objData.length);
    }
    function approveHRRecordWorkingConfirmation() {
        console.log(objData);
        Swal.fire({
            icon: "warning",
            title: "ยืนยันอนุมัติบันทึกวันทำงาน ใช่ไหม?",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch('/leave/approve-hr/record-working-approve/submit', {
                        method: 'POST',
                        headers: {
                            'Content-type': 'application/json; charset=UTF-8',
                            'X-CSRF-TOKEN': '{{csrf_token()}}',
                        },
                        body: JSON.stringify({'id': objData}),
                    })
                    .then(function(response){
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
                    title: "อนุมัติบันทึกวันทำงานเรียบร้อย!",
                    timer: 2000,
                });
                objData.shift();
                $table.bootstrapTable("refresh");
                rebuild();
            }
        });
    }
</script>
@endsection