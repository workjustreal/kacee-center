@extends('layouts.master-layout', ['page_title' => 'รายการบาร์โค้ดสินค้าที่สร้าง'])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/inputdate/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->
    <style>
        div.dt-buttons {
            position: relative;
            float: right;
            display: none !important;
        }

        .dataTables_filter>label {
            display: none !important;
        }
    </style>
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
                            <li class="breadcrumb-item active">บาร์โค้ดสินค้า</li>
                        </ol>
                    </div>
                    <h4 class="page-title">รายการบาร์โค้ดสินค้าที่สร้าง</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="shelflayer" class="form-label">ผู้สร้าง</label>
                                <input class="form-control" type="text" placeholder="USERNAME" id="username"
                                    name="username">
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="order_date" class="form-label">วันที่สร้าง (เริ่มต้น)</label>
                                <input type="text" class="form-control custom-datepicker" placeholder="START DATE"
                                    id="date_start" name="date_start" value="">
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="order_date" class="form-label">วันที่สร้าง (สิ้นสุด)</label>
                                <input type="text" class="form-control custom-datepicker" placeholder="END DATE"
                                    id="date_end" name="date_end" value="">
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="search" class="form-label">&nbsp;</label><br>
                                <button type="submit" id="btn-search" name="btn-search"
                                    class="btn btn-dark w-100">ค้นหา</button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <table id="barcodeHistory"
                                    class="display dataTable table table-striped dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>รหัสการสร้าง</th>
                                            <th>หมายเหตุ</th>
                                            <th>ผู้สร้าง</th>
                                            <th>วันที่สร้าง</th>
                                            <th>ดาวน์โหลด</th>
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
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
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/inputdate/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
    <script src="{{ asset('assets/js/inputdate/form-pickers.init.js') }}"></script>
    <!-- third party js ends -->
    <script type="text/javascript">
        $(document).ready(function() {
            get_data();
            $('#date_start, #date_end').on('change', function() {
                get_data();
            });
        });

        function get_data() {
            var isAdmin = "{{ auth()->user()->isAdmin() }}";
            var isUser = "{{ auth()->user()->id }}";
            if ($.fn.dataTable.isDataTable('#barcodeHistory')) {
                var table = $('#barcodeHistory').DataTable();
                table.destroy();
            }
            var res = $.ajax({
                url: "{{ Route('barcode.search') }}",
                method: 'GET',
                data: {
                    username: $("#username").val(),
                    date_start: $("#date_start").val(),
                    date_end: $("#date_end").val(),
                },
                dataType: 'json',
            }).then(function(json, textStatus, jqXHR) {
                var data = [];
                for (var i = 0; i < json.data.length; i++) {
                    date = new Date(json.data[i].created_at);
                    var create_time = date.getDate() + '/' + (date.getMonth() + 1).toString().padStart(2, "0") +
                        '/' + date.getFullYear() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date
                        .getSeconds();
                    data.push([
                        (i + 1).toLocaleString("en-US"),
                        json.data[i].generate_id,
                        json.data[i].remark,
                        json.data[i].fname + ' ' + json.data[i].lname,
                        create_time,
                        `<a href="/product/barcode-export/` + json.data[i].generate_id +
                        `"><i class="fas fa-file-excel text-success"></i> ดาวน์โหลดไฟล์</a>`,
                        (isAdmin || isUser == json.data[i].userid) ? `<a class="action-icon" href="/product/barcode-edit/` + json.data[i].generate_id +
                        `" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>` : ``
                    ]);
                }
                var table = $('#barcodeHistory').DataTable({
                    data: data,
                    deferRender: true,
                    scrollCollapse: true,
                    scroller: true,
                    dom: 'Blfrtip',
                    'lengthMenu': [25, 50, 100],
                    pageLength: 25,
                    "language": {
                        "paginate": {
                            "previous": "<i class='mdi mdi-chevron-left'>",
                            "next": "<i class='mdi mdi-chevron-right'>"
                        },
                        "lengthMenu": "แสดง _MENU_ รายการ",
                        "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                        "infoEmpty": "แสดงทั้งหมด 0 ถึง 0 จาก 0 รายการ",
                        "infoFiltered": "(กรองข้อมูลทั้งหมด _MAX_ รายการ)",
                        "emptyTable": "ไม่มีข้อมูลในตาราง",
                        "zeroRecords": "ไม่พบข้อมูล",
                    },
                    "drawCallback": function() {
                        $('.dataTables_paginate > .pagination').addClass(
                            'pagination-rounded');
                    },
                });
                var timeout = null;
                $('#username').on('keyup', function() {
                    var search = this.value;
                    clearTimeout(timeout);
                    timeout = setTimeout(function() {
                        table.search(search).draw();
                    }, 500);
                });
            });
        }
    </script>
@endsection
