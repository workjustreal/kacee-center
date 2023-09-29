@extends('layouts.master-layout', ['page_title' => "ประวัติการลางานของพนักงาน"])
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
                        <li class="breadcrumb-item active">ประวัติการลางานของพนักงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">ประวัติการลางานของพนักงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-auto col-md-auto col-sm-12">
                            <div class="input-group mb-2">
                                <label class="input-group-text" for="level0">บริษัท</label>
                                <select class="form-select" name="level0" id="level0">
                                    <option value="all" selected>ทั้งหมด</option>
                                    @foreach ($level0 as $level)
                                    <option value="{{ $level->dept_id }}">
                                        {{ $level->dept_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-auto col-md-auto col-sm-12">
                            <div class="input-group mb-2">
                                <label class="input-group-text" for="level1">ส่วน</label>
                                <select class="form-select" name="level1" id="level1">
                                    <option value="all" selected>ทั้งหมด</option>
                                    @foreach ($level1 as $level)
                                    <option value="{{ $level->dept_id }}">
                                        {{ $level->dept_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-auto col-md-auto col-sm-12">
                            <div class="input-group mb-2">
                                <label class="input-group-text" for="level2">ฝ่าย</label>
                                <select class="form-select" name="level2" id="level2">
                                    <option value="all" selected>ทั้งหมด</option>
                                    @foreach ($level2 as $level)
                                    <option value="{{ $level->dept_id }}">
                                        {{ $level->dept_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-auto col-md-auto col-sm-12">
                            <div class="input-group mb-2">
                                <label class="input-group-text" for="level3">แผนก</label>
                                <select class="form-select" name="level3" id="level3">
                                    <option value="all" selected>ทั้งหมด</option>
                                    @foreach ($level3 as $level)
                                    <option value="{{ $level->dept_id }}">
                                        {{ $level->dept_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-auto col-md-auto col-sm-12">
                            <div class="input-group mb-2">
                                <label class="input-group-text" for="level4">หน่วยงาน</label>
                                <select class="form-select" name="level4" id="level4">
                                    <option value="all" selected>ทั้งหมด</option>
                                    @foreach ($level4 as $level)
                                    <option value="{{ $level->dept_id }}">
                                        {{ $level->dept_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-auto col-md-auto col-sm-12">
                            <div class="input-group mb-2">
                                <label class="input-group-text" for="area_code">พื้นที่การขาย</label>
                                <select class="form-select" name="area_code" id="area_code">
                                    <option value="" selected>ไม่ระบุ</option>
                                    @foreach ($sales_area as $area)
                                    <option value="{{ $area->area_code }}">
                                        {{ $area->area_code }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-auto col-md-auto col-sm-12">
                            <div class="input-group mb-2">
                                <label class="input-group-text" for="emp_type">ประเภท</label>
                                <select class="form-select" name="emp_type" id="emp_type">
                                    <option value="all" selected>ทั้งหมด</option>
                                    <option value="D">รายวัน</option>
                                    <option value="M">รายเดือน</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-auto col-md-auto col-sm-12">
                            <div class="input-group mb-2">
                                <label class="input-group-text" for="emp_status">สถานะ</label>
                                <select class="form-select" name="emp_status" id="emp_status">
                                    <option value="all" selected>ทั้งหมด</option>
                                    <option value="1">ปกติ</option>
                                    <option value="2">ทดลองงาน</option>
                                    <option value="0">ลาออก</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="exportForm" class="hidd"></div>
                    <div class="row">
                        <div class="col-auto">
                            <div class="mb-2">
                                <input type="text" class="form-control" id="search" name="search" autocomplete="off"
                                    placeholder="ค้นหา">
                            </div>
                        </div>
                    </div>
                    <table id="table" data-toggle="table" data-loading-template="loadingTemplate" data-ajax="ajaxRequest" data-query-params="queryParams" data-search="true"
                        data-search-align="left" data-pagination="true" data-search-selector="#search" data-custom-search="customSearch" data-page-size="10" class="table text-nowrap">
                        <thead>
                            <tr>
                                <th data-field="emp_id" data-sortable="true" data-width="100">รหัสพนักงาน</th>
                                <th data-field="name" data-sortable="true">ชื่อ - นามสกุล</th>
                                <th data-field="level1" data-sortable="true">ส่วน</th>
                                <th data-field="level2" data-sortable="true">ฝ่าย</th>
                                <th data-field="level3" data-sortable="true">แผนก</th>
                                <th data-field="level4" data-sortable="true">หน่วยงาน</th>
                                <th data-field="position" data-sortable="true">ตำแหน่ง</th>
                                <th data-field="emp_type" data-sortable="true">ประเภทพนักงาน</th>
                                <th data-field="emp_status" data-sortable="true">สถานะพนักงาน</th>
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
    var $table = $("#table");
    $(document).ready(function() {
        setTimeout(() => {
            $table.bootstrapTable('refreshOptions', {level0: $("#level0").val()});
        }, 500);
        $("#level0").change(function () {
            $("#level1").empty();
            $("#level1").append("<option value='all'>ทั้งหมด</option>");
            $("#level2").empty();
            $("#level2").append("<option value='all'>ทั้งหมด</option>");
            $("#level3").empty();
            $("#level3").append("<option value='all'>ทั้งหมด</option>");
            $("#level4").empty();
            $("#level4").append("<option value='all'>ทั้งหมด</option>");
            $.ajax({
                url: '{{ route("employee.level1") }}',
                type: "get",
                data: {
                    level0: $("#level0").val(),
                },
                dataType: "json",
                success: function (response) {
                    var len = response.level1.length;
                    for (var i = 0; i < len; i++) {
                        var id = response.level1[i]["dept_id"];
                        var name = response.level1[i]["dept_name"];
                        $("#level1").append(
                            "<option value='" + id + "'>" + name + "</option>"
                        );
                    }
                    var len = response.level2.length;
                    for (var i = 0; i < len; i++) {
                        var id = response.level2[i]["dept_id"];
                        var name = response.level2[i]["dept_name"];
                        $("#level2").append(
                            "<option value='" + id + "'>" + name + "</option>"
                        );
                    }
                    var len = response.level3.length;
                    for (var i = 0; i < len; i++) {
                        var id = response.level3[i]["dept_id"];
                        var name = response.level3[i]["dept_name"];
                        $("#level3").append(
                            "<option value='" + id + "'>" + name + "</option>"
                        );
                    }
                    var len = response.level4.length;
                    for (var i = 0; i < len; i++) {
                        var id = response.level4[i]["dept_id"];
                        var name = response.level4[i]["dept_name"];
                        $("#level4").append(
                            "<option value='" + id + "'>" + name + "</option>"
                        );
                    }
                    $table.bootstrapTable("refreshOptions", {
                        level0: $("#level0").val(),
                    });
                    rebuild();
                },
            });
        });
        $("#level1").change(function () {
            $("#level2").empty();
            $("#level2").append("<option value='all'>ทั้งหมด</option>");
            $("#level3").empty();
            $("#level3").append("<option value='all'>ทั้งหมด</option>");
            $("#level4").empty();
            $("#level4").append("<option value='all'>ทั้งหมด</option>");
            $.ajax({
                url: '{{ route("employee.level2") }}',
                type: "get",
                data: {
                    level1: $("#level1").val(),
                },
                dataType: "json",
                success: function (response) {
                    var len = response.level2.length;
                    for (var i = 0; i < len; i++) {
                        var id = response.level2[i]["dept_id"];
                        var name = response.level2[i]["dept_name"];
                        $("#level2").append(
                            "<option value='" + id + "'>" + name + "</option>"
                        );
                    }
                    var len = response.level3.length;
                    for (var i = 0; i < len; i++) {
                        var id = response.level3[i]["dept_id"];
                        var name = response.level3[i]["dept_name"];
                        $("#level3").append(
                            "<option value='" + id + "'>" + name + "</option>"
                        );
                    }
                    var len = response.level4.length;
                    for (var i = 0; i < len; i++) {
                        var id = response.level4[i]["dept_id"];
                        var name = response.level4[i]["dept_name"];
                        $("#level4").append(
                            "<option value='" + id + "'>" + name + "</option>"
                        );
                    }
                    $table.bootstrapTable("refreshOptions", {
                        level1: $("#level1").val(),
                    });
                    rebuild();
                },
            });
        });
        $("#level2").change(function () {
            $("#level3").empty();
            $("#level3").append("<option value='all'>ทั้งหมด</option>");
            $("#level4").empty();
            $("#level4").append("<option value='all'>ทั้งหมด</option>");
            $("#area_code").empty();
            $("#area_code").append("<option value=''>ไม่ระบุ</option>");
            $.ajax({
                url: '{{ route("employee.level3") }}',
                type: "get",
                data: {
                    level2: $("#level2").val(),
                },
                dataType: "json",
                success: function (response) {
                    var len = response.level3.length;
                    for (var i = 0; i < len; i++) {
                        var id = response.level3[i]["dept_id"];
                        var name = response.level3[i]["dept_name"];
                        $("#level3").append(
                            "<option value='" + id + "'>" + name + "</option>"
                        );
                    }
                    var len = response.level4.length;
                    for (var i = 0; i < len; i++) {
                        var id = response.level4[i]["dept_id"];
                        var name = response.level4[i]["dept_name"];
                        $("#level4").append(
                            "<option value='" + id + "'>" + name + "</option>"
                        );
                    }
                    var len = response.sales_area.length;
                    for (var i = 0; i < len; i++) {
                        var id = response.sales_area[i]["area_code"];
                        var name = response.sales_area[i]["area_code"];
                        $("#area_code").append(
                            "<option value='" + id + "'>" + name + "</option>"
                        );
                    }
                    $table.bootstrapTable("refreshOptions", {
                        level2: $("#level2").val(),
                    });
                    rebuild();
                },
            });
        });
        $("#level3").change(function () {
            $("#level4").empty();
            $("#level4").append("<option value='all'>ทั้งหมด</option>");
            $.ajax({
                url: '{{ route("employee.level4") }}',
                type: "get",
                data: {
                    level3: $("#level3").val(),
                },
                dataType: "json",
                success: function (response) {
                    var len = response.level4.length;
                    for (var i = 0; i < len; i++) {
                        var id = response.level4[i]["dept_id"];
                        var name = response.level4[i]["dept_name"];
                        $("#level4").append(
                            "<option value='" + id + "'>" + name + "</option>"
                        );
                    }
                    $table.bootstrapTable("refreshOptions", {
                        level3: $("#level3").val(),
                    });
                    rebuild();
                },
            });
        });
        $("#level4").change(function () {
            $table.bootstrapTable("refreshOptions", {
                level4: $("#level4").val(),
            });
            rebuild();
        });
        $("#area_code").change(function () {
            $table.bootstrapTable("refreshOptions", {
                area_code: $("#area_code").val(),
            });
            rebuild();
        });
        $("#emp_type").change(function () {
            $table.bootstrapTable("refreshOptions", {
                emp_type: $("#emp_type").val(),
            });
            rebuild();
        });
        $("#emp_status").change(function () {
            $table.bootstrapTable("refreshOptions", {
                emp_status: $("#emp_status").val(),
            });
            rebuild();
        });
        setTimeout(() => {
            $table.bootstrapTable("refresh");
            rebuild();
        }, 500);
    });
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
    function queryParams(params) {
        params.level0 = $("#level0").val();
        params.level1 = $("#level1").val();
        params.level2 = $("#level2").val();
        params.level3 = $("#level3").val();
        params.level4 = $("#level4").val();
        params.area_code = $("#area_code").val();
        params.emp_type = $("#emp_type").val();
        params.emp_status = $("#emp_status").val();
        return params;
    }
    function ajaxRequest(params) {
        var url = '{{ url("leave/manage/emp-attendance-search") }}';
        $.get(url + "?" + $.param(params.data)).then(function (res) {
            params.success(res);
        });
    }
</script>
@endsection