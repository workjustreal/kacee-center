@extends('layouts.master-layout', ['page_title' => "ประวัติการมาทำงานของพนักงาน"])
@section('css')
<!-- third party css -->
<link href="{{ asset('assets/css/placeholder-loading.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">ประวัติการมาทำงานของพนักงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">ประวัติการมาทำงานของพนักงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-auto">
                            <div class="mb-2">
                                <input type="text" class="form-control" id="search" name="search" autocomplete="off" placeholder="ค้นหา">
                            </div>
                        </div>
                    </div>
                    <table id="table" data-toggle="table" data-loading-template="loadingTemplate" data-ajax="ajaxRequest" data-query-params="queryParams" data-search="true"
                        data-search-align="left" data-pagination="true" data-search-selector="#search" data-custom-search="customSearch" data-page-size="10" class="table text-nowrap">
                        <thead>
                            <tr>
                                <th data-field="no" data-sortable="true">ลำดับ</th>
                                <th data-field="emp_id" data-sortable="true">รหัสพนักงาน</th>
                                <th data-field="emp_name" data-sortable="true">ชื่อ-นามสกุล</th>
                                <th data-field="emp_level1" data-sortable="true">ส่วน</th>
                                <th data-field="emp_level2" data-sortable="true">ฝ่าย</th>
                                <th data-field="emp_level3" data-sortable="true">แผนก</th>
                                <th data-field="emp_level4" data-sortable="true">หน่วยงาน</th>
                                <th data-field="emp_position" data-sortable="true">ตำแหน่ง</th>
                                <th data-field="emp_type" data-sortable="true">ประเภท</th>
                                <th data-field="emp_status" data-sortable="true">สถานะ</th>
                                <th data-field="action" data-sortable="true">จัดการ</th>
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
    function ajaxRequest(params) {
        var url = "{{ url('leave/approve/emp-attendance-search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
</script>
@endsection