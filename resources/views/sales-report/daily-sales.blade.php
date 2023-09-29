@extends('layouts.master-layout', ['page_title' => "ยอดขายรายวัน"])
@section('css')
<!-- third party css -->
<link href="{{ asset('assets/css/placeholder-loading.min.css') }}" rel="stylesheet">
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
<style>
    #btnScrollToTop {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
    }
    #btnScrollToTop:hover {
        background-color: #555;
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
                            <li class="breadcrumb-item active">Sales Report</li>
                        </ol>
                    </div>
                    <h4 class="page-title">ยอดขายรายวัน</h4>
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
                                        <label for="daily_category">หมวดหมู่รายวัน</label>
                                        <select class="form-select" name="daily_category" id="daily_category">
                                            <option value="all" selected>ทั้งหมด</option>
                                            @foreach ($daily_category as $daily_category)
                                            <option value="{{ $daily_category->daily_category }}">
                                                {{ ($daily_category->daily_category!="") ? $daily_category->daily_category : "อื่นๆ" }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                        <label for="doc_date" class="form-label mb-0">วันที่</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control daily-datepicker" id="doc_date" name="doc_date" placeholder="เลือกวันที่">
                                        </div>
                                    </div>
                                    <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                        <label for="search">ค้นหา</label>
                                        <input type="text" class="form-control" placeholder="ค้นหาสินค้า" name="search" id="search" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2 btn-export">
                                <label>&nbsp;</label>
                                <div class="text-sm-end">
                                    <button type="button" class="btn btn-soft-primary waves-effect waves-light" onclick="print();">Print</button>
                                    <button type="button" class="btn btn-soft-success waves-effect waves-light" onclick="exportExcel();">Excel</button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" class="form-control" id="available_date" name="available_date" value="{{ $available_date }}">
                        <div id="exportForm" class="hidd"></div>
                        <table id="table"
                            data-toggle="table"
                            data-loading-template="loadingTemplate"
                            data-buttons-class="btn btn-sm btn-secondary"
                            data-ajax="ajaxRequest"
                            data-query-params="queryParams"
                            data-undefined-text=""
                            data-search="true"
                            data-search-selector="#search"
                            class="table table-striped text-nowrap">
                            <thead>
                                <tr class="d-print-none">
                                    <th colspan="5" class="summary-text"></th>
                                    <th data-field="sum_qty" data-valign="middle" class="summary-qty-text"></th>
                                    <th data-field="sum_price" data-valign="middle" class="summary-price-text"></th>
                                </tr>
                                <tr>
                                    <th data-field="no" data-sortable="true">ลำดับ</th>
                                    <th data-field="daily_category" data-sortable="true">หมวดหมู่รายวัน</th>
                                    <th data-field="stkcod" data-sortable="true">รหัสสินค้า</th>
                                    <th data-field="stkdes" data-sortable="true">ชื่อสินค้า</th>
                                    <th data-field="unit" data-sortable="true">หน่วย</th>
                                    <th data-field="qty" data-sortable="true" data-sorter="qtySorter">จำนวน</th>
                                    <th data-field="price" data-sortable="true" data-sorter="priceSorter">ยอดขาย(บาท)</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <button onclick="topFunction()" id="btnScrollToTop" title="Go to top">Top</button>
    </div>
@endsection
@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap-table-loading-pl-style.js')}}"></script>
    <script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
    <script src="{{asset('assets/libs/flatpickr/dist/l10n/th.js')}}"></script>
    <script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
    <!-- third party js ends -->
    <script type="text/javascript">
        var $table = $('#table');
        $(document).ready(function() {
            moment.locale("th-TH");
            flatpickr.localize(flatpickr.l10ns.th);
            $(".daily-datepicker").flatpickr({
                locale: {
                    firstDayOfWeek: 0,
                },
                mode: "range",
                dateFormat: "d/m/Y",
                // disable: [
                //     function (dateObject) {
                //         var enabledDates = rmyAvailableDate();
                //         var date = dateObject;
                //         date.setDate(date.getDate() + 1);
                //         for (var i = 0; i < enabledDates.length; i++) {
                //             if (date.toISOString().slice(0, 10) === new Date(enabledDates[i]).toISOString().slice(0, 10)) {
                //                 return false;
                //             }
                //         }
                //         return true;
                //     }
                // ],
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
                onClose: function(selectedDates, dateStr, instance){
                    $(instance.input).blur();
                    $table.bootstrapTable('refreshOptions', {
                        doc_date: $("#doc_date").val()
                    });
                    getSummary();
                    rebuild();
                }
            });
            $("#daily_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    daily_category: $("#daily_category").val()
                });
                getSummary();
                rebuild();
            });
            // $("#doc_date").change(function() {
            //     $table.bootstrapTable('refreshOptions', {
            //         doc_date: $("#doc_date").val()
            //     });
            //     getSummary();
            //     rebuild();
            // });
        });
        function rmyAvailableDate() {
            const rdatedData = $("#available_date").val().split(",");
            return rdatedData;
        }
        function qtySorter(a, b) {
            var aa = a.replace(',', '');
            var bb = b.replace(',', '');
            return aa - bb;
        }
        function priceSorter(a, b) {
            var aa = a.replace(',', '');
            var bb = b.replace(',', '');
            return aa - bb;
        }
        function queryParams(params) {
            params.daily_category = $("#daily_category").val();
            params.doc_date = $("#doc_date").val();
            return params;
        }
        function ajaxRequest(params) {
            $(".btn-export").hide();
            var url = "{{ route('sr.daily_sales.search') }}";
            $.get(url + '?' + $.param(params.data)).then(function (res) {
                if (res.total > 0) {
                    $(".btn-export").show();
                }
                params.success(res)
            });
        }
        function getSummary() {
            $.ajax({
                url: "{{ route('sr.daily_sales.summary') }}",
                method: 'GET',
                data: {daily_category: $("#daily_category").val(), doc_date: $("#doc_date").val()},
                success: function(res) {
                    if (res.success == true) {
                        $(".summary-text").html('<h4 class="text-success">'+res.summary_text+'</h4>');
                        $(".summary-qty-text").html('<h4 class="text-success">'+res.summary_qty+'</h4>');
                        $(".summary-price-text").html('<h4 class="text-success">'+res.summary_price+'</h4>');
                    }
                }
            });
        }
        function print() {
            var url = '{{ url("sales-report/daily-sales/print") }}';
            $("#exportForm").append('<form action="' + url + '" method="GET" target="_blank">');
            $("#exportForm form").append(
                '<input type="text" name="daily_category" value="' + $("#daily_category").val() + '"/>'
            );
            $("#exportForm form").append(
                '<input type="text" name="doc_date" value="' + $("#doc_date").val() + '"/>'
            );
            $("#exportForm form").submit();
            $("#exportForm").html("");
        }
        function exportExcel() {
            var url = '{{ url("sales-report/daily-sales/export") }}';
            $("#exportForm").append('<form action="' + url + '" method="GET">');
            $("#exportForm form").append(
                '<input type="text" name="daily_category" value="' + $("#daily_category").val() + '"/>'
            );
            $("#exportForm form").append(
                '<input type="text" name="doc_date" value="' + $("#doc_date").val() + '"/>'
            );
            $("#exportForm form").submit();
            $("#exportForm").html("");
        }
        // Get the button
        let btnScrollToTop = document.getElementById("btnScrollToTop");

        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};

        function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            btnScrollToTop.style.display = "block";
        } else {
            btnScrollToTop.style.display = "none";
        }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
        }
    </script>
@endsection
