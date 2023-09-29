@extends('layouts.master-layout', ['page_title' => "ประเภทการลางาน"])
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
                        <li class="breadcrumb-item active">ประเภทการลางาน</li>
                    </ol>
                </div>
                <h4 class="page-title">ประเภทการลางาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between mb-2">
                        <div class="col-auto">
                            <div class="mb-2">
                                <input type="text" class="form-control" id="search" name="search" autocomplete="off" placeholder="ค้นหา">
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="mb-2 text-sm-end">
                                <a href="{{ url('leave/manage/leave-type/create') }}" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-plus-circle me-1"></i> เพิ่มประเภทการลางาน </a>
                            </div>
                        </div>
                    </div>
                    <table id="table" data-toggle="table" data-search="true" data-search-align="left" data-search-selector="#search" data-ajax="ajaxRequest" data-query-params="queryParams" class="table text-nowrap">
                        <thead>
                            <tr>
                                <th data-field="id" data-sortable="true" data-width="100">รหัส</th>
                                <th data-field="name" data-sortable="true">ชื่อ</th>
                                <th data-field="detail" data-sortable="true">รายละเอียด</th>
                                <th data-field="monthly" data-sortable="true">สิทธิ์รายเดือน</th>
                                <th data-field="daily" data-sortable="true">สิทธิ์รายวัน</th>
                                <th data-field="status" data-sortable="true">สถานะ</th>
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
    function queryParams(params) {
        return params;
    }
    function ajaxRequest(params) {
        var url = "{{ url('leave/manage/leave-type/search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function deleteLeaveTypeConfirmation(id) {
        Swal.fire({
            icon: "warning",
            title: "คุณต้องการลบประเภทการลางาน ใช่ไหม?",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการลบ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch(`/leave/manage/leave-type/del/` + id)
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
                $table.bootstrapTable("refresh");
                rebuild();
            }
        });
    }
</script>
@endsection