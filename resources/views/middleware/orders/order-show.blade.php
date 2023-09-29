@extends('layouts.master-layout', ['page_title' => "จัดการคำสั่งซื้อ"])
@section('css')
<!-- third party css -->
<link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/page-sarabun-print.css') }}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
<style>
    @media print {
        div.content, div.row, div.card {
            background-color: #ffffff;
            box-shadow: none;
        }
        div.note {
            padding-top: 0 !important;
        }
        span.badge {
            color: #000000;
        }
        small.original_price {
            display: none;
        }
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
                <h4 class="page-title">ข้อมูลคำสั่งซื้อ</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card page-sarabun">
                <div class="card-body">
                    <!-- Logo & title -->
                    <div class="clearfix pt-1">
                        <div class="float-start">
                            <h3 class="m-0">#{{ strtoupper($eshop->platform_name) }}</h3>
                        </div>
                        <div class="float-end">
                            <h4 class="m-0">คำสั่งซื้อ</h4>
                        </div>
                    </div>

                    <div class="clearfix">
                        <div class="float-start">
                            <div class="mt-3">
                                <h6>ที่อยู่จัดส่ง</h6>
                                <address>
                                    @if (count($shipping_address))
                                    {{ $shipping_address['first_name'] }} {{ $shipping_address['last_name'] }}<br>
                                    @if (strtoupper($eshop->platform_name) == "SHOPEE")
                                    {{ $shipping_address['full_address'] }}<br>
                                    @elseif (strtoupper($eshop->platform_name) == "LAZADA")
                                    {{ $shipping_address['address1'] }} {{ $shipping_address['address2'] }}<br>
                                    {{ explode("/",$shipping_address['district'])[0] }}, {{ explode("/",$shipping_address['province'])[0] }} {{ $shipping_address['post_code'] }}<br>
                                    @elseif (strtoupper($eshop->platform_name) == "TIKTOK")
                                    {{ $shipping_address['address_detail'] }}<br>
                                    @endif
                                    <abbr title="Phone">P:</abbr> {{ $shipping_address['phone'] }}
                                    @endif
                                </address>
                            </div>
                        </div>
                        <div class="float-end">
                            <div class="mt-3 float-end">
                                <p><strong>วันที่สั่งซื้อ : </strong> <span class="float-end"> &nbsp;&nbsp;&nbsp;&nbsp; {{ \Carbon\Carbon::parse($order->create_time)->format('d/m/Y H:i:s') }}</span></p>
                                <p><strong>สถานะคำสั่งซื้อ : </strong> <span class="float-end">{!! $status !!}</span></p>
                                <p><strong>หมายเลขคำสั่งซื้อ : </strong> <span class="float-end"> &nbsp;&nbsp; {{ $order->order_number }}</span></p>
                                @if ($order->so_number != "")
                                <p><strong>หมายเลข SO : </strong> <span class="float-end"> &nbsp;&nbsp; {{ $order->so_number }}</span></p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="table-responsive">
                                @foreach ($order_package_items as $package_item)
                                <b>แพ็กเกจ {{ $loop->index + 1 }}</b>
                                <span class="text-muted ms-2">หมายเลขพัสดุ : {{ $package_item->package_number }}</span>
                                <span class="text-muted ms-2">หมายเลขจัดส่ง : {{ $package_item->tracking_number }}</span>
                                <span class="text-muted ms-2">จำนวน : {{ $package_item->package_total_quantity }}</span>
                                @if ($package_item->package_number=="" && $package_item->tracking_number=="")
                                <span class="badge bg-danger fw-normal ms-2">ยกเลิก</span>
                                @endif
                                <table class="table mb-4 align-top">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px">#</th>
                                            <th>รหัส/ชื่อสินค้า</th>
                                            <th style="width: 12%">จำนวน</th>
                                            <th style="width: 12%">ราคา</th>
                                            <th style="width: 12%" class="text-end">ราคารวม</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $line_item = 1; @endphp
                                        @foreach ($order_items as $item)
                                        @if ($package_item->tracking_number == $item->tracking_number)
                                        <tr>
                                            <td>{{ $line_item }}</td>
                                            <td>
                                                {{ $item->name }} <br />
                                                @if ($item->variation != "")
                                                <small class="text-muted fs-6">{{ $item->variation }}</small> <br />
                                                @endif
                                                <small class="text-muted">รหัสสินค้า : {{ $item->sku }}</small>
                                            </td>
                                            <td>{{ number_format($item->quantity) }}</td>
                                            <td>
                                                <span class="baht-symbol">฿</span>{{ number_format($item->sale_price, 2) }}
                                                @if ($item->original_price > $item->sale_price)
                                                <small class="original_price text-danger text-decoration-line-through ms-1"><span class="baht-symbol">฿</span>{{ number_format($item->original_price, 2) }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end"><span class="baht-symbol">฿</span>{{ number_format($item->quantity * $item->sale_price, 2) }}</td>
                                        </tr>
                                        @php $line_item++; @endphp
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>
                                @endforeach
                            </div> <!-- end table-responsive -->
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row">
                        <div class="col-sm-6">
                            {{-- <div class="clearfix pt-5 note">
                                <h6 class="text-muted">Notes:</h6>
                                <small class="text-muted">
                                    All accounts are to be paid within 7 days from receipt of
                                    invoice. To be paid by cheque or credit card or direct payment
                                    online. If account is not paid within 7 days the credits details
                                    supplied as confirmation of work undertaken will be charged the
                                    agreed quoted fee noted above.
                                </small>
                            </div> --}}
                        </div> <!-- end col -->
                        <div class="col-sm-6">
                            {{-- <div class="float-end">
                                <p><b>จำนวนรวม :</b> <span class="float-end">{{ number_format($order->total_quantity) }} ชิ้น</span></p>
                                <p><b>ยอดรวม :</b> <span class="float-end">{{ number_format($order->total_amount, 2) }} บาท</span></p>
                                <p><b>ค่าจัดส่ง :</b> <span class="float-end"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ number_format($order->total_shipping_fee, 2) }} บาท</span></p>
                                <p><b>ส่วนลด :</b> <span class="float-end"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ number_format($order->total_discount, 2) }} บาท</span></p>
                                <h3>{{ number_format($order->total_amount + $order->total_shipping_fee, 2) }} บาท</h3>
                            </div> --}}
                            <div class="float-end">
                                <p><span>จำนวนรวม :</span> <span class="float-end">{{ number_format($order->total_quantity) }} ชิ้น</span></p>
                                <p><span>ยอดรวม :</span> <span class="float-end"><span class="baht-symbol">฿</span>{{ number_format(($order->total_amount - $order->total_shipping_fee) + $order->total_discount, 2) }}</span></p>
                                <p><span>ค่าจัดส่ง :</span> <span class="float-end"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="baht-symbol">฿</span>{{ number_format($order->total_shipping_fee, 2) }}</span></p>
                                <p><span>ส่วนลด :</span> <span class="float-end"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="baht-symbol">-฿</span>{{ number_format($order->total_discount, 2) }}</span></p>
                                <div class="d-flex justify-content-between align-items-baseline"><b>ยอดรวม :</b> &nbsp;&nbsp;&nbsp;&nbsp;<span class="float-end fs-3"><span class="baht-symbol">฿</span>{{ number_format($order->total_amount, 2) }}</span></div>
                            </div>
                            <div class="clearfix"></div>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="d-flex justify-content-between mt-4 mb-1 d-print-none">
                        <div class="col-sm-6">
                            <a href="javascript:history.back()" class="btn btn-secondary waves-effect waves-light"><i class="mdi mdi-keyboard-backspace me-1"></i> Back</a>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-end">
                                <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-printer me-1"></i> พิมพ์</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row d-print-none">
        <div class="col-sm-12">
            <div class="border-0 border-bottom text-center">Action</div>
            <div class="border border-light p-2 mb-3">
                @foreach ($order_logs as $list)
                <div class="post-user-comment-box bg-white rounded my-1">
                    <div class="d-flex align-items-start">
                        <img class="me-2 avatar-sm rounded-circle" src="{{ url('assets/images/users/thumbnail/'.$list->image) }}" onerror="this.onerror=null;this.src='{{ url('assets/images/users/thumbnail/user-1.jpg') }}'" alt="image">
                        <div class="w-100">
                            <h5 class="mt-0">{{ $list->name . ' ' . $list->surname }} <small class="text-muted">{{ \Carbon\Carbon::parse($list->updated_at)->locale('th_TH')->diffForHumans() }}</small></h5>
                            <i class="mdi mdi-share-outline me-1"></i>{{ $list->description }}<small class="text-muted ms-2">{{ \Carbon\Carbon::parse($list->updated_at)->thaidate('D j M Y, เวลา H:i น.') }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
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
<script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    moment.locale("th-TH");
    $(document).ready(function() {
    });
</script>
@endsection