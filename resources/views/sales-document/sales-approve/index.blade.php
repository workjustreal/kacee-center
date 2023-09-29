@extends('layouts.master-layout', ['page_title' => 'อนุมัติการลงสินค้าให้ลูกค้า'])
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
                            <li class="breadcrumb-item active">Sales Approve</li>
                        </ol>
                    </div>
                    <h4 class="page-title">รายการลงสินค้า</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        {{-- <div class="card">
                            <div class="card-header bg-primary text-white"><b>รายการรออนุมัติ</b> </div>
                            <div class="card-body">
                                <div class="row justify-content-between">
                                    <div class="col-auto">
                                        <div class="row">
                                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                                <label for="search">ค้นหา</label>
                                                <input type="text" class="form-control" placeholder="ค้นหา"
                                                    name="searchWait" id="searchWait" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-striped text-nowrap" id="tableWait" data-toggle="table"
                                    data-loading-template="loadingTemplate" data-buttons-class="btn btn-sm btn-secondary"
                                    data-ajax="ajaxRequestWait" data-query-params="queryParams" data-undefined-text=""
                                    data-search="true" data-search-align="left" data-pagination="true"
                                    data-search-selector="#searchWait" data-page-size="5">
                                    <thead>
                                        <tr>
                                            <th data-field="state" data-checkbox="true"></th>
                                            <th data-field="ID" data-visible="false">ID</th>
                                            <th data-field="gen_id" data-sortable="true">รหัสรายการ</th>
                                            <th data-field="customer_code" data-sortable="true">รหัสลูกค้า</th>
                                            <th data-field="customer_name" data-sortable="true">ชื่อร้าน</th>
                                            <th data-field="invoice" data-sortable="true">เลขที่ IV</th>
                                            <th data-field="pay" data-sortable="true">ยอดเงิน</th>
                                            <th data-field="emp_id" data-sortable="true">ผู้ลงบันทึก</th>
                                            <th data-field="created_at" data-sortable="true">วันที่ลงบันทึก</th>
                                            <th data-field="status" data-sortable="true">สถานะ</th>
                                            <th data-field="action" data-sortable="false">จัดการ</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="mt-2 mb-1 pt-2">
                                    <button type="button" class="btn btn-primary" id="btnApprove" disabled>อนุมัติ</button>
                                </div>
                            </div>
                        </div> --}}

                        <div class="card">
                            {{-- <div class="card-header bg-blue text-white"><h4>รายการลงสินค้า</h4> </div> --}}
                            <div class="card-body">
                                <div class="row justify-content-between">
                                    <div class="col-auto">
                                        <div class="row">
                                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                                <label for="doc_date" class="form-label mb-0">เดือน/ปี</label>
                                                <div class="form-group">
                                                    <input type="text" class="form-control month-datepicker"
                                                        id="doc_date" name="doc_date" placeholder="เลือกเดือน">
                                                </div>
                                            </div>
                                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                                <label for="status_category">สถานะ</label>
                                                <select class="form-select" name="status_category" id="status_category">
                                                    <option value="" selected>ทั้งหมด</option>
                                                    <option value="รออนุมัติ">รอรับทราบ</option>
                                                    <option value="เสร็จสิ้น">เสร็จสิ้น</option>
                                                    {{-- <option value="อนุมัติ">อนุมัติ</option> --}}
                                                    <option value="ยกเลิก">ยกเลิก</option>
                                                    {{-- <option value="ยกเลิกโดยอนุมัติ">ยกเลิกโดยหัวหน้า</option> --}}
                                                </select>
                                            </div>

                                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                                <label for="search">ค้นหา</label>
                                                <input type="text" class="form-control" placeholder="ค้นหา"
                                                    name="search" id="search" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                            <th data-field="emp_id" data-sortable="true">ผู้ลงบันทึก</th>
                                            <th data-field="emp_dept_id" data-sortable="true">ฝ่าย/แผนกผู้บันทึก</th>
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
        var $tableWait = $('#tableWait');
        var objData = [];
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
            $("#btnApprove").click(function() {
                approveConfirmation();
            });
            $tableWait.on('check-all.bs.table', function(e, rowsAfter, rowsBefore) {
                objData = [];
                if (rowsAfter.length > 0) {
                    for (var i = 0; i < rowsAfter.length; i++) {
                        objData.push(rowsAfter[i].ID);
                    }
                }
                toggleBtnApprove();
                console.log(objData);
            });
            $tableWait.on('check.bs.table', function(e, row, $element) {
                objData.push(row.ID);
                toggleBtnApprove();
                console.log(objData);
            });
            $tableWait.on('uncheck-all.bs.table', function(e, rowsAfter, rowsBefore) {
                objData = [];
                toggleBtnApprove();
                console.log(objData);
            });
            $tableWait.on('uncheck.bs.table', function(e, row, $element) {
                if (objData.length > 0) {
                    for (var i = 0; i < objData.length; i++) {
                        if (objData[i] === row.ID) {
                            objData.splice(i, 1);
                        }
                    }
                }
                toggleBtnApprove();
                console.log(objData);
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

        function ajaxRequestWait(params) {
            var url = "{{ route('sd.sales_approve.search') }}";
            $.get(url + '?' + $.param(params.data)).then(function(res) {
                params.success(res)
            });
        }

        function ajaxRequest(params) {
            var url = "{{ route('sd.sales_all.search') }}";
            $.get(url + '?' + $.param(params.data)).then(function(res) {
                params.success(res)
            });
        }

        function stateFormatter(value, row, index) {
        // if (row.leave_status !== 'A2') {
        //     return {
        //         disabled: true
        //     }
        // }
            return value;
        }

        function toggleBtnApprove() {
            $("#btnApprove").prop("disabled", !objData.length);
        }

        function approveConfirmation() {
            let url = "{{ route('sd.sales_approve.submit') }}";
            Swal.fire({
                icon: "warning",
                title: "ยืนยันอนุมัติรายการ ใช่ไหม?",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ดำเนินการ!",
                cancelButtonText: "ยกเลิก",
                showLoaderOnConfirm: true,
                stopKeydownPropagation: false,
                preConfirm: () => {
                    return fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-type': 'application/json; charset=UTF-8',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                'id': objData
                            }),
                        })
                        .then(function(response) {
                            if (!response.ok) {
                                throw new Error(response.statusText);
                            }
                            return response.json();
                        })
                        .then(function(data) {
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
                        title: "อนุมัติรายการเรียบร้อย!",
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                    // objData.shift();
                    // $table.bootstrapTable("refresh");
                    // rebuild();
                }
            });
        }

        function cancelConfirmation(id, page) {
            let url = '/sales-document/sales-form/cancel/' + id + '/' + page;
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
                    }, 1500);
                }
            });
        }
    </script>
@endsection
