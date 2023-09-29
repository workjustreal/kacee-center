@extends('layouts.master-layout', ['page_title' => "ยอดขายรายวัน (10 อันดับแรก)"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
<style>
    div.dt-buttons {
        position: relative;
        float: right;
    }
    .dataTables_filter > label {
        display: none;
    }
    tbody tr td.dt-control {
        cursor: pointer;
    }
    @keyframes spinner {
        to {transform: rotate(360deg);}
    }
    .spinner:before {
        content: '';
        box-sizing: border-box;
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin-top: -10px;
        margin-left: -10px;
        border-radius: 50%;
        border: 2px solid #ccc;
        border-top-color: #333;
        animation: spinner .6s linear infinite;
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
                    <h4 class="page-title">ยอดขายรายวัน (10 อันดับแรก)</h4>
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
                        <div class="table-responsive">
                            <table id="table" class="display dataTable table nowrap w-100">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>อันดับ</th>
                                        <th>ชื่อร้านค้า</th>
                                        <th>รหัสร้านค้า</th>
                                        <th>จำนวน</th>
                                        <th>ยอดเงิน (บาท)</th>
                                        <th>header</th>
                                    </tr>
                                </thead>
                            </table>
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
    <script src="{{asset('assets/libs/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
    <script src="{{asset('assets/libs/flatpickr/dist/l10n/th.js')}}"></script>
    <script src="{{asset('assets/js/calendar/moment.min.js')}}"></script>
    <script src="{{asset('assets/js/calendar/moment-with-locales.js')}}"></script>
    <!-- third party js ends -->
    <script type="text/javascript">
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
                    table.ajax.reload();
                }
            });
            var table = $('#table').DataTable({
                paging: false,
                pageLength: false,
                info: false,
                ajax: {
                    url: "{{ route('sr.daily_sales_top10.search') }}",
                    dataSrc: '',
                    data: function(d) {
                        d.daily_category = $("#daily_category").val(),
                        d.doc_date = $("#doc_date").val()
                    },
                },
                processing: true,
                language: {
                    loadingRecords: '&nbsp;',
                    processing: '<div class="spinner"></div>'
                },
                columns: [
                    {
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: '<i class="mdi mdi-plus-circle text-success"></i>',
                    },
                    { data: 'no' },
                    { data: 'cusnam' },
                    { data: 'cuscod' },
                    { data: 'qty_total' },
                    { data: 'price_total' },
                    {
                        data: 'headers',
                        visible: false,
                        render: function(data){
                            return data.join('');
                        }
                    },
                ],
                // order: [[1, 'asc']],
            });
            var timeout = null;
            $('#search').on('keyup', function() {
                var search = this.value;
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    table.search( search ).draw();
                }, 500);
            });
            // Add event listener for opening and closing details
            $('#table tbody').on('click', 'td.dt-control', function () {
                var tr = $(this).closest('tr');
                var tr_td = $(this).closest('tr > td.dt-control');
                var row = table.row(tr);
                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    tr.removeClass('bg-light');
                    tr_td.html('<i class="mdi mdi-plus-circle text-success"></i>');
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                    tr.addClass('bg-light');
                    tr_td.html('<i class="mdi mdi-minus-circle text-danger"></i>');
                }
            });
            $("#daily_category").change(function() {
                table.ajax.reload();
            });
            // $("#doc_date").change(function() {
            //     table.ajax.reload();
            // });
        });
        function rmyAvailableDate() {
            const rdatedData = $("#available_date").val().split(",");
            return rdatedData;
        }
        function format(d) {
            var html = '<div class="bg-light"><div style="max-width: 75%;"><table class="display dataTable table table-sm nowrap">';
            for (var i=0; i<d.headers.length; i++) {
                html += '<tr>' +
                '<th class="text-end"></th>' +
                '<th colspan="2">' +
                d.headers[i].doc_num + ' / ผู้รับออเดอร์ - ' + d.headers[i].shortnam +
                '</th>' +
                '<th>' +
                // d.headers[i].qty_total +
                // '</th>' +
                '<th>' +
                // d.headers[i].price_total +
                // '</th>' +
                '</tr>';
                for (var j=0; j<d.headers[i].items.length; j++) {
                    html += '<tr>' +
                    '<td class="text-end"></td>' +
                    '<td>รหัสสินค้า: ' +
                    d.headers[i].items[j].stkcod +
                    '</td>' +
                    '<td>' +
                    d.headers[i].items[j].stkdes +
                    '</td>' +
                    '<td>' +
                    d.headers[i].items[j].qty_total +
                    '</td>' +
                    '<td>' +
                    d.headers[i].items[j].price_total +
                    '</td>' +
                    '</tr>';
                }
            }
            html += '</table></div></div>';
            return html;
        }
        function print() {
            var url = '{{ url("sales-report/daily-sales-top10/print") }}';
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
            var url = '{{ url("sales-report/daily-sales-top10/export") }}';
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
    </script>
@endsection
