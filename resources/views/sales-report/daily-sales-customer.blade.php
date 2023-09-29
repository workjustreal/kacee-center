@extends('layouts.master-layout', ['page_title' => "ยอดขายรายวัน (แยกกลุ่มลูกค้า)"])
@section('css')
<!-- third party css -->
<link href="{{ asset('assets/css/placeholder-loading.min.css') }}" rel="stylesheet">
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
<style>
    .table > tbody > tr > td a.text-fw {
        display: inline-block;
        width: 100%;
    }
    .ph-item,
    .ph-picture {
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0;
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
                    <h4 class="page-title">ยอดขายรายวัน (แยกกลุ่มลูกค้า)</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between mb-2">
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
                                        <label for="type">ประเภท</label>
                                        <select class="form-select" name="type" id="type">
                                            <option value="qty" selected>จำนวน</option>
                                            <option value="price">ยอดขาย(บาท)</option>
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
                                <tr>
                                    <th data-field="category" rowspan="2" data-valign="middle" data-halign="center" data-align="left">กลุ่มสินค้า</th>
                                    <th data-field="summary" rowspan="2" data-valign="middle" data-halign="center" data-align="center">ยอดรวมทั้งหมด</th>
                                    @foreach ($thead["header"] as $value)
                                    <th @if($value["colspan"] > 1) colspan="{{ $value["colspan"] }}" @endif data-align="center">{{ $value["display_name"] }}</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($thead["subheader"] as $value)
                                    <th data-field="{{ $value["name"] }}" data-align="center">{{ $value["display_name"] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
                    rebuild();
                }
            });
            $("#daily_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    daily_category: $("#daily_category").val()
                });
                rebuild();
            });
            $("#type").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    type: $("#type").val()
                });
                rebuild();
            });
            // $("#doc_date").change(function() {
            //     $table.bootstrapTable('refreshOptions', {
            //         doc_date: $("#doc_date").val()
            //     });
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
            params.type = $("#type").val();
            params.doc_date = $("#doc_date").val();
            return params;
        }
        function ajaxRequest(params) {
            $(".btn-export").hide();
            var url = "{{ route('sr.daily_sales_customer.search') }}";
            $.get(url + '?' + $.param(params.data)).then(function (res) {
                if (res.total > 0) {
                    $(".btn-export").show();
                }
                params.success(res)
            });
        }
        function print() {
            var url = '{{ url("sales-report/daily-sales-customer/print") }}';
            $("#exportForm").append('<form action="' + url + '" method="GET" target="_blank">');
            $("#exportForm form").append(
                '<input type="text" name="daily_category" value="' + $("#daily_category").val() + '"/>'
            );
            $("#exportForm form").append(
                '<input type="text" name="type" value="' + $("#type").val() + '"/>'
            );
            $("#exportForm form").append(
                '<input type="text" name="doc_date" value="' + $("#doc_date").val() + '"/>'
            );
            $("#exportForm form").submit();
            $("#exportForm").html("");
        }
        function exportExcel() {
            var url = '{{ url("sales-report/daily-sales-customer/export") }}';
            $("#exportForm").append('<form action="' + url + '" method="GET">');
            $("#exportForm form").append(
                '<input type="text" name="daily_category" value="' + $("#daily_category").val() + '"/>'
            );
            $("#exportForm form").append(
                '<input type="text" name="type" value="' + $("#type").val() + '"/>'
            );
            $("#exportForm form").append(
                '<input type="text" name="doc_date" value="' + $("#doc_date").val() + '"/>'
            );
            $("#exportForm form").submit();
            $("#exportForm").html("");
        }
    </script>
@endsection
