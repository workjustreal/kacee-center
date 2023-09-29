@extends('layouts.master-nopreloader-layout', ['page_title' => 'จัดการงานซ่อม'])
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
                            <li class="breadcrumb-item active">จัดการงานซ่อม</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- start table -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-bordered nav-justified">
                            @php
                                $leader = '';
                                $active = '';
                                $status_li = '';
                                if (Auth::User()->isLeader()) {
                                    $leader = 'active';
                                    $active = '';
                                    $status_li = 'หัวหน้าอนุมัติ';
                                } else {
                                    $leader = '';
                                    $active = 'active';
                                    $status_li = 'ดำเนินการ';
                                }
                            @endphp
                            <li class="nav-item">
                                <a href="#approve-w" data-bs-toggle="tab" aria-expanded="true"
                                    class="nav-link {{ $leader }}" onclick="changeStatus('หัวหน้าอนุมัติ');">
                                    รอรับงาน
                                    @if ($status_appove > 0 && Auth::User()->isLeader())
                                        <span class="badge rounded-circle"
                                            style="font-size: 1em;font-family: 'Cerebri Sans,sans-serif';background-color:rgb(255, 37, 37);color:#f8f5f5;">
                                            &nbsp;!&nbsp;
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#action-w" data-bs-toggle="tab" aria-expanded="false"
                                    class="nav-link {{ $active }}" onclick="changeStatus('ดำเนินการ');">
                                    ดำเนินการ
                                    {{-- {{$status_action}} --}}
                                    @if ($status_action > 0)
                                        <span class="badge rounded-circle"
                                            style="font-size: 1em;font-family: 'Cerebri Sans,sans-serif';background-color:rgb(255, 37, 37);color:#f8f5f5;">
                                            &nbsp;!&nbsp;
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#report-w" data-bs-toggle="tab" aria-expanded="false" class="nav-link"
                                    onclick="changeStatus('รอตรวจสอบ');">
                                    รอตรวจสอบ
                                    {{-- {{$status_check}} --}}
                                    @if ($status_check > 0 && Auth::User()->isLeader())
                                        <span class="badge rounded-circle"
                                            style="font-size: 1em;font-family: 'Cerebri Sans,sans-serif';background-color:rgb(255, 37, 37);color:#f8f5f5;">
                                            &nbsp;!&nbsp;
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#all-w" data-bs-toggle="tab" aria-expanded="false" class="nav-link"
                                    onclick="changeStatus('');">
                                    ประวัติงานทั้งหมด
                                </a>
                            </li>
                            <input type="hidden" name="status_ul" id="status_ul" value="{{ $status_li }}">
                        </ul>
                        <div class="card">
                            <div class="tab-content">

                                <div class="tab-pane {{ $leader }}" id="approve-w">
                                    <div class="card-header bg-emp bg-soft-primary">
                                        <h4 class="text-white">รอรับงาน</h4>
                                    </div>
                                </div>

                                <div class="tab-pane {{ $active }}" id="action-w">
                                    <div class="card-header bg-emp bg-soft-primary">
                                        <h4 class="text-white">ดำเนินการ</h4>
                                    </div>
                                </div>

                                <div class="tab-pane" id="report-w">
                                    <div class="card-header bg-emp bg-soft-primary">
                                        <h4 class="text-white">รอตรวจสอบ</h4>
                                    </div>
                                </div>

                                <div class="tab-pane" id="all-w">
                                    <div class="card-header bg-emp bg-soft-primary">
                                        <h4 class="text-white">ประวัติงานทั้งหมด</h4>
                                    </div>
                                </div>
                            </div>

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
                                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2" id="status_div"
                                                style="display: none;">
                                                <label for="status_category">สถานะ</label>
                                                <select class="form-select" name="status_category" id="status_category">
                                                    <option value="" id="status_category_empty" selected>ทั้งหมด
                                                    </option>
                                                    <option value="รออนุมัติ">รออนุมัติ</option>
                                                    <option value="หัวหน้าอนุมัติ">รอรับงาน</option>
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
                                                <label for="search">ค้นหา</label>
                                                <input type="text" class="form-control" placeholder="ค้นหา"
                                                    name="search" id="search" value="">
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
                                            <th data-field="order_id" data-sortable="true">รหัสใบแจ้ง</th>
                                            <th data-field="order_dept" data-sortable="true">งานแผนก</th>
                                            <th data-field="order_type" data-sortable="true">ประเภทงาน</th>
                                            <th data-field="user_id" data-sortable="true">ผู้แจ้ง</th>
                                            <th data-field="dept_id" data-sortable="true">ฝ่าย / แผนก (ผู้แจ้ง)</th>
                                            <th data-field="order_date" data-sortable="true">วันที่แจ้ง</th>
                                            <th data-field="withdraw" data-sortable="true">เบิกอุปกรณ์</th>
                                            <th data-field="status" data-sortable="true">สถานะ</th>
                                            <th data-field="action" data-sortable="false" data-width="150">จัดการ</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end table form -->

        <!-- start Modal -->
        <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div id="comment"></div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            onclick="closeModal()"></button>
                    </div>

                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <span class="text-warning" style="font-size: 80px;"><i
                                    class="mdi mdi-alert-circle-outline"></i></span>
                            <h3>คุณต้องการยกเลิกรายการ ใช่ไหม ?</h3>
                        </div>

                        <form action="{{ url('/repair/cancel') }}" class="wow fadeInLeft" method="post">
                            @csrf
                            <div class="col-12">
                                <div class="fom-group mb-4">
                                    <label class="control-label">หมายเหตุ / ความคิดเห็น:</label>
                                    <textarea class="form-control form-control-md" id="manager_detail" name="manager_detail" placeholder="หมายเหตุ"
                                        required="" rows="3" value=""></textarea>
                                </div>

                                <input type="hidden" id="hidden_id" name="id">
                                <input type="hidden" name="page" value="action">

                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary px-4" id="btn-submit">
                                        <i class="fe-save"></i> ดำเนินการ
                                    </button>
                                    <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal"
                                        aria-label="Close" onclick="closeModal()">
                                        <i class="fe-save"></i> ย้อนกลับ
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end Modal -->

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
        var status_li = "{{$status_li}}";
        var startMonth = '';
        var EndMonth = '';
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
                setTimeout(() => {
                    $table.bootstrapTable('refreshOptions', {
                        status_category: $("#status_category").val()
                    });
                }, 200);
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

            if (status_li == 'หัวหน้าอนุมัติ') {
                $table.bootstrapTable('hideColumn', 'withdraw');
            } else {
                $table.bootstrapTable('showColumn', 'withdraw');
            }
        });

        function queryParams(params) {
            setTimeout(() => {
                params.status_category = $("#status_category").val();
                params.doc_date = $("#doc_date").val();
                params.status_ul = $("#status_ul").val();
                params.dept_category = $("#dept_category").val();
            }, 200);
            return params;
        }

        function changeStatus(data) {
            $("#status_ul").val(data);
            if (data == "") {
                setTimeout(() => {
                    var startMonth = moment().startOf('month').format('DD/MM/YYYY');
                    var EndMonth = moment().endOf('month').format('DD/MM/YYYY');
                    document.getElementById("doc_date").value = startMonth + ' ถึง ' + EndMonth;
                    document.getElementById("status_div").style.display = "block";
                    $table.bootstrapTable('showColumn', 'withdraw');
                }, 200);
            } else {
                setTimeout(() => {
                    document.getElementById("status_div").style.display = "none";
                    document.getElementById("doc_date").value = '';

                    if (data == 'หัวหน้าอนุมัติ') {
                        $table.bootstrapTable('hideColumn', 'withdraw');
                    } else {
                        $table.bootstrapTable('showColumn', 'withdraw');
                    }
                }, 200);
            }
            $table.bootstrapTable('refreshOptions', {
                status_ul: data
            });
        }

        function ajaxRequest(params) {
            setTimeout(() => {
                var url = "{{ route('repair.searchAction') }}";
                $.get(url + '?' + $.param(params.data)).then(function(res) {
                    params.success(res)
                });
            }, 200);
        }

        function cancelModal(id) {
            document.getElementById('comment').innerText = 'ยกเลิกใบแจ้งซ่อม : ' + id;
            document.getElementById('hidden_id').value = id;
        }

        function closeModal() {
            document.getElementById('manager_detail').value = "";
        }
    </script>
@endsection
