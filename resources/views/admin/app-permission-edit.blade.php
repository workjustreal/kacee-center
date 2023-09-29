@extends('layouts.master-layout', ['page_title' => "แก้ไขสิทธิ์ระบบงาน"])
@section('css')
<!-- third party css -->
<link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">แก้ไขสิทธิ์ระบบงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">แก้ไขสิทธิ์ระบบงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form action="{{ route('app-permission.update') }}" class="wow fadeInLeft" method="POST"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{method_field('PUT')}}
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="app_id" class="form-label">ระบบงาน</label>
                                    <input type="hidden" class="form-control" id="app_id" name="app_id" value="{{ $application->id }}">
                                    <input type="text" class="form-control" id="app_name" name="app_name" value="{{ $application->name }}" readonly>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 pt-2">
                                    <label class="header-title">สิทธิ์หน่วยงาน / แผนก</label>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-12 pt-2">
                                            <label class="header-title text-decoration-underline">ส่วนบริหารกลาง</label>
                                            <div class="form-check mb-3 mt-2 form-check-primary">
                                                <input class="form-check-input" type="checkbox" id="dept1_all">
                                                <label class="form-check-label text-primary" for="dept1_all">== เลือกทั้งหมด ==</label>
                                            </div>
                                            @foreach ($dept1 as $list)
                                            @php
                                            if (substr($list->dept_id, 3, 6) == '000000') {
                                                $tab = "";
                                            } else if (substr($list->dept_id, 5, 4) == '0000') {
                                                $tab = "margin-left: 20px;";
                                            } else if (substr($list->dept_id, 7, 2) == '00') {
                                                $tab = "margin-left: 40px;";
                                            } else {
                                                $tab = "margin-left: 60px;";
                                            }
                                            @endphp
                                            <div class="form-check mb-2 form-check-primary" style="{{ $tab }}">
                                                <input class="form-check-input dept1_item" type="checkbox" value="{{ $list->dept_id }}" id="dept1_{{ $loop->index + 1 }}" name="dept[]"
                                                @if ($dept)
                                                    @foreach ($dept as $d1)
                                                        @if ($list->dept_id == $d1->dept_id)
                                                            checked
                                                        @endif
                                                    @endforeach
                                                @endif>
                                                <label class="form-check-label" for="dept1_{{ $loop->index + 1 }}">{{ $list->dept_name }} ({{ $list->dept_id }})</label>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12 pt-2">
                                            <label class="header-title text-decoration-underline">ส่วนขายและการตลาด</label>
                                            <div class="form-check mb-3 mt-2 form-check-primary">
                                                <input class="form-check-input" type="checkbox" id="dept2_all">
                                                <label class="form-check-label text-primary" for="dept2_all">== เลือกทั้งหมด ==</label>
                                            </div>
                                            @foreach ($dept2 as $list)
                                            @php
                                            if (substr($list->dept_id, 3, 6) == '000000') {
                                                $tab = "";
                                            } else if (substr($list->dept_id, 5, 4) == '0000') {
                                                $tab = "margin-left: 20px;";
                                            } else if (substr($list->dept_id, 7, 2) == '00') {
                                                $tab = "margin-left: 40px;";
                                            } else {
                                                $tab = "margin-left: 60px;";
                                            }
                                            @endphp
                                            <div class="form-check mb-2 form-check-primary" style="{{ $tab }}">
                                                <input class="form-check-input dept2_item" type="checkbox" value="{{ $list->dept_id }}" id="dept2_{{ $loop->index + 1 }}" name="dept[]"
                                                @if ($dept)
                                                    @foreach ($dept as $d1)
                                                        @if ($list->dept_id == $d1->dept_id)
                                                            checked
                                                        @endif
                                                    @endforeach
                                                @endif>
                                                <label class="form-check-label" for="dept2_{{ $loop->index + 1 }}">{{ $list->dept_name }} ({{ $list->dept_id }})</label>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12 pt-2">
                                            <label class="header-title text-decoration-underline">ส่วนโรงงาน</label>
                                            <div class="form-check mb-3 mt-2 form-check-primary">
                                                <input class="form-check-input" type="checkbox" id="dept3_all">
                                                <label class="form-check-label text-primary" for="dept3_all">== เลือกทั้งหมด ==</label>
                                            </div>
                                            @foreach ($dept3 as $list)
                                            @php
                                            if (substr($list->dept_id, 3, 6) == '000000') {
                                                $tab = "";
                                            } else if (substr($list->dept_id, 5, 4) == '0000') {
                                                $tab = "margin-left: 20px;";
                                            } else if (substr($list->dept_id, 7, 2) == '00') {
                                                $tab = "margin-left: 40px;";
                                            } else {
                                                $tab = "margin-left: 60px;";
                                            }
                                            @endphp
                                            <div class="form-check mb-2 form-check-primary" style="{{ $tab }}">
                                                <input class="form-check-input dept3_item" type="checkbox" value="{{ $list->dept_id }}" id="dept3_{{ $loop->index + 1 }}" name="dept[]"
                                                @if ($dept)
                                                    @foreach ($dept as $d1)
                                                        @if ($list->dept_id == $d1->dept_id)
                                                            checked
                                                        @endif
                                                    @endforeach
                                                @endif>
                                                <label class="form-check-label" for="dept3_{{ $loop->index + 1 }}">{{ $list->dept_name }} ({{ $list->dept_id }})</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label class="header-title">สิทธิ์รายบุคคล</label>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-12 mb-1">
                                            <input type="text" class="form-control form-control-required" id="emp_id" name="emp_id" placeholder="ค้นหาพนักงาน" autocomplete="off">
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <div class="d-flex justify-content-end">
                                                <input type="text" class="form-control bg-light me-2" id="emp_name" name="emp_name" readonly>
                                                <button type="button" class="btn btn-blue" id="btn-add" disabled>เพิ่ม</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 pt-2">
                                    <table data-toggle="table" data-page-size="10" data-buttons-class="xs btn-light" data-pagination="true"
                                        class="table-bordered" data-search="false">
                                        <thead class="table-light">
                                            <tr>
                                                <th data-field="no" data-sortable="false" data-width="100">ลำดับ</th>
                                                <th data-field="emp_id" data-sortable="true">รหัสพนักงาน</th>
                                                <th data-field="emp_name" data-sortable="true">ชื่อ-นามสกุล</th>
                                                <th data-field="emp_dept" data-sortable="true">หน่วยงาน/แผนก</th>
                                                <th data-field="emp_position" data-sortable="true">ตำแหน่ง</th>
                                                <th data-field="delete" data-sortable="true">ลบ</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-3 mt-3 mb-5">
                                    <input type="hidden" class="form-control" id="session_act" name="session_act" value="edit">
                                    @if ($errors->any())
                                    <a class="btn btn-secondary" href="{{ url('/admin/application/permission') }}">ย้อนกลับ</a>
                                    @else
                                    <button type="button" class="btn btn-secondary" onclick="history.back()">ย้อนกลับ</button>
                                    @endif
                                    <button type="submit" class="btn btn-primary mx-2" id="btn-submit">อัปเดต</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/bootstrap-tables.init.js') }}"></script>
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap3-typeahead.js') }}"></script>
<script src="{{ asset('assets/js/pages/emp.init.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    var session_act = document.getElementById("session_act").value;
    $(document).ready(function() {
        $('[data-toggle="select2"]').select2();

        if ($(".dept1_item").length === $(".dept1_item:checked").length) {
            $("#dept1_all").prop("checked", true);
        } else {
            $("#dept1_all").prop("checked", false);
        }
        $("#dept1_all").click(function(){
            $(".dept1_item").prop('checked', $(this).prop('checked'));
        });
        $(".dept1_item").click(function(){
            if ($(".dept1_item").length === $(".dept1_item:checked").length) {
                $("#dept1_all").prop("checked", true);
            } else {
                $("#dept1_all").prop("checked", false);
            }
        });

        if ($(".dept2_item").length === $(".dept2_item:checked").length) {
            $("#dept2_all").prop("checked", true);
        } else {
            $("#dept2_all").prop("checked", false);
        }
        $("#dept2_all").click(function(){
            $(".dept2_item").prop('checked', $(this).prop('checked'));
        });
        $(".dept2_item").click(function(){
            if ($(".dept2_item").length === $(".dept2_item:checked").length) {
                $("#dept2_all").prop("checked", true);
            } else {
                $("#dept2_all").prop("checked", false);
            }
        });

        if ($(".dept3_item").length === $(".dept3_item:checked").length) {
            $("#dept3_all").prop("checked", true);
        } else {
            $("#dept3_all").prop("checked", false);
        }
        $("#dept3_all").click(function(){
            $(".dept3_item").prop('checked', $(this).prop('checked'));
        });
        $(".dept3_item").click(function(){
            if ($(".dept3_item").length === $(".dept3_item:checked").length) {
                $("#dept3_all").prop("checked", true);
            } else {
                $("#dept3_all").prop("checked", false);
            }
        });
        $('#emp_id').on('keyup focus', function(){
            getEmp($(this), $('#emp_name'));
        });
        $('#emp_id').on('blur', function(){
            getCheckEmp($(this), $('#emp_name'));
        });
        get_data("first");
        $("#btn-add").click(function(){
            if ($("#emp_id").val() == "") {
                Swal.fire({
                    icon: "warning",
                    title: "ยังไม่ได้เลือกพนักงาน",
                    showConfirmButton: false,
                    timer: 2000,
                });
                return false;
            }
            $.ajax({
                url: "{{ url('admin/application/permission/add-user') }}",
                method: 'GET',
                data: {session_act: session_act, app_id: $("#app_id").val(), emp_id: $("#emp_id").val()},
                success: function(res) {
                    if (res.success == true) {
                        get_data();
                    } else {
                        Swal.fire({
                            icon: "warning",
                            title: res.message,
                            showConfirmButton: false,
                            timer: 2000,
                        });
                    }
                    $("#emp_id").val('');
                    $("#emp_name").val('');
                }
            });
        });
    });
    function remove_data(emp_id) {
        $.ajax({
            url: "{{ url('admin/application/permission/remove-user') }}",
            method: 'GET',
            data: {session_act: session_act, app_id: $("#app_id").val(), emp_id: emp_id},
            success: function(res) {
                get_data();
            }
        });
    }
    function get_data(first="") {
        $.ajax({
            url: "{{ url('admin/application/permission/get-users') }}",
            method: 'GET',
            data: {session_act: session_act, app_id: $("#app_id").val(), first:first},
            dataType: 'json',
            success: function(data) {
                $('tbody').html('');
                $('tbody').html(data.table_data);
            }
        });
    }
</script>
@endsection
