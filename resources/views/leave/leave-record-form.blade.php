@extends('layouts.master-layout', ['page_title' => 'บันทึกวันทำงานฝ่ายขาย'])
@section('css')
    <link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/ladda/ladda.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/monthSelect/style.css') }}" rel="stylesheet" />
    <style>
        .form-switch .form-check-input {
            height: 24px !important;
            width: 40px !important;
            background-color: #fd5d49 !important;
            border-color: #ffffff !important;
        }
        .form-switch .form-check-input:checked {
            background-color: #26b99a !important;
            border-color: #ffffff !important;
        }
        .form-switch .form-check-input {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='white'/%3e%3c/svg%3e") !important;
        }
        .form-switch .form-check-input:focus {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='white'/%3e%3c/svg%3e") !important;
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Kacee</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Leave</a></li>
                            <li class="breadcrumb-item active">บันทึกวันทำงานฝ่ายขาย</li>
                        </ol>
                    </div>
                    <h4 class="page-title">บันทึกวันทำงานฝ่ายขาย</h4>
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
                                <div class="d-flex d-inline">
                                    <div class="mb-3 me-2">
                                        <label for="dept" class="form-label">เลือกฝ่าย</label>
                                        <select class="form-select" aria-label=".form-select-sm" id="dept"
                                            name="dept" required>
                                            <option value="" selected="selected" disabled>-</option>
                                            @foreach ($department as $list)
                                                <option value="{{ $list->dept_id }}">
                                                    {{ $list->dept_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="month" class="form-label">เลือกเดือน</label>
                                        <input type="text" class="form-control month-datepicker" id="month"
                                            name="month" placeholder="เลือกเดือน" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="mb-2 text-sm-end">
                                    <br>
                                    <button type="button" class="btn btn-soft-secondary waves-effect waves-light hidd" id="btnDownload" onclick="downloadForm();"><i class="mdi mdi-download me-1"></i>ดาวน์โหลดแบบฟอร์ม</button>
                                </div>
                            </div>
                        </div>
                        <form class="form-horizontal" id="data-form" name="data-form" action="" method="POST"
                            enctype="multipart/form-data">
                            <div class="text-center" id="tableLoading"></div>
                            <div id="divTable" class="table-responsive">
                            </div>
                        </form>
                        <div id="divNavigation" style="display: none;">
                            <div class="mt-3 mb-3 text-center d-flex justify-content-center">
                                <a href="{{ url('leave/leave-record') }}" id="btnBack" class="btn btn-soft-secondary waves-effect waves-light me-3"><i class="mdi mdi-keyboard-backspace me-1"></i>ย้อนกลับ</a>
                                <div>
                                    <button id="loading" name="loading" class="btn btn-primary" type="button" style="display: none;" disabled>
                                        <span class="spinner-border spinner-border-sm me-1" role="status"
                                            aria-hidden="true"></span>
                                        กำลังบันทึก...
                                    </button>
                                    <button type="button" id="submit" name="submit" class="ladda-button btn btn-primary" style="display: none;"
                                        dir="ltr" data-style="zoom-out" title="SAVE" onclick="saveData();"><i class="mdi mdi-content-save me-1"></i>บันทึก</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="downloadForm" class="d-none"></div>
    </div>
@endsection
@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/bootstrap-tables.init.js') }}"></script>
    <script src="{{ asset('assets/libs/ladda/ladda.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/loading-btn.init.js') }}"></script>
    <script src="{{ asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/monthSelect/index.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
    <!-- third party js ends -->
    <script type="text/javascript">
        $(document).ready(function() {
            moment.locale("th-TH");
            flatpickr.localize(flatpickr.l10ns.th);
            $(".month-datepicker").flatpickr({
                disableMobile: "true",
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: "m/Y",
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
                        })
                        .appendTo($(instance.calendarContainer));
                },
            });
            $("#dept, #month").on('change', function() {
                getData();
            });
        });

        function rmydays(date) {
            // Disable weekends
            return date.getDay() === 0 || date.getDay() === 6;
        }

        function getSaturdaysOfMonths(date) {
            // Get Saturdays of months
            var my_date = date.split('-');
            var year = parseInt(my_date[0]);
            var month = parseInt(my_date[1])-1;

            var saturdays = [];

            for (var i = 1; i < new Date(year, month, 0).getDate(); i++){
                var date = new Date(year, month, i);
                if (date.getDay() == 6){
                    saturdays.push(date);
                }
            };
            return saturdays;
        }

        function getData() {
            $('#btnDownload').hide();
            $('#divTable').html('');
            $('#divNavigation').hide();
            var dept = $("#dept").val();
            var month = $("#month").val();
            if ((dept != "" && dept != null) && (month != "" && month != null)) {
                var m = month.split('/');
                var date = m[1]+"-"+m[0]+"-01";
                var sat_list = getSaturdaysOfMonths(date);
                $("#tableLoading").html('<span class="spinner-border spinner-border" role="status" aria-hidden="true"></span>');
                $.ajax({
                    url: "{{ url('leave/leave-record-form/search') }}",
                    method: 'GET',
                    data: {
                        dept: dept,
                        month: month
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#btnDownload').show();
                        $("#tableLoading").html('');
                        $('#divNavigation').show();
                        if (data.total_data > 0) {
                            $('#submit').show();
                        } else {
                            $('#submit').hide();
                        }
                        $('#divTable').html(data.table_data);
                        $(".leave-datepicker").flatpickr({
                            locale: {
                                firstDayOfWeek: 0,
                            },
                            disable: [rmydays],
                            dateFormat: "d/m/Y",
                            minDate: new Date(date), // now
                            maxDate: new Date(date).fp_incr(90), // next 3 months
                            disableMobile: true,
                            onReady: function(dateObj, dateStr, instance) {
                                instance.changeMonth(parseInt(data.diff_month));
                                const $clear = $(
                                        '<div class="flatpickr-clear"><button class="btn btn-sm btn-link">Clear</button></div>'
                                    )
                                    .on("click", () => {
                                        instance.clear();
                                        instance.close();
                                    })
                                    .appendTo($(instance.calendarContainer));
                            },
                            onOpen: function (selectedDates, dateStr, instance) {
                                instance.set("minDate", sat_list[instance.element.alt]);
                            },
                        });
                    }
                });
            }
        }

        function toggleLeaveDate(i, s) {
            const status = $("input[name='status[" + i + "][" + s + "]']:checked").val();
            if (status === "on") { // ทำงาน
                $("input[name='leave_date[" + i + "][" + s + "]']").attr("style", "width:100px;background-color:yellow");
                $("input[name='leave_date[" + i + "][" + s + "]']").attr("disabled", false);
            } else { // หยุด
                $("input[name='leave_date[" + i + "][" + s + "]']").val("");
                $("input[name='leave_date[" + i + "][" + s + "]']").attr("style", "width:100px;background-color:lightgrey");
                $("input[name='leave_date[" + i + "][" + s + "]']").attr("disabled", true);
            }
        }

        function downloadForm() {
            if ($("#dept").val() == "" || $("#dept").val() == null) {
                Swal.fire({
                    icon: "warning",
                    title: "ยังไม่ได้เลือกฝ่าย",
                    showConfirmButton: false,
                    timer: 2000,
                });
                return false;
            } else if ($("#month").val() == "" || $("#month").val() == null) {
                Swal.fire({
                    icon: "warning",
                    title: "ยังไม่ได้เลือกเดือน",
                    showConfirmButton: false,
                    timer: 2000,
                });
                return false;
            }
            var url = "{{ url('leave/leave-record/download') }}";
            $("#downloadForm").append('<form action="' + url + '" method="GET">');
            $("#downloadForm form").append(
                '<input type="text" name="download-dept" value="' + $("#dept").val() + '"/>'
            );
            $("#downloadForm form").append(
                '<input type="text" name="download-month" value="' + $("#month").val() + '"/>'
            );
            $("#downloadForm form").submit();
            $("#downloadForm").html("");
        }

        function convertDateFormat(date) {
            if (date == "" || date == null) {
                return "";
            }
            return moment(date, "DD/MM/YYYY").format("YYYY-MM-DD");
        }

        function convertFormToJSON(form) {
            const array = $(form).serializeArray(); // Encodes the set of form elements as an array of names and values.
            const json = [];
            var rows_count = $("#rows_count").val();
            var sat_count = $("#sat_count").val();
            var sat_list = $("#sat_list").val().split(",");
            for (let i = 0; i < rows_count; i++) {
                const arr = [];
                for (let s = 0; s < sat_count; s++) {
                    arr.push({
                        sat: (s+1),
                        status: $("input[name='status[" + i + "][" + s + "]']:checked").val() || "off",
                        leave_date: convertDateFormat($("input[name='leave_date[" + i + "][" + s + "]']").val()) || "",
                        work_date: sat_list[s] || "",
                    });
                }
                json.push({
                    emp_id: $("input[name='emp_id[" + i + "]']").val() || "",
                    data: arr,
                });
            }
            return json;
        }

        function findDuplicates(arr) {
            return arr.filter((currentValue, currentIndex) => arr.indexOf(currentValue) !== currentIndex);
        }

        function saveData() {
            Swal.fire({
                icon: "warning",
                title: "คุณต้องการบันทึกวันทำงาน ใช่ไหม?",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ยืนยัน!",
                cancelButtonText: "ยกเลิก",
                showLoaderOnConfirm: true,
                stopKeydownPropagation: false,
                didOpen: () => {
                    const rows_count = $("#rows_count").val();
                    const sat_count = $("#sat_count").val();
                    const sat_list = $("#sat_list").val().split(",");
                    let chk_found = 0;
                    for (let i = 0; i < rows_count; i++) {
                        const emp_id = $("input[name='emp_id[" + i + "]']").val();
                        let dateDuplicate = [];
                        for (let s = 0; s < sat_count; s++) {
                            const status = $("input[name='status[" + i + "][" + s + "]']:checked").val();
                            const leave_date = $("input[name='leave_date[" + i + "][" + s + "]']").val();
                            if (status === "on" && (leave_date == "" || leave_date == null)) {
                                Swal.fire({
                                    icon: "warning",
                                    title: "รหัสพนักงาน: "+emp_id,
                                    text: "มีบางรายการไม่ได้เลือกวันหยุด",
                                });
                                return false;
                            } else if (status === "on" && new Date(convertDateFormat(leave_date)) < new Date(sat_list[s])) {
                                Swal.fire({
                                    icon: "warning",
                                    title: "รหัสพนักงาน: "+emp_id,
                                    text: "เลือกวันหยุดได้หลังจากเสาร์ที่เรามาทำงานแล้วเท่านั้น",
                                });
                                return false;
                            }
                            if (status === "on") {
                                dateDuplicate.push(leave_date);
                                chk_found++;
                            }
                        }
                        if (findDuplicates(dateDuplicate).length > 0) {
                            Swal.fire({
                                icon: "warning",
                                title: "รหัสพนักงาน: "+emp_id,
                                text: "ห้ามเลือกวันหยุดซ้ำกัน!",
                            });
                            return false;
                        }
                    }
                    if (chk_found === 0) {
                        Swal.fire({
                            icon: "warning",
                            title: "ยังไม่ได้เลือกวันหยุด!",
                        });
                        return false;
                    }
                },
                preConfirm: () => {
                    const jsonData = convertFormToJSON("#data-form");
                    return fetch('/leave/leave-record-store', {
                            method: 'POST',
                            headers: {
                                'Content-type': 'application/json; charset=UTF-8',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                'dept': $("#dept").val(),
                                'month': $("#month").val(),
                                'data': jsonData
                            }),
                        })
                        .then(function(response) {
                            if (!response.ok) {
                                throw new Error(response.statusText);
                            }
                            return response.json();
                        })
                        .then(function(data) {
                            console.log(data);
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
                $('#loading').hide();
                $('#submit').show();
                if (result.isConfirmed) {
                    $('#loading').show();
                    $('#submit').hide();
                    Swal.fire({
                        icon: "success",
                        title: "บันทึกเรียบร้อย!",
                    });
                    setTimeout(() => {
                        window.location.href = "/leave/leave-record";
                    }, 2000);
                }
            });
        }
    </script>
@endsection
