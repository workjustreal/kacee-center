@extends('layouts.master-layout', ['page_title' => "จัดการผู้ใช้งาน Line Chatbot"])
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
                        <li class="breadcrumb-item active">Chatbot</li>
                    </ol>
                </div>
                <h4 class="page-title">จัดการผู้ใช้งาน Line Chatbot</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card ribbon-custom-box">
                <div class="card-body">
                    <div class="ribbon-custom ribbon-custom-success ribbon-custom-top-right text-white"><span>LINE</span></div>
                    <div class="card-box">
                        <div class="row mb-2">
                            <div class="col-auto">
                                <div class="mb-2">
                                    <label for="status_chatbot">สถานะ ปิด/เปิด</label>
                                    <select class="form-select" name="status_chatbot" id="status_chatbot">
                                        <option value="" selected>ทั้งหมด</option>
                                        <option value="lock">ปิด</option>
                                        <option value="unlock">เปิด</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="mb-2">
                                    <label for="status_chatbot">ค้นหา</label>
                                    <input type="text" class="form-control" id="search" name="search" autocomplete="off" placeholder="ค้นหา">
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="mb-2">
                                    <br>
                                    <button type="button" class="btn btn-info waves-effect waves-light" onclick="refreshData();">
                                        <span class="btn-label"><i class="mdi mdi-refresh"></i></span>รีเฟรช
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-auto">
                                <div class="mb-2">
                                    <button type="button" class="btn btn-danger me-2 btnChangeStatusSelect" id="btnChangeStatusCloseSelect" disabled><i class="fe-lock"></i> ปิด</button>
                                    <button type="button" class="btn btn-success btnChangeStatusSelect" id="btnChangeStatusOpenSelect" disabled><i class="fe-unlock"></i> เปิด</button>
                                </div>
                            </div>
                        </div>
                        <table id="table" data-toggle="table" data-ajax="ajaxRequest" data-query-params="queryParams" data-search="true" data-page-list="[10]"
                            data-search-align="left" data-pagination="true" data-search-selector="#search" data-page-size="10" class="table">
                            <thead>
                                <tr>
                                    <th data-field="state" data-checkbox="true" data-formatter="stateFormatter"></th>
                                    <th data-field="user_id" data-visible="false">USER ID</th>
                                    <th data-field="status_chatbot" data-visible="false">STATUS</th>
                                    <th data-field="username" data-sortable="true">ชื่อผู้ใช้</th>
                                    <th data-field="created_at" data-sortable="true">วันที่เริ่ม</th>
                                    <th data-field="updated_at" data-sortable="true">วันที่อัพเดท</th>
                                    <th data-field="action" data-sortable="false" data-width="100">ปิด/เปิด</th>
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
    var objData = [];
    $(document).ready(function() {
        $("#status_chatbot").change(function() {
            $table.bootstrapTable('refreshOptions', {
                status_chatbot: $("#status_chatbot").val()
            });
        });
        $("#btnChangeStatusCloseSelect").click(function() {
            updateStatusChatbotSelect('lock');
        });
        $("#btnChangeStatusOpenSelect").click(function() {
            updateStatusChatbotSelect('unlock');
        });
        $table.on('check-all.bs.table', function (e, rowsAfter, rowsBefore) {
            objData = [];
            if (rowsAfter.length > 0) {
                for (var i = 0; i < rowsAfter.length; i++) {
                    objData.push(rowsAfter[i].user_id);
                }
            }
            toggleBtnChangeStatusSelect();
        });
        $table.on('check.bs.table', function (e, row, $element) {
            objData.push(row.user_id);
            toggleBtnChangeStatusSelect();
        });
        $table.on('uncheck-all.bs.table', function (e, rowsAfter, rowsBefore) {
            objData = [];
            toggleBtnChangeStatusSelect();
        });
        $table.on('uncheck.bs.table', function (e, row, $element) {
            if (objData.length > 0) {
                for (var i = 0; i < objData.length; i++) {
                    if (objData[i] === row.user_id) {
                        objData.splice(i, 1);
                    }
                }
            }
            toggleBtnChangeStatusSelect();
        });
    });
    function queryParams(params) {
        params.status_chatbot = $("#status_chatbot").val();
        return params;
    }
    function ajaxRequest(params) {
        var url = "{{ url('chatbot/line_search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function stateFormatter(value, row, index) {
        return value;
    }
    function toggleBtnChangeStatusSelect() {
        $(".btnChangeStatusSelect").prop("disabled", !objData.length);
    }
    function refreshData() {
        $table.bootstrapTable('refreshOptions', {
            status_chatbot: $("#status_chatbot").val()
        });
    }
    function updateStatusChatbot(user_id, value) {
        if (user_id != "" && value != "") {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });
            $.ajax({
                url: "{{ url('chatbot/line_update_status_chatbot') }}",
                method: 'POST',
                dataType: 'json',
                data: {
                    user_id: user_id,
                    value: value,
                },
                success: function(res) {
                    if (res.success == true) {
                        toast('success', res.message);
                    } else {
                        toast('error', res.message);
                    }
                    setTimeout(() => {
                        $table.bootstrapTable('refreshOptions', {
                            status_chatbot: $("#status_chatbot").val()
                        });
                    }, 500);
                }
            });
        }
    }
    function toast(icon, title) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 3000,
        });
        Toast.fire({
            icon: icon,
            title: title
        });
    }
    function updateStatusChatbotSelect(status_chatbot) {
        Swal.fire({
            icon: "warning",
            title: (status_chatbot == 'lock') ? "ยืนยันการปิด ใช่ไหม?" : "ยืนยันการเปิด ใช่ไหม?",
            html: (status_chatbot == 'lock') ? '<i class="fe-lock fs-2 text-danger"></i>' : '<i class="fe-unlock fs-2 text-success"></i>',
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch('/chatbot/line_update_status_chatbot_select', {
                        method: 'POST',
                        headers: {
                            'Content-type': 'application/json; charset=UTF-8',
                            'X-CSRF-TOKEN': '{{csrf_token()}}',
                        },
                        body: JSON.stringify({'status_chatbot': status_chatbot, 'user_id': objData}),
                    })
                    .then(function(response){
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .then(function(data){
                        if (data.success === false) {
                            Swal.fire({
                                icon: "warning",
                                title: data.message,
                            });
                            return false;
                        }
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
                    title: "อัปเดตเรียบร้อย!",
                    timer: 2000,
                });
                objData.shift();
                setTimeout(() => {
                    $table.bootstrapTable('refreshOptions', {
                        status_chatbot: $("#status_chatbot").val()
                    });
                }, 500);
            }
        });
    }
</script>
@endsection