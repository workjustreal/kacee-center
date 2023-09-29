@extends('layouts.master-layout', ['page_title' => "ประกาศบริษัท"])
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                        <li class="breadcrumb-item active">ประกาศบริษัท</li>
                    </ol>
                </div>
                <h4 class="page-title">ประกาศบริษัท</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between mb-2">
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-md-auto col-sm-12">
                                    <div class="input-group mb-2">
                                        <label class="input-group-text" for="year">ปี</label>
                                        <select class="form-select" name="year" id="year">
                                            <option value="{{ date('Y')-1 }}">{{ date('Y')-1 }}</option>
                                            <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                                            <option value="{{ date('Y')+1 }}">{{ date('Y')+1 }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-auto col-sm-12">
                                    <div class="mb-2">
                                        <input type="text" class="form-control" id="search" name="search" autocomplete="off" placeholder="ค้นหา">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="mb-2 text-sm-end">
                                <a href="{{ url('events/create') }}" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-plus-circle me-1"></i> เพิ่มประกาศบริษัท </a>
                            </div>
                        </div>
                    </div>
                    <table id="table" data-toggle="table" data-ajax="ajaxRequest" data-query-params="queryParams" data-search="true"
                        data-search-align="left" data-pagination="true" data-search-selector="#search" data-page-size="10" class="table">
                        <thead>
                            <tr>
                                <th data-field="no" data-sortable="true" data-width="80">ลำดับ</th>
                                <th data-field="title" data-sortable="true">หัวข้อ</th>
                                <th data-field="start" data-sortable="true">เริ่ม</th>
                                <th data-field="end" data-sortable="true">สิ้นสุด</th>
                                <th data-field="calendar" data-sortable="true">แสดงในปฏิทิน</th>
                                <th data-field="info" data-sortable="true">แจ้งเตือน</th>
                                <th data-field="status" data-sortable="true">สถานะ</th>
                                <th data-field="user" data-sortable="false">ผู้สร้าง</th>
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
<!-- third party js ends -->
<script type="text/javascript">
    var $table = $('#table');
    $(document).ready(function() {
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
        var url = "{{ route('events.search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
</script>
@endsection