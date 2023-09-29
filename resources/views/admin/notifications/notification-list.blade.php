@extends('layouts.master-layout', ['page_title' => "จัดการการแจ้งเตือน"])
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">การแจ้งเตือน</li>
                    </ol>
                </div>
                <h4 class="page-title">จัดการการแจ้งเตือน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between mb-2">
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-auto">
                                    <div class="input-group mb-2">
                                        <label class="input-group-text" for="app_id">ระบบงาน</label>
                                        <select class="form-select" id="app_id" name="app_id">
                                            <option value="" selected="selected">ทั้งหมด</option>
                                            @foreach ($application as $list)
                                            <option value="{{ $list->id }}">{{ $list->name }}</option>
                                            @endforeach
                                            <option value="23">ร้องขอสติ๊กเกอร์บาร์โค้ด</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="mb-2">
                                        <input type="text" class="form-control" id="search" name="search" autocomplete="off" placeholder="ค้นหา">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-2 text-sm-end">
                                <a href="{{ url('admin/notifications/create') }}" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-plus-circle me-1"></i> เพิ่มสิทธิ์ </a>
                            </div>
                        </div>
                    </div>
                    <table id="table" data-toggle="table" data-pagination="true" data-side-pagination="server" data-search="true" data-search-align="left" data-search-selector="#search" data-ajax="ajaxRequest" data-query-params="queryParams" class="table">
                        <thead>
                            <tr>
                                <th data-field="no" data-sortable="true" data-width="100">ลำดับ</th>
                                <th data-field="app_name" data-sortable="true">ระบบงาน</th>
                                <th data-field="description" data-sortable="true">รายละเอียด</th>
                                <th data-field="from" data-sortable="true">จาก</th>
                                <th data-field="to" data-sortable="true">ถึง</th>
                                <th data-field="job_id" data-sortable="true">ไอดี</th>
                                <th data-field="type" data-sortable="true">ประเภท</th>
                                <th data-field="status" data-sortable="true">สถานะ</th>
                                <th data-field="created_at" data-sortable="true">วันที่</th>
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
        $("#app_id").change(function() {
            $table.bootstrapTable('refreshOptions', {
                app_id: $("#app_id").val()
            });
        });
    });
    function queryParams(params) {
        setTimeout(() => {
            params.app_id = $("#app_id").val();
        }, 200);
        return params;
    }
    function ajaxRequest(params) {
        setTimeout(() => {
            var url = "{{ url('admin/notifications/search') }}";
            $.get(url + '?' + $.param(params.data)).then(function (res) {
                params.success(res)
            });
        }, 500);
    }
    function deleteNotificationConfirmation(id) {
        Swal.fire({
            icon: "warning",
            title: "คุณต้องการลบการแจ้งเตือน ใช่ไหม?",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการลบ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch(`{{ url('admin/notifications/del') }}/` + id)
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
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