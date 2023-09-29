@extends('layouts.master-layout', ['page_title' => "ดาวน์โหลดคำสั่งซื้อ"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/inputdate/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Orders</a></li>
                        <li class="breadcrumb-item active">คำสั่งซื้อ</li>
                    </ol>
                </div>
                <h4 class="page-title">ดาวน์โหลดคำสั่งซื้อ</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('orders.search') }}" method="POST"
                        onsubmit="loading();">
                        {{ csrf_field() }}
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="row justify-content-center">
                            <div class="col-md-8 col-lg-6 col-xl-4">
                                <div class="form-group mb-3">
                                    <label class="control-label ">เลือกร้านค้า</label><br>
                                    @foreach ($eshop as $list)
                                    <div class="form-check-inline">
                                        <input class="form-check-input" type="radio" id="shop{{ $loop->index }}"
                                            name="shop" value="{{ $list->id }}" @if(old('shop')==$list->id) checked
                                        @endif required>
                                        <label class="form-check-label" for="shop{{ $loop->index }}">{{
                                            $list->platform_name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mb-3">
                                    <label for="order_date" class="form-label">วันที่สั่งซื้อ (เริ่มต้น)</label>
                                    <input type="text" class="form-control custom-datepicker" placeholder="ORDER DATE"
                                        id="order_date_start" name="order_date_start"
                                        value="{{ (old('order_date_start')) ? old('order_date_start') : date('d/m/Y') }}"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="order_date" class="form-label">วันที่สั่งซื้อ (สิ้นสุด)</label>
                                    <input type="text" class="form-control custom-datepicker" placeholder="ORDER DATE"
                                        id="order_date_end" name="order_date_end"
                                        value="{{ (old('order_date_end')) ? old('order_date_end') : date('d/m/Y') }}"
                                        required>
                                </div>
                                <div class="mb-5">
                                    <label for="order_status" class="form-label">สถานะคำสั่งซื้อ</label>
                                    <select class="form-select" id="order_status" name="order_status" required>
                                        <option value="1" selected>พร้อมจัดส่ง</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <button type="submit" id="btn-search" name="btn-search"
                                        class="btn btn-dark w-100">ค้นหา และ ดาวน์โหลด</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div class="row justify-content-center">
                        <div class="col-md-12 col-lg-12 col-xl-12">
                            <div id="div_msg" class="text-center border" style="height: 200px;">
                                <br><br><br>
                                @if ($message = Session::get('success'))
                                <h1><span class="text-success">{{ $message }}</span></h1>
                                @endif
                                @if ($message = Session::get('message'))
                                <h1><span class="text-danger">{!! $message !!}</span></h1>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
{{-- inputdate --}}
<script src="{{ asset('assets/js/inputdate/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
<script src="{{ asset('assets/js/inputdate/form-pickers.init.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
            setTimeout(() => {
                var success = '{{ Session::get("success") }}';
                if (success != "") {
                    excelExport();
                }
            }, 1000);
        });
        function excelExport() {
            var url = "{{ route('orders.export') }}";
            window.location = url;
        }
        function loading(){
            $("#div_msg").html('<br><br><div class="spinner-grow avatar-md text-secondary" role="status"></div><h1><span class="text-muted">กำลังโหลดข้อมูล กรุณารอสักครู่...</span></h1>');
        }
</script>
@endsection