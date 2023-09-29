@extends('layouts.master-nopreloader-layout', ['page_title' => 'รายการบำรุงรักษา'])
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
        <div class="row mb-2">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Repair</a></li>
                            <li class="breadcrumb-item active">รายการอนุมัติใบแจ้งซ่อม</li>
                        </ol>
                    </div>
                    {{-- <h4 class="page-title">รายการอนุมัติใบแจ้งซ่อม</h4> --}}
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- start table -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card"> 
                    <div class="card-body">
                        <div class="float-end">
                            <div class="row justify-content-between">
                                <div class="col-auto">
                                    <div class="row">
                                        <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                            <label for="search">ค้นหา</label>
                                            <input type="text" class="form-control" placeholder="ค้นหา" name="search"
                                                id="search" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3 >ใบแจ้งซ่อมรออนุมัติ & รอตรวจรับงาน</h3>
                        
                        <table class="table table-striped text-nowrap" id="tableApprove" data-toggle="table"
                            data-loading-template="loadingTemplate" data-buttons-class="btn btn-sm btn-secondary"
                            data-ajax="ajaxRequestApprove" data-query-params="queryParams" data-undefined-text=""
                            data-search="true" data-search-align="left" data-pagination="true"
                            data-search-selector="#search" data-page-size="5">
                            <thead>
                                <tr>
                                    <th data-field="order_id" data-sortable="true">รหัสใบแจ้ง</th>
                                    <th data-field="order_dept" data-sortable="true">งานแผนก</th>
                                    <th data-field="order_type" data-sortable="true">ประเภทงาน</th>
                                    <th data-field="user_id" data-sortable="true">ผู้แจ้ง</th>
                                    <th data-field="dept_id" data-sortable="true">ฝ่าย / แผนก (ผู้แจ้ง)</th>
                                    <th data-field="order_date" data-sortable="true">วันที่แจ้ง</th>
                                    <th data-field="status" data-sortable="true">สถานะ</th>
                                    <th data-field="action" data-sortable="false" data-width="150">จัดการ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="float-end">
                            <div class="row justify-content-between">
                                <div class="col-auto">
                                    <div class="row">
                                        <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                            <label for="doc_date" class="form-label mb-0">วันที่เริ่มต้น - วันที่สิ้นสุด</label>
                                            <div class="form-group">
                                                <input type="text" class="form-control month-datepicker" id="doc_date"
                                                    name="doc_date" placeholder="เลือกเดือน">
                                            </div>
                                        </div>
                                        <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                            <label for="dept_category">แผนกงาน</label>
                                            <select class="form-select" name="dept_category" id="dept_category">
                                                <option value="" id="dept_category_empty">เลือกแผนกงาน</option>
                                                <option value="A03050100">แผนกไฟฟ้าและสุขาภิบาล </option>
                                                <option value="A03050200">แผนกยานยนต์</option>
                                                <option value="A03060100">แผนกซ่อมบำรุง</option>
                                                <option value="A01100100">แผนกไอที</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                            <label for="status_category">สถานะ</label>
                                            <select class="form-select" name="status_category" id="status_category">
                                                <option value="" selected>ทั้งหมด</option>
                                                {{-- <option value="รออนุมัติ">รออนุมัติ</option> --}}
                                                <option value="หัวหน้าอนุมัติ">หัวหน้าอนุมัติ</option>
                                                <option value="ดำเนินการ">ดำเนินการ</option>
                                                <option value="รอตรวจสอบ">รอตรวจสอบ</option>
                                                <option value="ผ่านการตรวจสอบ">ผ่านการตรวจสอบ</option>
                                                <option value="เสร็จสิ้น">เสร็จสิ้น</option>
                                                <option value="ยกเลิกโดยผู้แจ้ง">ยกเลิกโดยผู้แจ้ง</option>
                                                <option value="ยกเลิกโดยหัวหน้า">ยกเลิกโดยผู้อนุมัติ</option>
                                                <option value="ยกเลิกโดยผู้รับงาน">ยกเลิกโดยผู้รับงาน</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                            <label for="searchAll">ค้นหา</label>
                                            <input type="text" class="form-control" placeholder="ค้นหา" name="searchAll"
                                                id="searchAll" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3 >ประวัติทั้งหมด</h3>
                        
                        <table class="table table-striped text-nowrap" id="table" data-toggle="table"
                            data-loading-template="loadingTemplate" data-buttons-class="btn btn-sm btn-secondary"
                            data-ajax="ajaxRequest" data-query-params="queryParams" data-undefined-text=""
                            data-search="true" data-search-align="left" data-pagination="true"
                            data-search-selector="#searchAll" data-page-size="10">
                            <thead>
                                <tr>
                                    <th data-field="order_id" data-sortable="true">รหัสใบแจ้ง</th>
                                    <th data-field="order_dept" data-sortable="true">งานแผนก</th>
                                    <th data-field="order_type" data-sortable="true">ประเภทงาน</th>
                                    <th data-field="user_id" data-sortable="true">ผู้แจ้ง</th>
                                    <th data-field="dept_id" data-sortable="true">ฝ่าย / แผนก (ผู้แจ้ง)</th>
                                    <th data-field="order_date" data-sortable="true">วันที่แจ้ง</th>
                                    <th data-field="status" data-sortable="true">สถานะ</th>
                                    <th data-field="action" data-sortable="false" data-width="150">จัดการ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end table form -->

    </div>
