@extends('layouts.master-layout', ['page_title' => "งวดค่าแรงประจำปี"])
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
                        <li class="breadcrumb-item active">งวดค่าแรงประจำปี</li>
                    </ol>
                </div>
                <h4 class="page-title">งวดค่าแรงประจำปี</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between mb-2">
                        <div class="col-md-auto col-sm-12">
                            <div class="input-group mb-2">
                                <label class="input-group-text" for="year">ปี</label>
                                <select class="form-select" name="year" id="year">
                                    <option value="{{ date('Y')-1 }}">{{ (date('Y')+543)-1 }}</option>
                                    <option value="{{ date('Y') }}" selected>{{ (date('Y')+543) }}</option>
                                    <option value="{{ date('Y')+1 }}">{{ (date('Y')+543)+1 }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9 col-sm-12">
                            <div class="mb-2 text-sm-end">
                                <a href="{{ url('leave/manage/period-salary/create') }}" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-plus-circle me-1"></i> เพิ่มงวดค่าแรง </a>
                            </div>
                        </div>
                    </div>
                    <table id="table" data-toggle="table" data-ajax="ajaxRequest" data-query-params="queryParams" class="table text-nowrap">
                        <thead>
                            <tr>
                                <th data-field="no" data-sortable="true" data-width="100">ลำดับ</th>
                                <th data-field="year" data-sortable="true">ปี</th>
                                <th data-field="month" data-sortable="true">เดือน</th>
                                <th data-field="start" data-sortable="true">เริ่ม</th>
                                <th data-field="end" data-sortable="true">สิ้นสุด</th>
                                <th data-field="last" data-sortable="true">วันสุดท้ายของการลางาน</th>
                                <th data-field="remark" data-sortable="true">หมายเหตุ</th>
                                <th data-field="action" data-sortable="false" data-width="150">จัดการ</th>
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
    $(document).ready(function() {
        setTimeout(() => {
            $table.bootstrapTable('refreshOptions', {year: $("#year").val()});
        }, 500);
        $("#year").change(function() {
            $table.bootstrapTable('refreshOptions', {
                year: $("#year").val()
            });
        });
    });
    function queryParams(params) {
        params.year = $("#year").val();
        return params;
    }
    function ajaxRequest(params) {
        var url = "{{ url('leave/manage/period-salary/search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function deletePeriodSalaryConfirmation(id) {
        Swal.fire({
            icon: "warning",
            title: "คุณต้องการลบข้อมูลงวดค่าแรง ใช่ไหม?",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการลบ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch(`/leave/manage/period-salary/del/` + id)
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
                    timer: 2000,
                });
                $table.bootstrapTable("refreshOptions", {
                    year: $("#year").val(),
                });
                rebuild();
            }
        });
    }
</script>
@endsection