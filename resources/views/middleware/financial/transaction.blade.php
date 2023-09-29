@extends('layouts.master-layout', ['page_title' => 'โหลดข้อมูลรับชำระ'])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
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

        .list-group input[type="radio"]+.list-group-item {
            cursor: pointer;
        }

        .list-group input[type="radio"]+.list-group-item:before {
            content: "\2713";
            color: transparent;
            font-weight: bold;
            margin-right: 1em;
        }

        .list-group input[type="radio"]:checked+.list-group-item {
            background-color: #c31d1f;
            color: #FFF;
        }

        .list-group input[type="radio"]:checked+.list-group-item:before {
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
                            <li class="breadcrumb-item active">บัญชี</li>
                        </ol>
                    </div>
                    <h4 class="page-title">โหลดข้อมูลรับชำระ</h4>
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
                                        <input class="form-check-input shop_item" type="radio"
                                            value="{{ $shop->platform_name }}#{{ $shop->seller_id }}" id="shop_{{ $loop->index + 1 }}"
                                            name="shop[]">
                                        <label
                                            class="list-group-item fw-normal @if ($loop->index == 0) rounded-top @endif"
                                            for="shop_{{ $loop->index + 1 }}">{{ $shop->platform_name }}</label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!-- End Left sidebar -->

                        <div class="inbox-rightbar">
                            <div class="row">
                                <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                    <label for="date" class="form-label">เลือกวันที่ (เริ่มต้น - สิ้นสุด)</label>
                                    <input type="text" class="form-control" placeholder="เลือกวันที่" id="date"
                                        name="date" required>
                                </div>
                            </div>
                            <hr>
                            <button type="button" id="btn-load"
                                class="btn btn-dark waves-effect waves-light">โหลดข้อมูลรับชำระ</button>
                            <button class="btn btn-dark align-items-conter disabled hidd" id="btn-loading">
                                <span class="spinner-border spinner-border-sm"></span>
                                กำลังโหลดข้อมูลรับชำระ...
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
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/confirmDate/confirmDate.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/range/rangePlugin.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
    <script src="{{ asset('assets/js/pages/financial/transaction.js') }}"></script>
    <!-- third party js ends -->
@endsection
