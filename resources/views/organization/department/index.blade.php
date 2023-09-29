@extends('layouts.master-layout', ['page_title' => "ข้อมูลหน่วยงาน"])
@section('css')
<!-- third party css -->
<link href="{{ asset('assets/css/placeholder-loading.min.css') }}" rel="stylesheet">
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Organization</a></li>
                        <li class="breadcrumb-item active">ข้อมูลหน่วยงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">ข้อมูลหน่วยงาน</h4>
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
                                @if (Auth::User()->manageEmployee())
                                <a href="{{ url('organization/department/create') }}" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-plus-circle me-1"></i> เพิ่มหน่วยงานใหม่ </a>
                                @endif
                                <a href="{{ url('organization/department/export') }}" class="btn btn-soft-secondary waves-effect waves-light">
                                    Export </a>
                            </div>
                        </div>
                    </div>
                    <table id="table" data-toggle="table" data-loading-template="loadingTemplate" data-ajax="ajaxRequest" data-search="true"
                        data-search-align="left" data-pagination="true" data-search-selector="#search" data-page-size="10" class="table">
                        <thead>
                            <tr>
                                <th data-field="dept_id" data-sortable="true" data-width="200">รหัสหน่วยงาน</th>
                                <th data-field="dept_name" data-sortable="true">ชื่อหน่วยงาน</th>
                                <th data-field="dept_name_en" data-sortable="true">ชื่อหน่วยงาน (EN)</th>
                                <th data-field="level" data-sortable="true">ระดับ</th>
                                <th data-field="dept_parent" data-sortable="true">ภายใต้หน่วยงาน</th>
                                @if (Auth::User()->manageEmployee())
                                <th data-field="action" data-sortable="false" data-width="150">จัดการ</th>
                                @endif
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
    function ajaxRequest(params) {
        var url = "{{ route('department.search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
</script>
@endsection