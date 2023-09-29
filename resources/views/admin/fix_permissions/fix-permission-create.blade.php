@extends('layouts.master-layout', ['page_title' => "เพิ่มสิทธิ์การใช้งานเฉพาะทาง"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">สิทธิ์การใช้งานเฉพาะทาง</li>
                    </ol>
                </div>
                <h4 class="page-title">เพิ่มสิทธิ์การใช้งานเฉพาะทาง</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" action="{{ route('fix-permission.store') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="mb-3">
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
                            <div class="mb-3">
                                <label for="permission" class="form-label">ชื่อสิทธิ์</label> <span class="text-danger">* เช่น <code>user-list</code> , <code>user-create</code></span>
                                <input id="permission" name="permission" type="text" class="form-control" value="{{ old('permission') }}"
                                    placeholder="ชื่อสิทธิ์" autocomplete="off" required />
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">รายละเอียดสิทธิ์</label>
                                <textarea class="form-control" id="description" placeholder="รายละเอียดสิทธิ์"
                                    name="description" rows="4" required>{{ old('description') }}</textarea>
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
                            <div class="mb-3 d-flex justify-content-between">
                                <input id="permission_id" name="permission_id" type="hidden" class="form-control" value="0" />
                                <input type="hidden" class="form-control" id="session_act" name="session_act" value="create">
                                <a href="{{ url('admin/fix-permissions') }}" class="btn btn-secondary mt-3"> ย้อนกลับ</a>
                                <button type="submit" class="btn btn-primary mt-3"> บันทึก</button>
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
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
<script src="{{ asset('assets/js/bootstrap3-typeahead.js') }}"></script>
<script src="{{ asset('assets/js/pages/emp.init.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    var session_act = document.getElementById("session_act").value;
    $(document).ready(function() {
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
                url: "{{ url('admin/fix-permissions/add-user') }}",
                method: 'GET',
                data: {session_act: session_act, permission_id: $("#permission_id").val(), emp_id: $("#emp_id").val()},
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
            url: "{{ url('admin/fix-permissions/remove-user') }}",
            method: 'GET',
            data: {session_act: session_act, permission_id: $("#permission_id").val(), emp_id: emp_id},
            success: function(res) {
                get_data();
            }
        });
    }
    function get_data(first="") {
        $.ajax({
            url: "{{ url('admin/fix-permissions/get-users') }}",
            method: 'GET',
            data: {session_act: session_act, permission_id: $("#permission_id").val(), first:first},
            dataType: 'json',
            success: function(data) {
                $('tbody').html('');
                $('tbody').html(data.table_data);
            }
        });
    }
    function deleteFixPermissionConfirmation(id) {
        Swal.fire({
            icon: "warning",
            title: "คุณต้องการลบสิทธิ์การใช้งานเฉพาะทาง ใช่ไหม?",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการลบ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch(base_url + `/admin/fix-permissions/del/` + id)
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