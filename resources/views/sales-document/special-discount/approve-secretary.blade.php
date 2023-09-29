@extends('layouts.master-layout', ['page_title' => 'อนุมัติคำขอ'])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/css/placeholder-loading.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/monthSelect/style.css') }}" rel="stylesheet" />
    <!-- third party css end -->
    <style>
        .btnPrint {
            margin-left: -4px;
            background-color: transparent;
            border: 0;
        }

        .bg-approve {
            background: rgb(112, 77, 215);
            background: linear-gradient(180deg, rgba(112, 77, 215, 1) 0%, rgba(95, 53, 219, 1) 100%);
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
                            <li class="breadcrumb-item active">Special Discount</li>
                        </ol>
                    </div>
                    <h4 class="page-title">อนุมัติคำขอ</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        @inject('thaiDateHelper', '\App\Services\ThaiDateHelperService')
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header bg-approve">
                        <b class="text-white">รออนุมัติ</b>
                    </div>
                    <div class="card-body">
                        <table data-toggle="table" data-search="true" data-show-columns="false" data-page-list="[5, 10, 20]"
                            data-page-size="20" data-buttons-class="xs btn-light" data-pagination="true"
                            class="table table-bordered table-hover table-borderless">
                            <thead class="table-light">
                                <tr>
                                    <th data-field="list-code" data-switchable="false">รหัสรายการ</th>
                                    <th data-field="date">วันที่แจ้ง</th>
                                    <th data-field="name">ชื่อผู้แจ้ง</th>
                                    <th data-field="customer-code">รหัสลูกค้า</th>
                                    <th data-field="shop-name">ชื่อร้าน</th>
                                    <th data-field="doc_status">สถานะ</th>
                                    <th data-field="action">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wapprove as $wa)
                                    <tr>
                                        <td>
                                            <a class="btn btn-outline-primary waves-effect waves-light"
                                                href="{{ url('sales-document/special-discount/preview/view') }}/{{ $wa->doc_id }}/{{ $wa->doc_status }}">
                                                {{ $wa->doc_id }}
                                            </a>
                                        </td>
                                        <td><i class="fe-clock"></i>
                                            {{ $thaiDateHelper->shortDateFormat($wa->created_at) }}</td>
                                        @php
                                            $name_cre = Auth::User()->findEmployee($wa->emp_id);
                                            if ($name_cre->nickname) {
                                                $emp_names = $name_cre->name . ' ' . $name_cre->surname . ' ( ' . $name_cre->nickname . ' )';
                                            } else {
                                                $emp_names = $name_cre->name . ' ' . $name_cre->surname;
                                            }
                                        @endphp
                                        <td><i class="fe-user"></i> {{ $emp_names }}</td>
                                        <td>{{ $wa->customer_code }}</td>
                                        <td><i class="mdi mdi-storefront-outline"></i> {{ $wa->customer_name }}</td>
                                        <td>
                                            <span class="badge badge-soft-blue">รออนุมัติ</span>
                                        </td>
                                        <td><a
                                                href="{{ url('sales-document/special-discount/preview/view') }}/{{ $wa->doc_id }}/{{ $wa->doc_status }}">
                                                <i class="fe-eye fs-4 text-primary" title="ดูรายละเอียด"></i>&nbsp;
                                            </a>
                                            {{-- <a href="javascript: void(0);"
                                                onclick="approve('{{ $wa->doc_id }}',{{ $wa->id }},'yes')">
                                                <i class="mdi mdi-check-circle-outline fs-4 text-success"
                                                    title="อนุมัติ"></i>&nbsp;
                                            </a>
                                            <a href="javascript: void(0);"
                                                onclick="approve('{{ $wa->doc_id }}',{{ $wa->id }},'no')">
                                                <i class="mdi mdi-close-circle-outline fs-4 text-danger"
                                                    title="ไม่อนุมัติ"></i>
                                            </a> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-approve">
                        <b class="text-white">รายการทั้งหมด</b>
                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('secretary.listapprove') }}">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-lg-3 mb-2">
                                    <label for="doc_date" class="form-label mb-0">เดือน/ปี</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control month-datepicker" id="doc_date"
                                            name="doc_date" placeholder="เลือกเดือน" value="{{ old('doc_date') }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <label for="doc_date" class="form-label mb-0">สถานะ</label>
                                    <div class="input-group">
                                        <select class="form-select" id="doc_status" name="doc_status"
                                            aria-label="Example select with button addon">
                                            <option value="" selected>ทั้งหมด</option>
                                            <option value="9" {{ old('doc_status') == '9' ? 'selected' : '' }}>
                                                อนุมัติแล้ว</option>
                                            <option value="0" {{ old('doc_status') == '0' ? 'selected' : '' }}>
                                                ไม่อนุมัติ</option>
                                        </select>
                                        <button class="btn btn-blue" type="submit"><i class="fe-search"></i>
                                            ค้นหา</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <table data-toggle="table" data-search="true" data-show-columns="false" data-page-list="[5, 10, 20]"
                            data-page-size="20" data-buttons-class="xs btn-light" data-pagination="true"
                            class="table table-bordered table-hover table-borderless">
                            <thead class="table-light">
                                <tr>
                                    <th data-field="list-code" data-switchable="false">รหัสรายการ</th>
                                    <th data-field="date">วันที่แจ้ง</th>
                                    <th data-field="name">ชื่อผู้แจ้ง</th>
                                    <th data-field="customer-code">รหัสลูกค้า</th>
                                    <th data-field="shop-name">ชื่อร้าน</th>
                                    <th data-field="print">ครั้งที่พิมพ์</th>
                                    <th data-field="doc_status">สถานะ</th>
                                    <th data-field="action">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($special as $dis)
                                    @if ($dis->doc_status != 1)
                                        @php
                                            $log_list = json_decode($dis->log_list);
                                            $count = 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <a class="btn btn-outline-primary waves-effect waves-light"
                                                    href="{{ url('sales-document/special-discount/preview/view') }}/{{ $dis->doc_id }}/{{ $dis->doc_status }}">
                                                    {{ $dis->doc_id }}
                                                </a>
                                            </td>
                                            <td><i class="fe-clock"></i>
                                                {{ $thaiDateHelper->shortDateFormat($dis->created_at) }}</td>
                                            @php
                                                $name_cres = Auth::User()->findEmployee($dis->emp_id);
                                                if ($name_cres->nickname) {
                                                    $emp_nameslist = $name_cres->name . ' ' . $name_cres->surname . ' ( ' . $name_cres->nickname . ' )';
                                                } else {
                                                    $emp_nameslist = $name_cres->name . ' ' . $name_cres->surname;
                                                }
                                            @endphp
                                            <td><i class="fe-user"></i> {{ $emp_nameslist }}</td>
                                            <td>{{ $dis->customer_code }}</td>
                                            <td><i class="mdi mdi-storefront-outline"></i> {{ $dis->customer_name }}</td>
                                            <td>
                                                @if ($dis->doc_status == 9)
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="historyPrint{{ $dis->id }}"
                                                        tabindex="-1" aria-labelledby="exampleModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <i class="mdi mdi-car-brake-alert text-info fa-5x"></i>
                                                                    <h4 class="modal-title mt-2" id="exampleModalLabel">
                                                                        ประวัติการพิมพ์เอกสาร</h4>
                                                                    <p class="text-dark">เลขที่เอกสาร :
                                                                        {{ $dis->doc_id }}</p>
                                                                    @foreach ($log_list as $print)
                                                                        @if ($print->description == 'พิมพ์เอกสาร')
                                                                            @php
                                                                                $count++;
                                                                                $emp = Auth::User()->findEmployee($print->logemp);
                                                                                if ($emp->nickname) {
                                                                                    $emp_name = $emp->name . ' ' . $emp->surname . ' ( ' . $emp->nickname . ' )';
                                                                                } else {
                                                                                    $emp_name = $emp->name . ' ' . $emp->surname;
                                                                                }
                                                                            @endphp
                                                                            <p><b class="text-dark mt-2">ครั้งที่
                                                                                    {{ $count }} : </b><span
                                                                                    class="text-blue">{{ $emp_name }}</span>
                                                                                <small class="text-muted"><i
                                                                                        class="mdi mdi-clock-outline"></i>
                                                                                    {{ date('d-m-Y H:i:s', strtotime($print->date)) }}
                                                                                </small>
                                                                            </p>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">ปิด</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                                        data-bs-target="#historyPrint{{ $dis->id }}">
                                                        <span class="badge badge-soft-info">{{ $count }}</span><i
                                                            class="text-info mdi mdi-file-document-multiple-outline"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($dis->doc_status == 0)
                                                    <span class="badge badge-soft-danger">ไม่อนุมัติ</span>
                                                @elseif($dis->doc_status == 1)
                                                    <span class="badge badge-soft-blue">รออนุมัติ</span>
                                                @elseif($dis->doc_status == 9)
                                                    <span class="badge badge-soft-success">เสร็จสิ้น</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a
                                                    href="{{ url('sales-document/special-discount/preview/view') }}/{{ $dis->doc_id }}/{{ $dis->doc_status }}">
                                                    <i class="fe-eye fs-4 text-primary" title="ดูรายละเอียด"></i>&nbsp;
                                                </a>
                                                @if ($dis->doc_status == 9)
                                                    <button data-bs-toggle="modal" data-bs-target="#printModal"
                                                        value="{{ $dis->doc_id }}" title="Print" class="btnPrint">
                                                        <i class="mdi mdi-printer fs-4 text-blue me-1"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="printModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="printModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            {{-- <div class="modal-dialog modal-lg modal-dialog-centered"> --}}
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="printModalLabel">แบบฟอร์มขออนุมัติส่วนลดงานล็อตใหญ่
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <embed src="" id='pdf' frameborder="0" width="100%" height="800px">
                </div>
            </div>
            {{-- </div> --}}
        </div>
        <!-- end modal -->
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

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {
            moment.locale("th-TH");
            flatpickr.localize(flatpickr.l10ns.th);
            $(".month-datepicker").flatpickr({
                disableMobile: "true",
                dateFormat: "m/Y",
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
                onClose: function(selectedDates, dateStr, instance) {
                    $(instance.input).blur();
                }
            });
        });

        $(document).on('click', '.btnPrint', function() {
            var objData = $(this).val();
            $("#pdf").attr("src",
                "{{ url('sales-document/special-discount/print') }}/" + objData.toString());
            $.ajax({
                type: 'POST',
                url: "{{ route('discount.log.print') }}",
                data: {
                    doc_id: objData,
                },
                success: function(response) {
                    console.log(response);
                },
            });
        });

        function approve(doc_id, id, approve) {
            var confirmMsg = (approve == "yes") ? "ยืนยันการอนุมัติ" : "ไม่ต้องการอนุมัติ";
            Swal.fire({
                icon: "warning",
                title: confirmMsg + " ใช่ไหม?",
                html: '<label>ควมคิดเห็น</label><input type="text" class="form-control" id="comment" name="comment">',
                showCancelButton: true,
                confirmButtonColor: "#00bc9d",
                cancelButtonColor: "#d33",
                confirmButtonText: "ยืนยัน!",
                cancelButtonText: "ยกเลิก",
                showLoaderOnConfirm: true,
                stopKeydownPropagation: false,
                preConfirm: () => {
                    var comment = $("#comment").val();
                    return fetch(
                            '/sales-document/product-decorate/approve/secretary-approve', {
                                method: 'POST',
                                headers: {
                                    'Content-type': 'application/json; charset=UTF-8',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({
                                    doc_id: doc_id,
                                    id: id,
                                    approve: approve,
                                    comment: comment,
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
                        title: "เรียบร้อย!",
                    });
                    setTimeout(() => {
                        location.reload()
                    }, 2000);
                }
            });
        }
    </script>
@endsection
