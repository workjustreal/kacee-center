@extends('layouts.master-layout', ['page_title' => "รายงานคำสั่งซื้อ"])
@section('css')
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/inputdate/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/confirmDate/confirmDate.min.css') }}" rel="stylesheet" />
<style>
    /* .cursor-default {cursor: default;}
    .cursor-pointer {cursor: pointer;} */
    .fixed-table-toolbar {
        height: 60px;
    }
    .bootstrap-table .fixed-table-container {
        position: relative;
        clear: none;
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Middleware</a></li>
                        <li class="breadcrumb-item active">คำสั่งซื้อออนไลน์</li>
                    </ol>
                </div>
                <h4 class="page-title">รายงานคำสั่งซื้อ</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <!-- Left sidebar -->
                    <div class="inbox-leftbar">

                        <div class="mb-2">
                            <label for="shop_id" class="form-label">เลือกร้านค้า</label>
                            <select class="form-select" id="shop_id" name="shop_id" required>
                                <option value="all" selected>ทั้งหมด</option>
                                @foreach ($eshop as $list)
                                <option value="{{ $list->id }}">{{ $list->platform_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="order_date" class="form-label">วันที่สั่งซื้อ (เริ่มต้น)</label>
                            <input type="text" class="form-control" placeholder="ORDER DATE"
                                id="order_date_start" name="order_date_start" required>
                        </div>
                        <div class="mb-2">
                            <label for="order_date" class="form-label">วันที่สั่งซื้อ (สิ้นสุด)</label>
                            <input type="text" class="form-control" placeholder="ORDER DATE"
                                id="order_date_end" name="order_date_end" required>
                        </div>
                        <div class="mb-3 hidd">
                            <label for="order_status" class="form-label">สถานะคำสั่งซื้อ</label>
                            <select class="form-select" id="order_status" name="order_status" required>
                                <option value="all" selected>ทั้งหมด</option>
                            </select>
                        </div>

                        <div class="mt-5">
                            <button type="button" id="btn-search" class="btn btn-dark w-100 waves-effect waves-light" disabled>ค้นหาและดาวน์โหลด</button>
                            <button class="btn btn-dark w-100 align-items-conter disabled hidd" id="btn-searching">
                                <span class="spinner-border spinner-border-sm"></span>
                                กำลังค้นหา...
                            </button>
                        </div>

                    </div>
                    <!-- End Left sidebar -->

                    <div class="inbox-rightbar">

                        <div class="d-flex justify-content-between align-items-baseline">
                            <div>
                                <h5 class="font-18">รายการล่าสุด</h5>
                                <span id="load_date" class="text-primary"></span><br>
                                <small id="time_data" class="text-secondary"></small><br>
                                <a href="javascript:void(0);" onclick="excelExport();" id="link-export" class="text-decoration-underline text-success hidd"><i class="mdi mdi-file-excel"></i>ดาวน์โหลดรายงาน</a>
                            </div>
                            <div class="text-primary">
                                <span id="orders_count"></span><br>
                                <span id="total_qty"></span><br>
                                <span id="total_price"></span>
                            </div>
                        </div>
                        <hr />

                        <div class="mt-2">
                            <table id="table" data-toggle="table" data-ajax="ajaxRequest" data-search="true"
                                data-pagination="true" data-page-size="10" class="table">
                                <thead>
                                    <tr>
                                        <th data-field="no" data-sortable="true" data-width="100">ลำดับ</th>
                                        <th data-field="shop" data-sortable="true" class="text-capitalize">ร้านค้า</th>
                                        <th data-field="ordernumber" data-sortable="true">หมายเลขคำสั่งซื้อ</th>
                                        <th data-field="total_qty" data-sortable="true">จำนวนรวม</th>
                                        <th data-field="total_price" data-sortable="true">ราคารวม</th>
                                        <th data-field="order_create" data-sortable="true">วันที่สั่งซื้อ</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                    <!-- end inbox-rightbar-->
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
<script src="{{ asset('assets/js/inputdate/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/confirmDate/confirmDate.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
<script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    moment.locale("th-TH");
    var $table = $('#table');
    $(document).ready(function(){
        const yesterday = moment().subtract(1, 'days').format('DD/MM/YYYY 16:30');
        const today = moment().format('DD/MM/YYYY 16:29');
        $("#order_date_start").flatpickr({
            enableTime: true,
            dateFormat: "d/m/Y H:i",
            time_24hr: true,
            defaultDate: yesterday,
            disableMobile: true,
            plugins: [new confirmDatePlugin({})],
            onReady: function (dateObj, dateStr, instance) {
                const $clear = $(
                    '<div class="flatpickr-clear"><button class="btn btn-sm btn-link text-danger">Clear</button></div>'
                )
                    .on("click", () => {
                        instance.clear();
                        instance.close();
                    })
                    .appendTo($(instance.calendarContainer));
            },
        });
        $("#order_date_end").flatpickr({
            enableTime: true,
            dateFormat: "d/m/Y H:i",
            time_24hr: true,
            defaultDate: today,
            disableMobile: true,
            plugins: [new confirmDatePlugin({})],
            onReady: function (dateObj, dateStr, instance) {
                const $clear = $(
                    '<div class="flatpickr-clear"><button class="btn btn-sm btn-link text-danger">Clear</button></div>'
                )
                    .on("click", () => {
                        instance.clear();
                        instance.close();
                    })
                    .appendTo($(instance.calendarContainer));
            },
        });
        changeBtnSearch();
        $("#shop_id, #order_date_start, #order_date_end, #order_status").on("change", function(){
            changeBtnSearch();
        });
        $("#btn-search").on("click", function(){
            search();
        });
    });
    function changeBtnSearch() {
        if ($("#shop_id").val().length>0 && $("#order_date_start").val().length>0 && $("#order_date_end").val().length>0 && $("#order_status").val().length>0) {
            $("#btn-search").prop("disabled", false);
        } else {
            $("#btn-search").prop("disabled", true);
        }
    }
    function search() {
        const date_start = moment($("#order_date_start").val(), "DD/MM/YYYY h:mm");
        const date_end = moment($("#order_date_end").val(), "DD/MM/YYYY h:mm");
        const start = new Date(date_start);
        const end = new Date(date_end);
        if (start > end) {
            Swal.fire({
                icon: "warning",
                title: "โปรดระบุวันที่สั่งซื้อให้ถูกต้อง",
                timer: 2000,
                showConfirmButton: false,
            });
        } else {
            $.ajax({
                url: "{{ Route('middleware.order-report.get-orders') }}",
                method: 'GET',
                data: {
                    shop_id: $("#shop_id").val(),
                    order_date_start: $("#order_date_start").val(),
                    order_date_end: $("#order_date_end").val(),
                    order_status: $("#order_status").val(),
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#btn-search').hide();
                    $('#btn-searching').show();
                    Swal.fire({
                        html: '<i class="mdi mdi-spin mdi-loading mdi-48px text-primary"></i><h4>Loading...</h4>',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    });
                },
                complete: function() {
                    $('#btn-search').show();
                    $('#btn-searching').hide();
                },
                success: function(res) {
                    console.log(res);
                    Swal.close();
                    if (res.success == true) {
                        Swal.fire({
                            icon: "success",
                            title: "โหลดข้อมูลเรียบร้อย",
                        });
                        excelExport();
                        $table.bootstrapTable('refresh');
                    } else {
                        Swal.fire({
                            icon: "warning",
                            title: res.message,
                        });
                    }
                }
            });
        }
    }
    function ajaxRequest(params) {
        var url = "{{ route('middleware.order-report.get-data') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            $("#orders_count").html('คำสั่งซื้อ ' + res.total + ' รายการ');
            $("#load_date").html('วันที่สั่งซื้อ ' + res.load_date);
            $("#time_data").html('ช่วงเวลาที่โหลดข้อมูล ' + res.time_data);
            $("#total_qty").html('จำนวนรวม ' + res.total_qty + ' ชิ้น');
            $("#total_price").html('ราคารวม ' + res.total_price + ' บาท');
            if (res.total > 0) {
                $('#link-export').show();
            } else {
                $('#link-export').hide();
            }
            params.success(res);
        });
    }
    function excelExport() {
        var query = {
            type: 'qty'
        }
        var url = "{{ route('middleware.order-report.export') }}?" + $.param(query);
        window.location = url;
    }
</script>
@endsection