@endsection
@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap-table-loading-pl-style.js')}}"></script>
    <script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/monthSelect/index.js') }}"></script>
    <script src="{{asset('assets/libs/flatpickr/dist/l10n/th.js')}}"></script>
    <script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
    <!-- third party js ends -->
    <script type="text/javascript">
        var $table = $('#table');
        var startMonth = moment().startOf('month').format('DD/MM/YYYY');
        var EndMonth = moment().endOf('month').format('DD/MM/YYYY');
        $(document).ready(function() {
            moment.locale("th-TH");
            flatpickr.localize(flatpickr.l10ns.th);
            $(".month-datepicker").flatpickr({
                disableMobile: "true",
                mode: 'range',
                dateFormat: "d/m/Y",
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
            $("#dept_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    dept_category: $("#dept_category").val()
                });
                rebuild();
            });
        });

        function rmyAvailableDate() {
            list = [];
            date = new Date();
            for (let i=0; i<=12; i++) {
                list.push(date.toISOString().slice(0, 7));
                date.setMonth(date.getMonth() - 1);
            }
            return list.reverse();
        }

        function queryParams(params) {
            params.status_category = $("#status_category").val();
            params.doc_date = $("#doc_date").val();
            params.dept_category = $("#dept_category").val();
            return params;
        }

        function ajaxRequest(params) {
            var url = "{{ route('repair.search') }}";
            $.get(url + '?' + $.param(params.data)).then(function(res) {
                params.success(res)
            });
        }

        function ajaxRequestApprove(params) {
            var url = "{{ route('repair.searchApprove') }}";
            $.get(url + '?' + $.param(params.data)).then(function(res) {
                params.success(res)
            });
        }
        
        function deleteLeaveRecordConfirmation(id) {
            Swal.fire({
                icon: "warning",
                title: "คุณต้องการยกเลิกรายการ ใช่ไหม?", 
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ดำเนินการ!",
                cancelButtonText: "ยกเลิก",
                showLoaderOnConfirm: true,
                stopKeydownPropagation: false,
                preConfirm: () => {
                    return fetch('/repair/cancel', {
                            method: 'POST',
                            headers: {
                                'Content-type': 'application/json; charset=UTF-8',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                id: id,
                                page: 'approve',
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
                    console.log(data);
                },
                allowOutsideClick: () => !Swal.isLoading(),
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: "success",
                        title: "เรียบร้อย!",
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            });
        }
    </script>
@endsection
