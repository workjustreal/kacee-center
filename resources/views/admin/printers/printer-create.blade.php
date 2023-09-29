@extends('layouts.master-layout', ['page_title' => "เพิ่มเครื่องพิมพ์"])
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
                        <li class="breadcrumb-item active">เครื่องพิมพ์</li>
                    </ol>
                </div>
                <h4 class="page-title">เพิ่มเครื่องพิมพ์</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" action="{{ route('printer.store') }}" method="POST" enctype="multipart/form-data">
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
                                <label for="name" class="form-label">ชื่อเครื่องพิมพ์</label>
                                <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}"
                                    placeholder="ชื่อเครื่องพิมพ์" autocomplete="off" required />
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">รายละเอียด</label>
                                <textarea class="form-control" id="description" placeholder="รายละเอียด"
                                    name="description" rows="4" required>{{ old('description') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">ประเภท</label>
                                <select class="form-select form-control-required" id="type" name="type" required>
                                    <option value="" selected="selected" disabled>-</option>
                                    <option value="label">เครื่องพิมพ์ Label</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="client_ip" class="form-label">ไอพีเครื่อง PC</label>
                                <input id="client_ip" name="client_ip" type="text" class="form-control" value="{{ old('client_ip') }}"
                                    placeholder="ไอพีเครื่อง PC" autocomplete="off" />
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">ผู้ใช้งาน</label><br>
                                <div class="radio radio-success form-check-inline ml-2">
                                    <input type="radio" id="role1" value="1" name="role" checked {{ (old('role')==1) ? 'checked' : '' }} required>
                                    <label for="role1">Admin </label>
                                </div>
                                <div class="radio radio-success form-check-inline">
                                    <input type="radio" id="role2" value="2" name="role" {{ (old('role')==2) ? 'checked' : '' }}>
                                    <label for="role2">User </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">สถานะ</label><br>
                                <div class="radio radio-success form-check-inline ml-2">
                                    <input type="radio" id="status1" value="1" name="status" checked {{ (old('status')==1) ? 'checked' : '' }} required>
                                    <label for="status1">ใช้งาน </label>
                                </div>
                                <div class="radio form-check-inline">
                                    <input type="radio" id="status2" value="0" name="status" {{ (old('status')==0) ? 'checked' : '' }}>
                                    <label for="status2">ไม่ใช้งาน </label>
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
                            <div class="mb-3 d-flex justify-content-between">
                                <input type="hidden" id="user_permission" name="user_permission" class="form-control" value="" />
                                <a href="{{ url('admin/printers') }}" class="btn btn-secondary mt-3"> ย้อนกลับ</a>
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
    var userObj = [];
    $(document).ready(function() {
        $('#emp_id').on('keyup focus', function(){
            getEmp($(this), $('#emp_name'));
        });
        $('#emp_id').on('blur', function(){
            getCheckEmp($(this), $('#emp_name'));
        });
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
                url: "{{ url('admin/printers/add-user') }}",
                method: 'GET',
                data: {emp_id: $("#emp_id").val()},
                success: function(res) {
                    if (res.success == true) {
                        userObj.push(res.data);
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
        if (userObj.length > 0) {
            for (var i = 0; i < userObj.length; i++) {
                if (userObj[i].emp_id === emp_id) {
                    userObj.splice(i, 1);
                }
            }
        }
        get_data();
    }
    function get_data() {
        var html = '';
        var users = '';
        $('tbody').html('');
        if (userObj.length > 0) {
            for (let i=0; i<userObj.length; i++) {
                users += (users!='') ? ',' : '';
                users += userObj[i].emp_id;
                html += '<tr>\
                        <td class="lh35">' + (i + 1) + '</td>\
                        <td class="lh35">' + userObj[i].emp_id + '</td>\
                        <td class="lh35">' + userObj[i].emp_name + '</td>\
                        <td class="lh35">' + userObj[i].emp_dept + '</td>\
                        <td class="lh35">' + userObj[i].emp_position + '</td>\
                        <td class="lh35"><a class="action-icon" href="javascript:void(0);" onclick="remove_data(\''+userObj[i].emp_id+'\')" title="ลบ"><i class="mdi mdi-delete"></i></a></td>\
                        </tr>';
            }
        } else {
            html += ' <tr> <td align="center" colspan="6"> ไม่พบข้อมูล </td> </tr> ';
        }
        $('tbody').html(html);
        $("#user_permission").val(users);
    }
</script>
@endsection