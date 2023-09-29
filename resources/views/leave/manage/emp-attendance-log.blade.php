@extends('layouts.master-layout', ['page_title' => "ประวัติการมาทำงานของพนักงาน"])
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
                    <div class="float-end">
                        <small class="text-muted">อัปเดต {{ $attendance_latest }}</small>
                    </div>
                    <h4 class="header-title">30 วันล่าสุด</h4>
                    <div class="row justify-content-between mt-3 mb-2">
                        <div class="col-md-auto col-sm-12">
                            <input type="text" class="form-control" id="search" name="search" value="{{ $emp_id }}" autocomplete="off" placeholder="ค้นหาพนักงาน">
                        </div>
                        <div class="col-md-4 col-sm-12 text-end">
                            <div class="emp-detail">
                                <div class="p-2">
                                    <p class="m-0">รหัสพนักงาน: {{ $result->emp_id }}</p>
                                    <p class="m-0">ชื่อ-นามสกุล: {{ $result->name }} {{ $result->surname }} @if ($result->nickname!="") ({{ $result->nickname }})@endif</p>
                                    <p class="m-0">แผนก/หน่วยงาน: {{ $result->dept_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-nowrap">
                            <thead>
                                <tr>
                                    <th class="text-center">วัน</th>
                                    <th class="text-center">วันที่</th>
                                    <th class="text-center">เวลาเข้า-ออก</th>
                                    <th class="text-center">สาย</th>
                                    <th class="text-center">ป่วย</th>
                                    <th class="text-center">กิจ</th>
                                    <th class="text-center">ขาดงาน</th>
                                    <th class="text-center">พักร้อน</th>
                                    <th class="text-center">หยุดชดเชย</th>
                                    <th class="text-center">คลอด/บวช</th>
                                    <th class="text-center">เร่งด่วน</th>
                                    <th class="text-center">ไม่รับค่าจ้าง</th>
                                    <th class="text-center">อื่นๆ</th>
                                    <th>หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
<script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
<script src="{{ asset('assets/js/bootstrap3-typeahead.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
$(document).ready(function() {
    var base_url = window.location.protocol + "//" + window.location.host;
        var $myTypeahead = $("#search");
        $myTypeahead.typeahead({
            minLength: 1,
            items: 10,
            showHintOnFocus: "all",
            selectOnBlur: false,
            autoSelect: true,
            displayText: function (item) {
                var userimg = "{{ url('assets/images/users/thumbnail/') }}/" + item.image;
                var errimg = "{{ url('assets/images/users/thumbnail/user-1.jpg') }}";
                html = '<div class="row">';
                    html += '<div class="col-md-2">';
                    html += '<img class="me-2 rounded-circle" src="' + userimg + '" onerror="this.onerror=null;this.src=\'' + errimg + '\';" width="35" height="35" />';
                    html += '</div>';
                    html += '<div class="col-md-10">';
                        html += '<span class="m-0">' + item.name + ' ' + item.surname + ' <span>(' + item.emp_id + ')</span></span>';
                        html += '<p class="m-0"><small>(' + item.dept_name + ')</small></p>';
                        html += '</div>';
                    html += '</div>';
                return html;
            },
            afterSelect: function (item) {
                this.$element[0].value = item.emp_id;
                $("#search").val(item.emp_id);
                html = '<div class="p-2">';
                html += '<p class="m-0">รหัสพนักงาน: ' + item.emp_id + '</p>';
                html += '<p class="m-0">ชื่อ-นามสกุล: ' + item.name + ' ' + item.surname + '</p>';
                html += '<p class="m-0">แผนก/หน่วยงาน: ' + item.dept_name + '</p>';
                html += '</div>';
                $(".emp-detail").html(html);
                get_data(item.emp_id);
            },
            source: function (search, process) {
                if (search == "") {
                    $(".emp-detail").html("");
                    get_data();
                }
                return $.get(
                    base_url + "/leave/manage/search-emp",
                    { search: search },
                    function (data) {
                        return process(data);
                    }
                );
            },
        });
    setTimeout(() => {
        get_data($("#search").val());
    }, 500);
});
function get_data(search="") {
    $.ajax({
        url: "{{ url('leave/manage/emp-attendance-log/search') }}",
        method: 'GET',
        data: {search: search},
        dataType: 'json',
        success: function(data) {
            $('tbody').html('');
            $('tbody').html(data.table_data);
        }
    });
}
</script>
@endsection