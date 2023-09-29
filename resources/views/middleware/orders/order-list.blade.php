@extends('layouts.master-layout', ['page_title' => "จัดการคำสั่งซื้อ"])
@section('css')
<!-- third party css -->
<link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/confirmDate/confirmDate.min.css') }}" rel="stylesheet" />
<!-- third party css end -->
<style>
    .label-btn-check {
        user-select: none;
    }
    .shop-check input[type="radio"] {
        display: none;
    }
    .shop-check input[type="radio"] + .label-btn-check {
        cursor: pointer;
    }
    .shop-check input[type="radio"] + .label-btn-check:before {
        content: "\2713 \a";
        white-space: pre;
        color: transparent;
        font-weight: bold;
    }
    .shop-check input[type="radio"]:checked + .label-btn-check {
        background-color: #c31d1f;
        color: #FFF;
    }
    .shop-check input[type="radio"]:checked + .label-btn-check:before {
        color: inherit;
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
                <h4 class="page-title">จัดการคำสั่งซื้อ</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <div class="row">
                                <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                    <label for="shop_id">ร้านค้า</label>
                                    <select class="form-select" name="shop_id" id="shop_id">
                                        <option value="all" selected>ทั้งหมด</option>
                                        @foreach ($eshop as $shop)
                                        <option value="{{ $shop->id }}">
                                            {{ $shop->platform_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                    <label for="date_start">วันที่สั่งซื้อ</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-white p-0 px-2 text-muted"><i class="mdi mdi-calendar-range mdi-18px"></i></span>
                                        <input type="text" class="form-control" id="date_start" name="date_start" placeholder="เลือกวันที่">
                                        <input type="text" class="form-control" id="date_end" name="date_end" placeholder="เลือกวันที่">
                                    </div>
                                </div>
                                <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                    <label for="order_status">สถานะ</label>
                                    <select class="form-select" id="order_status" name="order_status">
                                        <option value="all">ทั้งหมด</option>
                                        <option value="1" selected>รอดำเนินการ</option>
                                        <option value="2">อัปเดตและส่งออก</option>
                                        <option value="0">ยกเลิก</option>
                                    </select>
                                </div>
                                <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                    <label for="search">ค้นหา</label>
                                    <input type="text" class="form-control" id="search" name="search" autocomplete="off" placeholder="ค้นหา">
                                </div>
                                <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                    <br>
                                    <button type="button" id="btnSearch" class="btn btn-dark">ค้นหา</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                            <label>&nbsp;</label>
                            <div class="text-sm-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-success hidd" id="btnUpExport"><i class="mdi mdi-upload me-1"></i>อัปเดตและส่งออก</button>
                                    <button type="button" class="btn btn-primary" id="btnLoadOrder"><i class="mdi mdi-download me-1"></i>โหลดคำสั่งซื้อ</button>
                                    <button type="button" class="btn btn-info" id="btnFileHistory"><i class="mdi mdi-history me-1"></i>ไฟล์</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="table"
                        data-toggle="table"
                        data-loading-template="loadingTemplate"
                        data-cookie="true"
                        data-cookie-id-table="orderPageId"
                        {{-- data-search="true"
                        data-search-align="left"
                        data-search-selector="#search"
                        data-search-time-out="1000" --}}
                        data-ajax="ajaxRequest"
                        data-query-params="queryParams"
                        data-pagination="true"
                        data-side-pagination="server"
                        data-click-to-select="true"
                        data-page-list="[25, 50, 100, 200, 500]"
                        data-page-size="500"
                        class="table text-nowrap">
                        <thead>
                            <tr>
                                <th data-field="state" data-checkbox="true" data-formatter="stateFormatter"></th>
                                <th data-field="order_id" data-visible="false">ID</th>
                                <th data-field="no" data-visible="false" data-sortable="true" data-width="100">ลำดับ</th>
                                <th data-field="shop" data-sortable="true" class="text-capitalize">ร้านค้า</th>
                                <th data-field="order_number" data-sortable="true" data-click-to-select="false">หมายเลขคำสั่งซื้อ</th>
                                <th data-field="create_time" data-sortable="true" data-click-to-select="false">วันที่สั่งซื้อ</th>
                                <th data-field="total_quantity" data-sortable="true" data-click-to-select="false">จำนวนรวม</th>
                                <th data-field="total_amount" data-sortable="true" data-click-to-select="false">ราคารวม</th>
                                <th data-field="status" data-visible="false" data-click-to-select="false">สถานะ</th>
                                <th data-field="status_text" data-sortable="true" data-click-to-select="false">สถานะ</th>
                                <th data-field="action" data-valign="top" data-sortable="false" data-width="100" data-click-to-select="false">ดำเนินการ</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="loadOrderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="fullWidthModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h4 class="modal-title">โหลดข้อมูลคำสั่งซื้อ</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <label class="form-label">เลือกร้านค้า</label>
                        @foreach ($eshop as $shop)
                        <div class="col-sm-6 mb-3">
                            <div class="shop-check">
                                <input type="radio" class="btn-check shop_item" autocomplete="off" value="{{ $shop->id }}" id="shop_{{ $loop->index + 1 }}" name="shop[]">
                                <label class="btn btn-lg btn-outline-danger label-btn-check w-100" for="shop_{{ $loop->index + 1 }}">{{ $shop->platform_name }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="row d-flex justify-content-between mt-5">
                        <div class="col-sm-6">
                            <button type="button" id="btnSubmitLoad" class="btn btn-dark waves-effect waves-light">โหลดข้อมูลคำสั่งซื้อ</button>
                            <button class="btn btn-dark align-items-conter disabled hidd" id="btnSubmitLoading">
                                <span class="spinner-border spinner-border-sm"></span>
                                กำลังโหลดข้อมูลคำสั่งซื้อ...
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-end">
                                <button type="button" class="btn btn-light border waves-effect waves-light" data-bs-dismiss="modal">ปิด</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="fileHistoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="fullWidthModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h4 class="modal-title">ประวัติไฟล์ส่งออกล่าสุด</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <ul class="nav nav-tabs nav-bordered nav-justified">
                                <li class="nav-item">
                                    <a href="#shopee-content" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                                        Shopee
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#lazada-content" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                        Lazada
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane show active" id="shopee-content">
                                    <table class="table" id="tb-filehistory-shopee">
                                        <thead>
                                            <tr>
                                                <th>ชื่อไฟล์</th>
                                                <th>วันที่</th>
                                                <th>ดาวน์โหลด</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="lazada-content">
                                    <table class="table" id="tb-filehistory-lazada">
                                        <thead>
                                            <tr>
                                                <th>ชื่อไฟล์</th>
                                                <th>วันที่</th>
                                                <th>ดาวน์โหลด</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">ปิด</button>
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
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table-cookie.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table-style.js')}}"></script>
<script src="{{asset('assets/libs/tippy.js/tippy.js.min.js')}}"></script>
<script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/confirmDate/confirmDate.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/range/rangePlugin.min.js') }}"></script>
<script src="{{asset('assets/libs/flatpickr/dist/l10n/th.js')}}"></script>
<script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    moment.locale("th-TH");
    flatpickr.localize(flatpickr.l10ns.th);
    var $table = $("#table");
    var myLoadOrderModal = new bootstrap.Modal(document.getElementById('loadOrderModal'));
    var myFileHistoryModal = new bootstrap.Modal(document.getElementById('fileHistoryModal'));
    var objDataOrder = [];
    $(document).ready(function() {
        const yesterday = moment().subtract(1, 'days').format('DD/MM/YYYY 00:00');
        const today = moment().format('DD/MM/YYYY 23:59');
        // const date_start = getCookie('middleware.orders.date_start') != '' ? getCookie('middleware.orders.date_start') : yesterday;
        // const date_end = getCookie('middleware.orders.date_end') != '' ? getCookie('middleware.orders.date_end') : today;
        const date_start = "";
        const date_end = "";
        document.getElementById('date_start').value = date_start;
        document.getElementById('date_end').value = date_end;
        $table.on('check-all.bs.table', function (e, rowsAfter, rowsBefore) {
            objDataOrder = [];
            if (rowsAfter.length > 0) {
                for (var i = 0; i < rowsAfter.length; i++) {
                    objDataOrder.push(rowsAfter[i].order_id);
                }
            }
            toggleBtnUpExport();
        });
        $table.on('check.bs.table', function (e, row, $element) {
            objDataOrder.push(row.order_id);
            toggleBtnUpExport();
        });
        $table.on('uncheck-all.bs.table', function (e, rowsAfter, rowsBefore) {
            objDataOrder = [];
            toggleBtnUpExport();
        });
        $table.on('uncheck.bs.table', function (e, row, $element) {
            if (objDataOrder.length > 0) {
                for (var i = 0; i < objDataOrder.length; i++) {
                    if (objDataOrder[i] === row.order_id) {
                        objDataOrder.splice(i, 1);
                    }
                }
            }
            toggleBtnUpExport();
        });
        $("#date_start").flatpickr({
            locale: {
                firstDayOfWeek: 0,
            },
            enableTime: true,
            dateFormat: "d/m/Y H:i",
            time_24hr: true,
            defaultDate: date_start,
            defaultHour: 0,
            defaultMinute: 0,
            disableMobile: true,
            plugins: [
                new confirmDatePlugin({})
            ],
            onReady: function (dateObj, dateStr, instance) {
                const $clear = $(
                    '<div class="flatpickr-clear"><button class="btn btn-sm btn-link">Clear</button></div>'
                )
                    .on("click", () => {
                        instance.clear();
                        instance.close();
                    })
                    .appendTo($(instance.calendarContainer));
            },
            // onChange: function(selectedDates, dateStr, instance) {
            //     setCookie('middleware.orders.date_start', dateStr, 1);
            // },
            onClose: function(selectedDates, dateStr, instance){
                $(instance.input).blur();
            }
        });
        $("#date_end").flatpickr({
            locale: {
                firstDayOfWeek: 0,
            },
            enableTime: true,
            dateFormat: "d/m/Y H:i",
            time_24hr: true,
            defaultDate: date_end,
            defaultHour: 23,
            defaultMinute: 59,
            disableMobile: true,
            plugins: [
                new confirmDatePlugin({})
            ],
            onReady: function (dateObj, dateStr, instance) {
                const $clear = $(
                    '<div class="flatpickr-clear"><button class="btn btn-sm btn-link">Clear</button></div>'
                )
                    .on("click", () => {
                        instance.clear();
                        instance.close();
                    })
                    .appendTo($(instance.calendarContainer));
            },
            // onChange: function(selectedDates, dateStr, instance) {
            //     setCookie('middleware.orders.date_end', dateStr, 1);
            // },
            onClose: function(selectedDates, dateStr, instance){
                $(instance.input).blur();
            }
        });
        $("#btnSearch").click(function() {
            $table.bootstrapTable('refreshOptions', {
                shop_id: $("#shop_id").val()
            });
            rebuild();
        });
        $("#btnUpExport").click(function() {
            updateAndExportConfirmation();
        });
        $("#btnFileHistory").click(function() {
            fileHistory();
        });
        $("#btnLoadOrder").click(function() {
            $("input:radio[name='shop[]']").prop('checked', false);
            $("#btnSubmitLoad").prop("disabled", true);
            myLoadOrderModal.show();
        });
        changeBtnLoadOrders();
        $(".shop_item").on("change", function(){
            changeBtnLoadOrders();
        });
        $("#btnSubmitLoad").click(function() {
            loadOrders();
        });
    });
    function toggleBtnUpExport() {
        $("#btnUpExport").css("display", !objDataOrder.length ? 'none' : 'block');
    }
    function changeBtnLoadOrders() {
        if ($(".shop_item:checked").length>0) {
            $("#btnSubmitLoad").prop("disabled", false);
        } else {
            $("#btnSubmitLoad").prop("disabled", true);
        }
    }
    function stateFormatter(value, row, index) {
        if (row.status !== 1) {
            return {
                disabled: true
            }
        }
        return value;
    }
    function updateAndExportConfirmation() {
        var file_name = '';
        var orders_canceled;
        Swal.fire({
            icon: "warning",
            title: "ยืนยันการอัปเดตและส่งออก ใช่ไหม?",
            text: "รวม " + objDataOrder.length + " คำสั่งซื้อ",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch('/middleware/orders/upexport', {
                        method: 'POST',
                        headers: {
                            'Content-type': 'application/json; charset=UTF-8',
                            'X-CSRF-TOKEN': '{{csrf_token()}}',
                        },
                        body: JSON.stringify({'order_ids': objDataOrder}),
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
                        orders_canceled = data.orders_canceled;
                        file_name = data.file_name;
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
                    title: "เรียบร้อย!",
                    html: (orders_canceled != "") ? "<span class='text-danger'>มีคำสั่งซื้อที่ถูกยกเลิก</span><br>" + orders_canceled : "",
                });
                setTimeout(() => {
                    download("{{ url('/middleware/orders/downloadfile') }}/"+file_name, file_name);
                }, 1000);
                objDataOrder = [];
                $table.bootstrapTable("refresh");
                rebuild();
            }
        });
    }
    function deleteOrderConfirmation(id, order_number) {
        Swal.fire({
            icon: "warning",
            title: "ยืนยันการลบ ใช่ไหม?",
            text: "หมายเลขคำสั่งซื้อ : " + order_number,
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch('/middleware/orders/destroy', {
                        method: 'POST',
                        headers: {
                            'Content-type': 'application/json; charset=UTF-8',
                            'X-CSRF-TOKEN': '{{csrf_token()}}',
                        },
                        body: JSON.stringify({'id': id}),
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
                    title: "เรียบร้อย!",
                    timer: 2000,
                });
                $table.bootstrapTable("refresh");
                rebuild();
            }
        });
    }
    function queryParams(params) {
        setTimeout(() => {
            params.shop_id = $("#shop_id").val();
            params.date_start = $("#date_start").val();
            params.date_end = $("#date_end").val();
            params.order_status = $("#order_status").val();
            params.search = $("#search").val();
        }, 200);
        return params;
    }
    function ajaxRequest(params) {
        setTimeout(() => {
            var url = "{{ route('middleware.order-list.get-data') }}";
            $.get(url + '?' + $.param(params.data)).then(function (res) {
                params.success(res)
                tippy('.action-icon')
            });
            objDataOrder = [];
            toggleBtnUpExport();
        }, 500);
    }
    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        let expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i <ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
            c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
            }
        }
        return "";
    }
    const download = async (url, filename) => {
        const response = await fetch(url);
        const blob = await response.blob();
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    };
    function callDownload(url, filename) {
        download(url, filename);
    }
    function fileHistory() {
        $.ajax({
            url: "{{ url('middleware/orders/filehistory') }}",
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                let htmlShopee = ``;
                let htmlLazada = ``;
                if (res.success == true) {
                    for (let i = 0; i < res.shopee.length; i++) {
                        let file_name = res.shopee[i]['file_name'];
                        let file_date = res.shopee[i]['file_date'];
                        let file_download = "{{ url('/middleware/orders/downloadfile') }}/" + file_name;
                        htmlShopee += `<tr><td>`+file_name+`</td><td>`+file_date+`</td><td><a href="javascript:void(0)" onclick="callDownload('` + file_download + `', '` + file_name + `')"><i class="mdi mdi-download me-1"></i></a></td></tr>`;
                    }
                    for (let i = 0; i < res.lazada.length; i++) {
                        let file_name = res.lazada[i]['file_name'];
                        let file_date = res.lazada[i]['file_date'];
                        let file_download = "{{ url('/middleware/orders/downloadfile') }}/" + file_name;
                        htmlLazada += `<tr><td>`+file_name+`</td><td>`+file_date+`</td><td><a href="javascript:void(0)" onclick="callDownload('` + file_download + `', '` + file_name + `')"><i class="mdi mdi-download me-1"></i></a></td></tr>`;
                    }
                }
                document.querySelector("#tb-filehistory-shopee tbody").innerHTML = htmlShopee;
                document.querySelector("#tb-filehistory-lazada tbody").innerHTML = htmlLazada;
                myFileHistoryModal.show();
            }
        });
    }
    function loadOrders() {
        const shop = $("input[name='shop[]']:checked").map( function () {
            return this.value;
        }).get();
        const date_start = moment().subtract(5, 'days').format('DD/MM/YYYY 00:00');
        const date_end = moment().format('DD/MM/YYYY 23:59');
        const start = new Date(moment(date_start, "DD/MM/YYYY h:mm"));
        const end = new Date(moment(date_end, "DD/MM/YYYY h:mm"));
        if (shop.length <= 0) {
            Swal.fire({icon: "warning", title: "โปรดเลือกร้านค้า"});
        } else if (start > end) {
            Swal.fire({icon: "warning", title: "โปรดระบุวันที่สั่งซื้อให้ถูกต้อง"});
        } else {
            $.ajax({
                url: "{{ Route('middleware.order-list.get-orders') }}",
                method: 'GET',
                data: {
                    shop: shop,
                    date_start: date_start,
                    date_end: date_end,
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#btnSubmitLoad').hide();
                    $('#btnSubmitLoading').show();
                    Swal.fire({
                        html: '<i class="mdi mdi-spin mdi-loading mdi-48px text-primary"></i><h4>กำลังโหลดข้อมูลคำสั่งซื้อ...</h4>',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    });
                },
                complete: function() {
                    $('#btnSubmitLoad').show();
                    $('#btnSubmitLoading').hide();
                },
                success: function(res) {
                    Swal.close();
                    if (res.success == true) {
                        myLoadOrderModal.hide();
                        Swal.fire({
                            icon: "success",
                            title: "โหลดข้อมูลเรียบร้อย",
                            text: res.sum_orders_total + " คำสั่งซื้อ",
                        });
                        $table.bootstrapTable("refresh");
                        rebuild();
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
</script>
@endsection