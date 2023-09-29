@extends('layouts.master-nopreloader-layout', ['page_title' => 'รายการใบเบิกอุปกรณ์'])
@section('css')
    <link href="{{ asset('assets/css/placeholder-loading.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/monthSelect/style.css') }}" rel="stylesheet" />
@endsection

@section('content')
    @inject('thaiDateHelper', '\App\Services\ThaiDateHelperService')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">repair</a></li>
                            <li class="breadcrumb-item active">ใบเบิกอุปกรณ์</li>
                        </ol>
                    </div>
                    <h5 class="page-title">รายการใบเบิกอุปกรณ์</h5>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="float-end">
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                            data-bs-target="#printwithdrawModal">
                            <i class="mdi mdi-printer me-1"></i> Print
                        </button>
                    </div>

                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <div class="row">
                                <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                    <label for="doc_date" class="form-label mb-0">เดือน/ปี</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control month-datepicker" id="doc_date"
                                            name="doc_date" placeholder="เลือกเดือน">
                                    </div>
                                </div>
                                <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                    <label for="dept_category">แผนกงาน</label>
                                    <select class="form-select" name="dept_category" id="dept_category">
                                        <option value="" id="dept_category_empty">เลือกแผนกงาน</option>
                                        @if ($dept_select)
                                            @if (Auth::User()->roleAdmin() || auth()->user()->emp_id == 500383)
                                                <option value="A03050100">แผนกไฟฟ้าและสุขาภิบาล </option>
                                                <option value="A03050200">แผนกยานยนต์</option>
                                                <option value="A03060100">แผนกซ่อมบำรุง</option>
                                                <option value="A01100100">แผนกไอที</option>
                                            @else
                                                @foreach ($dept_select as $item)
                                                    @switch($item->dept_id)
                                                        @case('A03050100')
                                                            <option value="A03050100">แผนกไฟฟ้าและสุขาภิบาล </option>
                                                        @break

                                                        @case('A03050200')
                                                            <option value="A03050200">แผนกยานยนต์</option>
                                                        @break

                                                        @case('A03060100')
                                                            <option value="A03060100">แผนกซ่อมบำรุง</option>
                                                        @break

                                                        @case('A01100100')
                                                            <option value="A01100100">แผนกไอที</option>
                                                        @break

                                                        @default
                                                    @endswitch
                                                @endforeach
                                            @endif
                                        @endif
                                    </select>
                                </div>
                                <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                    <label for="search">ค้นหา</label>
                                    <input type="text" class="form-control" placeholder="ค้นหา" name="search"
                                        id="search" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-striped text-nowrap" id="table" data-toggle="table"
                        data-loading-template="loadingTemplate" data-buttons-class="btn btn-sm btn-secondary"
                        data-ajax="ajaxRequest" data-query-params="queryParams" data-undefined-text="" data-search="true"
                        data-search-align="left" data-pagination="true" data-search-selector="#search" data-page-size="10">
                        <thead>
                            <tr>
                                <th data-field="repair_order_id" data-sortable="true">รหัสใบแจ้งซ่อม</th>
                                <th data-field="dept_id" data-sortable="true">แผนก</th>
                                <th data-field="repair_order_count" data-sortable="true">จำนวนรายการ</th>
                                <th data-field="qty" data-sortable="true">จำนวนชิ้น</th>
                                <th data-field="total_prices" data-sortable="true">รวมราคา</th>
                                <th data-field="emp_id" data-sortable="true">ผู้เบิก</th>
                                <th data-field="withdraw_date" data-sortable="true">วันที่</th>
                                <th data-field="action" data-sortable="false" data-width="150">จัดการ</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        {{-- printwithdrawModal --}}
        {{-- <div id="printwithdrawModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" role="dialog" aria-labelledby="printwithdrawModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="printwithdrawModalLabel">พิมพ์ใบเบิกอุปกรณ์</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <embed src="{{ url('repair/withdraw-print-pdf', $oid) }}" frameborder="0" width="100%"
                            height="700px">
                    </div>
                </div>
            </div>
        </div> --}}

    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/bootstrap-tables.init.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-table-style.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/monthSelect/index.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
    <script type="text/javascript">
        var $table = $('#table');
        $(document).ready(function() {
            let date = new Date();
            let formattedDate = date.getFullYear() + '-' + (date.getMonth() + 1).toString().padStart(2, '0'); 
            flatpickr.localize(flatpickr.l10ns.th);
            $(".month-datepicker").flatpickr({
                altInput: true,
                disableMobile: "true",
                dateFormat: "Y-m",
                defaultDate: formattedDate, 
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: "Y-m",
                        altFormat: "F Y",
                        theme: "light"
                    })
                ],
                onReady: function(dateObj, dateStr, instance) {
                    const $clear = $(
                            '<div class="flatpickr-clear"><button class="btn btn-sm btn-link">Clear</button></div>'
                        )
                        .on("click", () => {
                            instance.clear();
                            instance.close();
                        }).appendTo($(instance.calendarContainer));
                },
                onClose: function(selectedDates, dateStr, instance) {
                    $(instance.input).blur();
                }
            });
            $("#doc_date").change(function() {
                setTimeout(() => {
                    $table.bootstrapTable('refreshOptions', {
                        doc_date: $("#doc_date").val()
                    });
                }, 200);
            });
            $("#dept_category").change(function() {
                setTimeout(() => {
                    $table.bootstrapTable('refreshOptions', {
                        dept_category: $("#dept_category").val()
                    });
                }, 200);
            });
        });

        function queryParams(params) {
            setTimeout(() => {
                params.doc_date = $("#doc_date").val();
                params.dept_category = $("#dept_category").val();
            }, 200);
            return params;
        }

        function ajaxRequest(params) {
            setTimeout(() => {
                var url = "{{ route('withdraw.searchWithdrawALL') }}";
                $.get(url + '?' + $.param(params.data)).then(function(res) {
                    params.success(res)
                });
            }, 200);
        }
    </script>
@endsection
