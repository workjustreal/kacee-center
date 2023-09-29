@extends('layouts.master-layout', ['page_title' => "ประวัติบันทึกวันทำงานฝ่ายขาย"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">ประวัติบันทึกวันทำงานฝ่ายขาย</li>
                    </ol>
                </div>
                <h4 class="page-title">ประวัติบันทึกวันทำงานฝ่ายขาย</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-md-auto col-sm-12">
                            <div class="row">
                                <div class="col-md-auto col-sm-12 mb-2">
                                    <label for="year" class="form-label mb-0">ปี</label>
                                    <div class="form-group">
                                        <select class="form-select pt-1" id="year" name="year">
                                            <option value="{{ date('Y')-1 }}">{{ (date('Y')+543)-1 }}</option>
                                            <option value="{{ date('Y') }}" selected>{{ (date('Y')+543) }}</option>
                                            <option value="{{ date('Y')+1 }}">{{ (date('Y')+543)+1 }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-auto col-sm-12 mb-2">
                                    <label for="month" class="form-label mb-0">เดือน</label>
                                    <div class="form-group">
                                        <select class="form-select pt-1" id="month" name="month">
                                            <option value="all">ทั้งหมด</option>
                                            @foreach ($months as $list)
                                            <option value="{{ $list["id"] }}">{{ $list["th"] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-auto col-sm-12 mb-2">
                                    <label for="dept" class="form-label mb-0">ฝ่าย</label>
                                    <div class="form-group">
                                        <select class="form-select pt-1" id="dept" name="dept">
                                            <option value="all">ทั้งหมด</option>
                                            @foreach ($department as $list)
                                            <option value="{{ $list->dept_id }}">{{ $list->dept_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-auto col-sm-12 mb-2">
                                    <label for="search" class="form-label mb-0">ค้นหา</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control pt-1" id="search" name="search" autocomplete="off" placeholder="ค้นหา">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (Auth::User()->isAccessLeaveRacord())
                        <div class="col-md-auto col-sm-12 mb-2 text-sm-end">
                            <label for="search" class="form-label mb-0">&nbsp;</label>
                            <div class="form-group">
                                <a href="{{ url('leave/leave-record-form') }}" class="btn btn-soft-primary waves-effect waves-light">บันทึกวันทำงาน</a>
                            </div>
                        </div>
                        @endif
                    </div>
                    <table id="table"
                        data-toggle="table"
                        data-pagination="true"
                        data-buttons-class="btn btn-sm btn-secondary"
                        data-ajax="ajaxRequest"
                        data-query-params="queryParams"
                        data-search="true"
                        data-search-selector="#search"
                        data-undefined-text=""
                        class="table text-nowrap">
                        <thead>
                            <tr>
                                <th data-field="no" data-sortable="true">ลำดับ</th>
                                <th data-field="year" data-sortable="true">ปี</th>
                                <th data-field="month" data-sortable="true">เดือน</th>
                                <th data-field="dept_name" data-sortable="true">ฝ่าย</th>
                                <th data-field="create_id" data-sortable="true">ผู้บันทึก</th>
                                <th data-field="create_date" data-sortable="true" data-sorter="dateSorter">วันที่บันทึก</th>
                                <th data-field="manage" data-sortable="false">ดูข้อมูล</th>
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
        $("#month").change(function() {
            $table.bootstrapTable('refreshOptions', {month: $("#month").val()});
        });
        $("#dept").change(function() {
            $table.bootstrapTable('refreshOptions', {dept: $("#dept").val()});
        });
    });
    function queryParams(params) {
        params.year = $("#year").val();
        params.month = $("#month").val();
        params.dept = $("#dept").val();
        return params;
    }
    function ajaxRequest(params) {
        var url = "{{ url('leave/leave-record/search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function deleteLeaveRecordConfirmation(id) {
        Swal.fire({
            icon: "warning",
            title: "คุณต้องการลบข้อมูล ใช่ไหม?",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch('/leave/leave-record-destroy', {
                        method: 'POST',
                        headers: {
                            'Content-type': 'application/json; charset=UTF-8',
                            'X-CSRF-TOKEN': '{{csrf_token()}}',
                        },
                        body: JSON.stringify({
                            id: id,
                        }),
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
                    title: "เรียบร้อย!",
                });
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        });
    }
</script>
@endsection