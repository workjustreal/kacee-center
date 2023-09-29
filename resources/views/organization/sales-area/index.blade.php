@extends('layouts.master-layout', ['page_title' => "ข้อมูลพื้นที่การขาย"])
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
                        <li class="breadcrumb-item active">ข้อมูลพื้นที่การขาย</li>
                    </ol>
                </div>
                <h4 class="page-title">ข้อมูลพื้นที่การขาย</h4>
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
                        @if (Auth::User()->manageEmployee())
                        <div class="col-sm-8">
                            <div class="mb-2 text-sm-end">
                                <a href="{{ url('organization/sales-area/create') }}" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-plus-circle me-1"></i> เพิ่มพื้นที่การขายใหม่ </a>
                            </div>
                        </div>
                        @endif
                    </div>
                    <table id="table" data-toggle="table" data-loading-template="loadingTemplate" data-ajax="ajaxRequest" data-search="true"
                        data-search-align="left" data-pagination="true" data-search-selector="#search" data-page-size="10" class="table">
                        <thead>
                            <tr>
                                <th data-field="area_code" data-sortable="true" data-width="100">รหัสพื้นที่การขาย</th>
                                <th data-field="dept_name" data-sortable="true">ฝ่าย</th>
                                <th data-field="area_description" data-sortable="true">รายละเอียด</th>
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
        var url = "{{ route('sales-area.search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function deleteSalesAreaConfirmation(id) {
        Swal.fire({
            title: "คุณต้องการลบ ใช่ไหม?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการลบ!",
            cancelButtonText: "ยกเลิก",
        }).then((willDelete) => {
            if (willDelete.isConfirmed) {
                (window.location.href = "/organization/sales-area/del/" + id),
                    Swal.fire({
                        icon: "success",
                        title: "ลบข้อมูลเรียบร้อย!",
                    });
            }
        });
    }
</script>
@endsection