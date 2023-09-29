@extends('layouts.master-layout', ['page_title' => "โหลดข้อมูลคำสั่งซื้อ"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/confirmDate/confirmDate.min.css') }}" rel="stylesheet" />
<!-- third party css end -->
<style>
    .list-group-item {
        user-select: none;
    }

    .list-group input[type="radio"] {
        display: none;
    }

    .list-group input[type="radio"] + .list-group-item {
        cursor: pointer;
    }

    .list-group input[type="radio"] + .list-group-item:before {
        content: "\2713";
        color: transparent;
        font-weight: bold;
        margin-right: 1em;
    }

    .list-group input[type="radio"]:checked + .list-group-item {
        background-color: #c31d1f;
        color: #FFF;
    }

    .list-group input[type="radio"]:checked + .list-group-item:before {
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
                <h4 class="page-title">โหลดข้อมูลคำสั่งซื้อ</h4>
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
                        <div class="mb-3">
                            <label class="form-label">เลือกร้านค้า</label>
                            <div class="list-group">
                                @foreach ($eshop as $shop)
                                <input class="form-check-input shop_item" type="radio" value="{{ $shop->id }}" id="shop_{{ $loop->index + 1 }}" name="shop[]">
                                <label class="list-group-item fw-normal @if($loop->index==0) rounded-top @endif" for="shop_{{ $loop->index + 1 }}">{{ $shop->platform_name }}</label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- End Left sidebar -->

                    <div class="inbox-rightbar">
                        <div class="row">
                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                <label for="date_start" class="form-label">วันที่สั่งซื้อ (เริ่มต้น)</label>
                                <input type="text" class="form-control" placeholder="เลือกวันที่" id="date_start" name="date_start" required>
                            </div>
                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                <label for="date_end" class="form-label">วันที่สั่งซื้อ (สิ้นสุด)</label>
                                <input type="text" class="form-control" placeholder="เลือกวันที่" id="date_end" name="date_end" required>
                            </div>
                        </div>
                        <hr>
                        <button type="button" id="btn-load" class="btn btn-dark waves-effect waves-light">โหลดข้อมูลคำสั่งซื้อ</button>
                        <button class="btn btn-dark align-items-conter disabled hidd" id="btn-loading">
                            <span class="spinner-border spinner-border-sm"></span>
                            กำลังโหลดข้อมูลคำสั่งซื้อ...
                        </button>
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
<script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/confirmDate/confirmDate.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/range/rangePlugin.min.js') }}"></script>
<script src="{{asset('assets/libs/flatpickr/dist/l10n/th.js')}}"></script>
<script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    $(document).ready(function() {
        moment.locale("th-TH");
        flatpickr.localize(flatpickr.l10ns.th);
        const yesterday = moment().subtract(1, 'days').format('DD/MM/YYYY 00:00');
        const today = moment().format('DD/MM/YYYY 23:59');
        $("#date_start").flatpickr({
            locale: {
                firstDayOfWeek: 0,
            },
            inline: true,
            enableTime: true,
            dateFormat: "d/m/Y H:i",
            defaultHour: 0,
            defaultMinute: 0,
            // defaultDate: yesterday,
            time_24hr: true,
            disableMobile: true,
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
        });
        $("#date_end").flatpickr({
            locale: {
                firstDayOfWeek: 0,
            },
            inline: true,
            enableTime: true,
            dateFormat: "d/m/Y H:i",
            defaultHour: 23,
            defaultMinute: 59,
            // defaultDate: today,
            time_24hr: true,
            disableMobile: true,
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
        });
        changeBtnLoadOrders();
        $(".shop_item, #date_start, #date_end").on("change", function(){
            changeBtnLoadOrders();
        });
        $("#btn-load").click(function() {
            loadOrders();
        });
    });
    function changeBtnLoadOrders() {
        if ($(".shop_item:checked").length>0 && $("#date_start").val().length>0 && $("#date_end").val().length>0) {
            $("#btn-load").prop("disabled", false);
        } else {
            $("#btn-load").prop("disabled", true);
        }
    }
    function loadOrders() {
        const shop = $("input[name='shop[]']:checked").map( function () {
            return this.value;
        }).get();
        const date_start = $("#date_start").val();
        const date_end = $("#date_end").val();
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
                    $('#btn-load').hide();
                    $('#btn-loading').show();
                    Swal.fire({
                        html: '<i class="mdi mdi-spin mdi-loading mdi-48px text-primary"></i><h4>Loading...</h4>',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    });
                },
                complete: function() {
                    $('#btn-load').show();
                    $('#btn-loading').hide();
                },
                success: function(res) {
                    console.log(res);
                    Swal.close();
                    if (res.success == true) {
                        Swal.fire({
                            icon: "success",
                            title: "โหลดข้อมูลเรียบร้อย",
                            text: res.orders_total + " คำสั่งซื้อ",
                        });
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