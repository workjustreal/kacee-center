@extends('layouts.master-layout', ['page_title' => 'การลงสินค้าให้ลูกค้า'])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/css/placeholder-loading.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/monthSelect/style.css') }}" rel="stylesheet" />
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                            <li class="breadcrumb-item active">Sales Form</li>
                        </ol>
                    </div>
                    <h4 class="page-title">การลงสินค้าให้ลูกค้า 
                        <span style="font-size: 14px">
                            <a href="{{ asset('assets/files/manual/คู่มือระบบ Sales Doc (ฝ่ายขาย).pdf') }}" target="_blank" rel="noopener noreferrer">
                                ( <i class="mdi mdi-file-document-outline text-primary"></i> คู่มือระบบ Sales Doc ฝ่ายขาย)
                            </a>
                        </span>
                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header bg-primary text-white"><b>รายการส่วนบุคคล</b> </div>
                    <div class="card-body">
                        <div class="float-end">
                            <a href="{{ url('/sales-document/sales-form/create') }}"
                                class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus-circle me-1"></i> เพิ่มรายการ
                            </a>
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
                                        <label for="status_category">สถานะ</label>
                                        <select class="form-select" name="status_category" id="status_category">
                                            <option value="" selected>ทั้งหมด</option>
                                            <option value="รออนุมัติ">รอรับทราบ</option>
                                            {{-- <option value="อนุมัติ">อนุมัติ</option> --}}
                                            <option value="เสร็จสิ้น">เสร็จสิ้น</option>
                                            <option value="ยกเลิก">ยกเลิก</option>
                                            {{-- <option value="ยกเลิกโดยหัวหน้า">ยกเลิกโดยผู้อนุมัติ</option> --}}
                                        </select>
                                    </div>

                                    <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                        <label for="search">ค้นหา</label>
                                        <input type="text" class="form-control" placeholder="ค้นหา" name="search"
                                            id="search" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div id="exportForm" class="hidd"></div> --}}
                        <table class="table table-striped text-nowrap" id="table" data-toggle="table"
                            data-loading-template="loadingTemplate" data-buttons-class="btn btn-sm btn-secondary"
                            data-ajax="ajaxRequest" data-query-params="queryParams" data-undefined-text=""
                            data-search="true" data-search-align="left" data-pagination="true"
                            data-search-selector="#search" data-page-size="10">
                            <thead>
                                <tr>
                                    <th data-field="gen_id" data-sortable="true">รหัสรายการ</th>
                                    <th data-field="customer_code" data-sortable="true">รหัสลูกค้า</th>
                                    <th data-field="customer_name" data-sortable="true">ชื่อร้าน</th>
                                    <th data-field="invoice" data-sortable="true">เลขที่ IV</th>
                                    <th data-field="pay" data-sortable="true">ยอดเงิน</th>
                                    <th data-field="created_at" data-sortable="true">วันที่ลงบันทึก</th>
                                    <th data-field="status" data-sortable="true">สถานะ</th>
                                    <th data-field="action" data-sortable="false">จัดการ</th>
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
    <script src="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/bootstrap-tables.init.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-table-style.js') }}"></script>

    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/monthSelect/index.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
    <!-- third party js ends -->

    <script type="text/javascript">
        var $table = $('#table');
        $(document).ready(function() {
            moment.locale("th-TH");
            flatpickr.localize(flatpickr.l10ns.th);
            $(".month-datepicker").flatpickr({
                disableMobile: "true",
                dateFormat: "m/Y",
                defaultDate:  moment().format('MM/YYYY'), 
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: "m/Y",
                        altFormat: "F Y",
                        theme: "light"
                    })
                ],
                // disable: [
                //     function(dateObject) {
                //         var enabledDates = rmyAvailableDate();
                //         var date = dateObject;
                //         date.setDate(date.getDate() + 1);
                //         if (enabledDates.includes(date.toISOString().slice(0, 7))) {
                //             return false;
                //         }
                //         return true;
                //     }
                // ],
                onReady: function(dateObj, dateStr, instance) {
                    const $clear = $(
                            '<div class="flatpickr-clear"><button class="btn btn-sm btn-link">Clear</button></div>'
                        )
                        .on("click", () => {
                            instance.clear();
                            instance.close();
                        })
                        .appendTo($(instance.calendarContainer));
                },
                onClose: function(selectedDates, dateStr, instance) {
                    $(instance.input).blur();
                }
            });
            $("#status_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    status_category: $("#status_category").val()
                });
                rebuild();
            });
            $("#doc_date").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    doc_date: $("#doc_date").val()
                });
                rebuild();
            });
        });

        function rmyAvailableDate() {
            list = [];
            date = new Date();
            for (let i = 0; i <= 12; i++) {
                list.push(date.toISOString().slice(0, 7));
                date.setMonth(date.getMonth() - 1);
            }
            return list.reverse();
        }

        function queryParams(params) {
            params.status_category = $("#status_category").val();
            params.doc_date = $("#doc_date").val();
            return params;
        }

        function ajaxRequest(params) {
            var url = "{{ route('sd.sales_form.search') }}";
            $.get(url + '?' + $.param(params.data)).then(function(res) {
                params.success(res)
            });
        }

        function cancelConfirmation(id, page) {
            Swal.fire({
                icon: "warning",
                title: "คุณต้องการยกเลิกข้อมูล ใช่ไหม?",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ดำเนินการ!",
                cancelButtonText: "ยกเลิก",
                showLoaderOnConfirm: true,
                stopKeydownPropagation: false,
                preConfirm: () => {
                    let url = '/sales-document/sales-form/cancel/' + id + '/' + page;
                    return fetch(url)
                        .then((response) => {
                            if (!response.ok) {
                                throw new Error(response.statusText);
                            }
                            return response.json();
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
                        title: "ยกเลิกข้อมูลเรียบร้อย!",
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            });
        }
    </script>
@endsection
