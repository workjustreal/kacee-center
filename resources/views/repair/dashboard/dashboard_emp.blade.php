@extends('layouts.master-nopreloader-layout', ['page_title' => 'Dashboard รายบุคคล'])
@section('css')
    <!-- third party css -->
    {{-- <link href="{{ asset('assets/css/placeholder-loading.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/monthSelect/style.css') }}" rel="stylesheet" /> --}}
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Repair</a></li>
                            <li class="breadcrumb-item active">สรุปงานแจ้งซ่อมรายบุคคล</li>
                        </ol>
                    </div>
                    <h4 class="page-title">สรุปงานแจ้งซ่อมรายบุคคล</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- start table -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between mb-2">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        @php
                                            $_status = '';
                                            $emp = Auth::User()->findEmployee($emp_id);
                                            $fullname = $emp->title . ' ' . $emp->name . ' ' . $emp->surname;
                                            $fullname .= $emp->nickname ? ' ( ' . $emp->nickname . ' )' : '';
                                        @endphp
                                        <h4>พนักงาน : <span class="text-primary">{{ $fullname }}</span></h4>
                                        <h4>เดือนปี : <span
                                                class="text-primary">{{ \Carbon\Carbon::parse($date)->thaidate('F Y') }}</span>
                                        </h4>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <h4>สถานะ : <span class="text-primary">{{ $status }}</span></h4>
                                        <h4>จำนวนงาน : <span class="text-primary">{{ $countList }} งาน</span></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive ">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>รหัสใบแจ้ง</th>
                                        <th>สถานที่ซ่อม</th>
                                        <th>ประเภทงาน</th>
                                        <th>อุปกรณ์ที่แจ้ง</th>
                                        <th>วันที่แจ้ง</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records as $rows)
                                        @php
                                            switch ($rows->status) {
                                                case 'รออนุมัติ':
                                                    $_status = '<span class="badge bg-warning">รออนุมัติ</span>';
                                                    break;
                                                case 'หัวหน้าอนุมัติ':
                                                    $_status = '<span class="badge bg-info">หัวหน้าอนุมัติ</span>';
                                                    break;
                                                case 'ดำเนินการ':
                                                    $_status = '<span class="badge bg-secondary">ดำเนินการ</span>';
                                                    break;
                                                case 'รอตรวจสอบ':
                                                    $_status = '<span class="badge bg-primary">รอตรวจสอบ</span>';
                                                    break;
                                                case 'ผ่านการตรวจสอบ':
                                                    $_status = '<span class="badge bg-blue">ผ่านการตรวจสอบ</span>';
                                                    break;
                                                case 'เสร็จสิ้น':
                                                    $_status = "<span class='badge bg-success'>เสร็จสิ้น</span>";
                                                    break;
                                                case 'ยกเลิกโดยผู้แจ้ง':
                                                    $_status = '<span class="badge bg-danger">ยกเลิก</span>';
                                                    break;
                                                case 'ยกเลิกโดยหัวหน้า':
                                                    $_status = '<span class="badge bg-danger">ยกเลิก</span>';
                                                    break;
                                                case 'ยกเลิกโดยผู้รับงาน':
                                                    $_status = '<span class="badge bg-danger">ยกเลิก</span>';
                                                    break;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>
                                                <a href="{{ url('repair/show', $rows->order_id) }}" title="ดูรายละเอียด">
                                                    <i class="mdi mdi-clipboard-list-outline"></i>
                                                    {{ $rows->order_id }}
                                                </a>
                                            </td>
                                            <td>{{ $rows->order_address }}</td>
                                            <td>{{ $rows->order_type }}</td>
                                            <td>{{ $rows->order_tool }}</td>
                                            <td>{{ \Carbon\Carbon::parse($rows->order_date)->thaidate('j M Y') }}</td>
                                            <td>@php echo $_status; @endphp</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>


                        <button type="button" class="btn btn-secondary waves-effect waves-light me-2"
                            onclick="javascript:window.history.back();">
                            <i class="mdi mdi-keyboard-backspace me-1"></i> Back</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end table form -->

    </div>
@endsection
@section('script')
    <!-- third party js -->
    {{-- <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/bootstrap-tables.init.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-table-style.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/monthSelect/index.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script> --}}

    <script type="text/javascript">
        // $(document).ready(function() {
        //     var $table = $('#table');
        //     let date = new Date();
        //     let formattedDate = date.getFullYear() + '-' + (date.getMonth() + 1).toString().padStart(2, '0'); 
        //     flatpickr.localize(flatpickr.l10ns.th);
        //     $(".month-datepicker").flatpickr({
        //         altInput: true,
        //         disableMobile: "true",
        //         dateFormat: "Y-m",
        //         defaultDate: formattedDate, 
        //         plugins: [
        //             new monthSelectPlugin({
        //                 shorthand: true,
        //                 dateFormat: "Y-m",
        //                 altFormat: "F Y",
        //                 theme: "light"
        //             })
        //         ],
        //         onReady: function(dateObj, dateStr, instance) {
        //             const $clear = $(
        //                     '<div class="flatpickr-clear"><button class="btn btn-sm btn-link">Clear</button></div>'
        //                 )
        //                 .on("click", () => {
        //                     instance.clear();
        //                     instance.close();
        //                 }).appendTo($(instance.calendarContainer));
        //         },
        //         onClose: function(selectedDates, dateStr, instance) {
        //             $(instance.input).blur();
        //         }
        //     });
        //     $("#doc_date").change(function() {
        //         setTimeout(() => {
        //             $table.bootstrapTable('refreshOptions', {
        //                 doc_date: $("#doc_date").val()
        //             });
        //         }, 200);
        //     });
        // });

        // function queryParams(params) {
        //     setTimeout(() => {
        //         params.status_category = $("#status_category").val();
        //         params.doc_date = $("#doc_date").val();
        //     }, 200);
        //     return params;
        // }

        // function ajaxRequest(params) {
        //     var url = "{{ route('repair.dashboard.searchEmp') }}";
        //     setTimeout(() => {
        //         $.get(url + '?' + $.param(params.data)).then(function(res) {
        //             params.success(res)
        //         });
        //     }, 200);
        // }
    </script>
@endsection
