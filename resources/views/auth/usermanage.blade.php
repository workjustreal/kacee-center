@extends('layouts.master-layout', ['page_title' => "จัดการผู้ใช้งาน"])
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
                        <li class="breadcrumb-item active">ผู้ใช้งาน</li>
                    </ol>
                </div>
                <h4 class="page-title">จัดการผู้ใช้งาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <div class="row justify-content-between mb-2">
                            <div class="col-auto">
                                <div class="mb-2">
                                    <input type="text" class="form-control" id="search" name="search" autocomplete="off" placeholder="ค้นหา">
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="mb-2 text-sm-end">
                                    <a href="{{url('admin/register')}}" class="btn btn-dark waves-effect waves-light">สร้างผู้ใช้งาน
                                        &nbsp;<i class="fas fa-user-plus"></i></a>
                                </div>
                            </div>
                        </div>
                        <table id="table" data-toggle="table" data-ajax="ajaxRequest" data-query-params="queryParams" data-search="true"
                            data-search-align="left" data-pagination="true" data-search-selector="#search" data-page-size="10" class="table">
                            <thead>
                                <tr>
                                    <th data-field="name" data-sortable="true">ชื่อ - นามสกุล</th>
                                    <th data-field="emp_id" data-sortable="true">รหัสพนักงาน</th>
                                    <th data-field="dept" data-sortable="true">หน่วยงาน</th>
                                    <th data-field="email" data-sortable="true">อีเมล</th>
                                    <th data-field="level" data-sortable="true">ระดับ</th>
                                    <th data-field="role" data-sortable="true">บทบาท</th>
                                    <th data-field="login" data-sortable="true">ล็อกอิน</th>
                                    <th data-field="verified" data-sortable="true">ยืนยันตัวตน</th>
                                    <th data-field="action" data-sortable="false" data-width="100">จัดการ</th>
                                </tr>
                            </thead>
                        </table>
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
<!-- third party js ends -->
<script type="text/javascript">
    var $table = $('#table');
    function queryParams(params) {
        return params;
    }
    function ajaxRequest(params) {
        var url = "{{ route('user-manage.search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
</script>
@endsection