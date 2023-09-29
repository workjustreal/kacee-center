@extends('layouts.master-layout', ['page_title' => 'รายการลงสินค้าทั้งหมด'])
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
                            <li class="breadcrumb-item active">Sales List</li>
                        </ol>
                    </div>
                    {{-- <h4 class="page-title">รายการลงสินค้าทั้งหมด</h4> --}}
                    <h4 class="page-title">การลงสินค้าให้ลูกค้า 
                        <span style="font-size: 14px">
                            <a href="{{ asset('assets/files/manual/คู่มือระบบ Sales Doc (ฝ่ายขนส่ง).pdf') }}" target="_blank" rel="noopener noreferrer">
                                ( <i class="mdi mdi-file-document-outline text-primary"></i> คู่มือระบบ Sales Doc ฝ่ายขนส่ง)
                            </a>
                        </span>
                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-sm-12">
                @php $_user = auth()->user();@endphp
                @if (Auth::User()->roleAdmin() ||
                        $_user->dept_id == 'A03000000' ||
                        $_user->dept_id == 'A03040000' ||
                        ($_user->dept_id == 'A03040200' && Auth::User()->isLeader()))
                    <div class="card">
                        <div class="card-header bg-primary text-white"><b>รอรับทราบ</b></div>
                        <div class="card-body">
                            <div class="row justify-content-between">
                                <div class="col-auto">
                                    <div class="row">
                                        <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                            <label for="search">ค้นหา</label>
                                            <input type="text" class="form-control" placeholder="ค้นหา"
                                                name="searchAcknowledge" id="searchAcknowledge" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped text-nowrap" id="tableAcknowledge" data-toggle="table"
                                data-loading-template="loadingTemplate" data-buttons-class="btn btn-sm btn-secondary"
                                data-ajax="ajaxRequestAcknowledge" data-query-params="queryParams" data-undefined-text=""
                                data-search="true" data-search-align="left" data-pagination="true"
                                data-search-selector="#searchAcknowledge" data-page-size="5">
                                <thead>
                                    <tr>
                                        <th data-field="state" data-checkbox="true"></th>
                                        <th data-field="ID" data-visible="false">ID</th>
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
                            <button type="button" class="btn btn-primary mt-2" id="btnAcknowledge"
                                disabled>รับทราบ</button>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header bg-blue text-white"><b>พร้อมปริ้น</b> </div>
                    <div class="card-body">
                        <div class="float-end">
                            {{-- <button type="button" class="btn btn-primary" id="btnApprove" disabled>Print</button> --}}
                            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                                data-bs-target="#printModal" id="btnPrint" disabled>
                                <i class="mdi mdi-printer me-1"></i> Print
                            </button>
                        </div>
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <div class="row">
                                    <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                        <label for="doc_date" class="form-label mb-0">วันที่รับทราบเริ่มต้น - สิ้นสุด</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control month-datepicker" id="doc_date"
                                                name="doc_date" placeholder="เลือกเดือน">
                                        </div>
                                    </div>

                                    <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                        <label for="status_category">พิมพ์</label>
                                        <select class="form-select" name="status_category" id="status_category">
                                            <option value="" selected>ทั้งหมด</option>
                                            <option value="1">ยังไม่ได้พิมพ์</option>
                                            <option value="2">พิมพ์แล้ว</option>
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
                        <table class="table table-striped text-nowrap" id="table" data-toggle="table"
                            data-loading-template="loadingTemplate" data-buttons-class="btn btn-sm btn-secondary"
                            data-ajax="ajaxRequest" data-query-params="queryParams" data-undefined-text=""
                            data-search="true" data-search-align="left" data-pagination="true"
                            data-search-selector="#search" data-page-size="10">
                            <thead>
                                <tr>
                                    <th data-field="state" data-checkbox="true" data-formatter="stateFormatter"></th>
                                    <th data-field="ID" data-visible="false">ID</th>
                                    <th data-field="_STATUS" data-visible="false">STATUS</th>
                                    <th data-field="gen_id" data-sortable="true">รหัสรายการ</th>
                                    <th data-field="customer_code" data-sortable="true">รหัสลูกค้า</th>
                                    <th data-field="customer_name" data-sortable="true">ชื่อร้าน</th>
                                    <th data-field="invoice" data-sortable="true">เลขที่ IV</th>
                                    <th data-field="emp_id" data-sortable="true">ผู้ลงบันทึก</th>
                                    <th data-field="emp_dept_id" data-sortable="true">ฝ่าย/แผนกผู้บันทึก</th>
                                    <th data-field="created_at" data-sortable="true">วันที่ลงบันทึก</th>
                                    <th data-field="count_print" data-sortable="true">ครั้งพิมพ์</th>
                                    <th data-field="status" data-sortable="true">สถานะ</th>
                                    <th data-field="action" data-sortable="false">จัดการ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <!-- start modal -->
                <div id="printModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" role="dialog" aria-labelledby="printModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        {{-- <div class="modal-dialog modal-lg modal-dialog-centered"> --}}
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="printModalLabel">พิมพ์ใบลงสินค้าให้ลูกค้า KACEE</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <embed src="" id='pdf' frameborder="0" width="100%" height="600px">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end modal -->

                <div class="modal fade" id="logPrintModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="text-center">
                                    <div class="get-data"></div>
                                    {{-- <i class="dripicons-information h1 text-info"></i>
                                    <h4 class="mt-2">จำนวนครั้งที่พิมพ์</h4>
                                    <p>หมายเลขใบลงสินค้า : SF23040005</p>
                                    <hr/>
                                    <p>เลขที่ IV : IV8555555</p>
                                    <p>รหัสลูกค้า : SH55555555</p>
                                    <p>ชื่อร้าน : ร้าน 555555555555555 ช็อป</p>
                                    <p>ครั้งที่: 1 ผู้พิมพ์: อรนุช เวลา: 24/04/2023 19:46:19</p>
                                    <button type="button" class="btn btn-info my-2" data-bs-dismiss="modal">OK</button> --}}
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
        var $tableA = $('#tableAcknowledge');
        var objData = [];
        var objDataA = [];
        var startMonth = moment().subtract(1, "days").format('DD/MM/YYYY');
        var EndMonth = moment().format('DD/MM/YYYY');
        $(document).ready(function() {
            moment.locale("th-TH");
            flatpickr.localize(flatpickr.l10ns.th);
            $(".month-datepicker").flatpickr({
                disableMobile: "true",
                mode: 'range',
                dateFormat: "d/m/Y",
                // maxDate: 'today',
                defaultDate: [startMonth, EndMonth],
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
                    $table.bootstrapTable('refreshOptions', {
                        doc_date: $("#doc_date").val()
                    });
                    rebuild();
                }
            });
            $("#btnPrint").click(function() {
                $("#pdf").attr("src", "{{ url('sales-document/sales-form/print') }}/" + objData.toString());
                let _url = "{{ route('sd.sales_list.printLog') }}";
                $.ajax({
                    type: "GET",
                    url: _url,
                    data: {
                        'id': objData.toString()
                    },
                });

            });
            $("#btnAcknowledge").click(function() {
                acknowledgeConfirmation();
            });
            $("#status_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    status_category: $("#status_category").val()
                });
                rebuild();
            });

            $table.on('check-all.bs.table', function(e, rowsAfter, rowsBefore) {
                objData = [];
                if (rowsAfter.length > 0) {
                    for (var i = 0; i < rowsAfter.length; i++) {
                        objData.push(rowsAfter[i].ID);
                    }
                }
                toggleBtnPrint();
            });
            $table.on('check.bs.table', function(e, row, $element) {
                objData.push(row.ID);
                toggleBtnPrint();
            });
            $table.on('uncheck-all.bs.table', function(e, rowsAfter, rowsBefore) {
                objData = [];
                toggleBtnPrint();
            });
            $table.on('uncheck.bs.table', function(e, row, $element) {
                if (objData.length > 0) {
                    for (var i = 0; i < objData.length; i++) {
                        if (objData[i] === row.ID) {
                            objData.splice(i, 1);
                        }
                    }
                }
                toggleBtnPrint();
            });

            $tableA.on('check-all.bs.table', function(e, rowsAfter, rowsBefore) {
                objDataA = [];
                if (rowsAfter.length > 0) {
                    for (var i = 0; i < rowsAfter.length; i++) {
                        objDataA.push(rowsAfter[i].ID);
                    }
                }
                toggleBtnAcknowledge();
                console.log(objDataA);
            });
            $tableA.on('check.bs.table', function(e, row, $element) {
                objDataA.push(row.ID);
                toggleBtnAcknowledge();
                console.log(objDataA);
            });
            $tableA.on('uncheck-all.bs.table', function(e, rowsAfter, rowsBefore) {
                objDataA = [];
                toggleBtnAcknowledge();
                console.log(objDataA);
            });
            $tableA.on('uncheck.bs.table', function(e, row, $element) {
                if (objDataA.length > 0) {
                    for (var i = 0; i < objDataA.length; i++) {
                        if (objDataA[i] === row.ID) {
                            objDataA.splice(i, 1);
                        }
                    }
                }
                toggleBtnAcknowledge();
                console.log(objDataA);
            });
        });

        function stateFormatter(value, row, index) {
            if (row._STATUS == 'ยกเลิกโดยผู้ดูแลระบบ') {
                return {
                    disabled: true
                }
            }
            return value;
        }

        function logPrint(id) {
            let _url = "{{ route('sd.sales_list.showPrintLog') }}";
            $.ajax({
                type: "GET",
                url: _url,
                data: { 'id': id },
                success: function(response) {
                    let user = '';
                    user += '<i class="dripicons-information h1 text-info"></i><h4 class="mt-2">จำนวนครั้งที่พิมพ์</h4>';
                    user += '<p>หมายเลขรายการ : ' + response.data.gen_id + '</p><hr/>';
                    user += '<p>เลขที่ IV : ' + response.data.invoice + '</p>';
                    user += '<p>รหัสลูกค้า : ' + response.data.customer_code +'</p>';
                    user += '<p>ชื่อร้าน : ' + response.data.customer_name +'</p>';
                    response.logdata.forEach((element, index) => {
                        console.log(index + 1);
                        user += '<p><span>ครั้งที่: ' + (index + 1) + ' ';
                        user += 'ผู้พิมพ์: <u>' + element.emp_id + '</u> ';
                        user += 'เวลา: <u>' + element.created_at;
                        user += '</u></span></p>';
                    });

                    user += '<button type="button" class="btn btn-info my-2" data-bs-dismiss="modal">OK</button>';
                    $('.get-data').html(user); 
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }

        function toggleBtnPrint() {
            $("#btnPrint").prop("disabled", !objData.length);
            // console.log(objData);
        }

        function toggleBtnAcknowledge() {
            $("#btnAcknowledge").prop("disabled", !objDataA.length);
            // console.log(objData);
        }

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
            var url = "{{ route('sd.sales_list.search') }}";
            $.get(url + '?' + $.param(params.data)).then(function(res) {
                params.success(res)
            });
        }

        function ajaxRequestAcknowledge(params) {
            var url = "{{ route('sd.sales_list.searchAcknowledge') }}";
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

        function acknowledgeConfirmation() {
            let url = "{{ route('sd.sales_list.submit') }}";
            Swal.fire({
                icon: "warning",
                title: "ยืนยันรับทราบรายการ ใช่ไหม?",
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
                                'id': objDataA
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
    </script>
@endsection
