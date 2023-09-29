@extends('layouts.master-layout', ['page_title' => "ข้อมูลเข้า-ออกงาน"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/ladda/ladda.min.css') }}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
<style>
    .dropify-message > p {
        font-size: 1.75rem;
        color: #cccccc;
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
                        <li class="breadcrumb-item active">ข้อมูลเข้า-ออกงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">ข้อมูลเข้า-ออกงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    @if (!session()->has('data') && !session()->has('data_export'))
    <!-- upload template -->
    <div class="d-none" id="uploadPreviewTemplate">
        <div class="card mt-1 mb-0 shadow-none border">
            <div class="p-2">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img data-dz-thumbnail src="#" class="avatar-sm rounded bg-light" alt="">
                    </div>
                    <div class="col ps-0">
                        <a href="javascript:void(0);" class="text-muted fw-bold" data-dz-name></a>
                        <p class="mb-0" data-dz-size></p>
                    </div>
                    <div class="col-auto">
                        <!-- Button -->
                        <a href="" class="btn btn-link btn-lg text-muted" data-dz-remove>
                            <i class="dripicons-cross"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="form-horizontal" id="upload-form" action="{{ url('leave/manage/fingerprint-upload') }}" method="POST"
                        enctype="multipart/form-data" onsubmit="return SubmitFormUpload(this);">
                        {{ csrf_field() }}
                        <input type="file" id="file" name="file" accept=".csv" data-plugins="dropify" data-height="300" data-max-file-size="5M" data-allowed-file-extensions="csv" />
                        {!! $errors->first('file', '<span class="text-danger">:message</span>') !!}
                        <div class="mt-3 text-center">
                            <button id="loading" name="loading" class="btn btn-primary hidd" type="button" disabled>
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                รอสักครู่...
                            </button>
                            <button type="submit" id="submit" name="submit" class="ladda-button btn btn-primary me-2" dir="ltr" data-style="zoom-out"><i class="mdi mdi-upload me-1"></i>อัปโหลด</button>
                            <button type="button" class="btn btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#downloadModal"><i class="mdi mdi-download me-1"></i>ดาวน์โหลดไฟล์</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @elseif (session()->has('data'))
    {{-- รายการอัปโหลด --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="form" id="data-form">
                        {{ csrf_field() }}
                        <h4 class="header-title">รายการอัปโหลด <span class="text-blue">({{ session()->get('total') }})</span></h4>
                        <br>
                        <input type="hidden" id="file_name" name="file_name" value="{{ session()->get('file_name') }}">
                        <div class="form-check mb-2 form-check-primary">
                            <input class="form-check-input" type="checkbox" id="checkAll" name="checkAll[]">
                            <label class="form-check-label text-dark" for="checkAll">เลือกทั้งหมด</label>
                        </div>
                        @foreach (session()->get('data') as $data)
                            @php
                                $isQty = ($data["total"] > 0) ? true : false;
                            @endphp
                            <div class="form-check mb-2 form-check-primary">
                                <input class="form-check-input branch branch_{{ $data["id"] }}" type="checkbox" value="{{ $data["id"] }}" id="branch_{{ $data["id"] }}" name="branch[]" {{ (!$isQty) ? 'disabled' : '' }}>
                                <label class="form-check-label {{ (!$isQty) ? 'text-danger' : 'text-dark' }}" for="branch_{{ $data["id"] }}">{{ $data["id"] }} - {{ $data["name"] }} ({{ $data["total"] }})</label>
                            </div>
                            <div class="ps-4">
                            @foreach ($data["device"] as $device)
                                <div class="form-check mb-2 form-check-primary">
                                    <input class="form-check-input device device_{{ $data["id"] }}" type="checkbox" value="{{ $device["id"] }}" id="device_{{ $device["id"] }}" name="device[]" {{ (!$isQty) ? 'disabled' : '' }}>
                                    <label class="form-check-label fst-italic {{ (!$isQty) ? 'text-danger' : '' }}" for="device_{{ $device["id"] }}">{{ $device["id"] }} - {{ $device["name"] }} ({{ $device["qty"] }})</label>
                                </div>
                            @endforeach
                            </div>
                        @endforeach
                        <div class="form-check mb-2 form-check-primary mt-5">
                            <input class="form-check-input" type="checkbox" id="checkDownload" name="checkDownload">
                            <label class="form-check-label text-dark" for="checkDownload">ดาวน์โหลดไฟล์ .txt</label>
                        </div>
                        <input type="text" class="form-control hidd" id="download_name" name="download_name" placeholder="ระบุชื่อไฟล์ที่ต้องการดาวน์โหลด">
                    </form>
                    <div class="mt-4">
                        <button type="button" class="btn btn-danger" onclick="location.reload();">ยกเลิก</a>
                        <button type="button" class="btn btn-success mx-2" onclick="saveConfirmation();">บันทึก</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    {{-- รายการดาวน์โหลด --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="form" id="data-export-form" action="{{ url('leave/manage/fingerprint-data-download') }}" method="POST"
                        enctype="multipart/form-data" onsubmit="return SubmitFormDataDownload(this);">
                        {{ csrf_field() }}
                        <h4 class="header-title">รายการดาวน์โหลด <span class="text-blue">({{ session()->get('total') }})</span></h4>
                        <h5 class="text-blue">วันที่ {{ session()->get('date_start') }} - {{ session()->get('date_end') }}</h5>
                        <br>
                        <input type="hidden" id="date_start_download" name="date_start_download" value="{{ session()->get('date_start') }}">
                        <input type="hidden" id="date_end_download" name="date_end_download" value="{{ session()->get('date_end') }}">
                        <div class="form-check mb-2 form-check-primary">
                            <input class="form-check-input" type="checkbox" id="checkDlAll" name="checkDlAll[]">
                            <label class="form-check-label text-dark" for="checkDlAll">เลือกทั้งหมด</label>
                        </div>
                        @foreach (session()->get('data_export') as $data)
                            @php
                                $isQty = ($data["total"] > 0) ? true : false;
                            @endphp
                            <div class="form-check mb-2 form-check-primary">
                                <input class="form-check-input branchdl branchdl_{{ $data["id"] }}" type="checkbox" value="{{ $data["id"] }}" id="branchdl_{{ $data["id"] }}" name="branchdl[]" {{ (!$isQty) ? 'disabled' : '' }}>
                                <label class="form-check-label {{ (!$isQty) ? 'text-danger' : 'text-dark' }}" for="branchdl_{{ $data["id"] }}">{{ $data["id"] }} - {{ $data["name"] }} ({{ $data["total"] }})</label>
                            </div>
                            <div class="ps-4">
                            @foreach ($data["device"] as $device)
                                <div class="form-check mb-2 form-check-primary">
                                    <input class="form-check-input devicedl devicedl_{{ $data["id"] }}" type="checkbox" value="{{ $device["id"] }}" id="devicedl_{{ $device["id"] }}" name="devicedl[]" {{ (!$isQty) ? 'disabled' : '' }}>
                                    <label class="form-check-label fst-italic {{ (!$isQty) ? 'text-danger' : '' }}" for="devicedl_{{ $device["id"] }}">{{ $device["id"] }} - {{ $device["name"] }} ({{ $device["qty"] }})</label>
                                </div>
                            @endforeach
                            </div>
                        @endforeach
                        <input type="text" class="form-control mt-5" id="downloaddl_name" name="downloaddl_name" placeholder="ระบุชื่อไฟล์ที่ต้องการดาวน์โหลด">
                        <div class="mt-4">
                            <button type="button" class="btn btn-danger" onclick="location.reload();">ยกเลิก</a>
                            <button type="submit" class="btn btn-info mx-2">ดาวน์โหลด</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- Select SO Modal -->
    <div id="downloadModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="downloadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="form-horizontal" id="download-form" action="{{ url('leave/manage/fingerprint-data') }}" method="POST"
                enctype="multipart/form-data" onsubmit="return SubmitFormDownload(this);">
                {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="downloadModalLabel">ดาวน์โหลดไฟล์ข้อมูลเข้า-ออกงาน</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                            <label for="date_start" class="form-label mb-0">วันที่เริ่มต้น</label>
                            <div class="form-group">
                                <input type="text" class="form-control datepicker" id="date_start" name="date_start" placeholder="เลือกวันที่">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                            <label for="date_end" class="form-label mb-0">วันที่สิ้นสุด</label>
                            <div class="form-group">
                                <input type="text" class="form-control datepicker" id="date_end" name="date_end" placeholder="เลือกวันที่">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">ตกลง</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('assets/libs/flatpickr/dist/l10n/th.js')}}"></script>
<script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
<script src="{{asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
<script src="{{asset('assets/libs/dropify/dropify.min.js')}}"></script>
<script src="{{ asset('assets/libs/ladda/ladda.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/loading-btn.init.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    !function(e){"use strict";var o=function(){this.$body=e("body")};o.prototype.init=function(){Dropzone.autoDiscover=!1,e('[data-plugin="dropzone"]').each((function(){var o=e(this).attr("action"),i=e(this).data("previewsContainer"),r={url:o};i&&(r.previewsContainer=i);var t=e(this).data("uploadPreviewTemplate");t&&(r.previewTemplate=e(t).html()),e(this).dropzone(r)}))},e.FileUpload=new o,e.FileUpload.Constructor=o}(window.jQuery),function(e){"use strict";window.jQuery.FileUpload.init()}(),$('[data-plugins="dropify"]').length>0&&$('[data-plugins="dropify"]').dropify({messages:{default:"Drag and drop a file here or click",replace:"Drag and drop or click to replace",remove:"Remove",error:"Ooops, something wrong appended."},error:{fileSize:"The file size is too big (5M max)."}});
    var mySelectModal = new bootstrap.Modal(document.getElementById('downloadModal'));
    $(document).ready(function() {
        moment.locale("th-TH");
        flatpickr.localize(flatpickr.l10ns.th);
        $(".datepicker").flatpickr({
            locale: {
                firstDayOfWeek: 0,
            },
            dateFormat: "d/m/Y",
            disableMobile: true,
            onReady: function (dateObj, dateStr, instance) {
                const $clear=$( '<div class="flatpickr-clear"><button class="btn btn-sm btn-link">Clear</button></div>' ) .on("click", ()=> {
                    instance.clear();
                    instance.close();
                }).appendTo($(instance.calendarContainer));
            },
            onClose: function(selectedDates, dateStr, instance){
                $(instance.input).blur();
            }
        });
        // --------------------------- รายการอัปโหลด ---------------------------------
        $("#checkAll").click(function(){
            $(".branch").not(":disabled").prop('checked', $(this).prop('checked'));
            $(".device").not(":disabled").prop('checked', $(this).prop('checked'));
        });
        $(".branch").click(function(){
            const branch_id = $(this).attr('id').split("_")[1];
            $(".device_"+branch_id).prop('checked', $(this).prop('checked'));
            if ($(".branch_"+branch_id).not(":disabled").length === $(".branch_"+branch_id+":checked").not(":disabled").length) {
                $("#checkAll").prop("checked", true);
            } else {
                $("#checkAll").prop("checked", false);
            }
        });
        $(".device").click(function(){
            const className = $(this).attr("class").split(' ').slice(-1);
            const branch_id = className.toString().split('_')[1];
            if ($(".device_"+branch_id).not(":disabled").length === $(".device_"+branch_id+":checked").not(":disabled").length) {
                $(".branch_"+branch_id).prop("checked", true);
                $("#checkAll").prop("checked", true);
            } else {
                $(".branch_"+branch_id).prop("checked", false);
                $("#checkAll").prop("checked", false);
            }
        });
        $("#checkDownload").click(function(){
            if ($(this).prop('checked')) {
                $("#download_name").show();
            } else {
                $("#download_name").val('');
                $("#download_name").hide();
            }
        });
        // --------------------------- END ---------------------------------
        // --------------------------- รายการดาวน์โหลด ---------------------------------
        $("#checkDlAll").click(function(){
            $(".branchdl").not(":disabled").prop('checked', $(this).prop('checked'));
            $(".devicedl").not(":disabled").prop('checked', $(this).prop('checked'));
        });
        $(".branchdl").click(function(){
            const branch_id = $(this).attr('id').split("_")[1];
            $(".devicedl_"+branch_id).prop('checked', $(this).prop('checked'));
            if ($(".branchdl_"+branch_id).not(":disabled").length === $(".branchdl_"+branch_id+":checked").not(":disabled").length) {
                $("#checkDlAll").prop("checked", true);
            } else {
                $("#checkDlAll").prop("checked", false);
            }
        });
        $(".devicedl").click(function(){
            const className = $(this).attr("class").split(' ').slice(-1);
            const branch_id = className.toString().split('_')[1];
            if ($(".devicedl_"+branch_id).not(":disabled").length === $(".devicedl_"+branch_id+":checked").not(":disabled").length) {
                $(".branchdl_"+branch_id).prop("checked", true);
                $("#checkDlAll").prop("checked", true);
            } else {
                $(".branchdl_"+branch_id).prop("checked", false);
                $("#checkDlAll").prop("checked", false);
            }
        });
        // --------------------------- END ---------------------------------
    });
    function SubmitFormUpload(form) {
        if (document.getElementById('file').value == "") {
            Swal.fire({
                icon: "warning",
                title: "ยังไม่ได้เลือกไฟล์",
                showConfirmButton: false,
                timer: 2000,
            });
            return false;
        }
        Swal.fire({
            title: 'กำลังอัปโหลดข้อมูล',
            html: 'กรุณารอสักครู่...',
            showConfirmButton: false,
            allowOutsideClick: false,
            onBeforeOpen: () => {
                Swal.showLoading()
            },
        });
        $('#loading').show();
        $('#submit').hide();
    }
    function SubmitFormDownload(form) {
        if (document.getElementById('date_start').value == "" || document.getElementById('date_end').value == "") {
            Swal.fire({
                icon: "warning",
                title: "ยังไม่ได้เลือกวันที่",
                showConfirmButton: false,
                timer: 2000,
            });
            return false;
        }
    }
    function SubmitFormDataDownload(form) {
        var download_name = $("#downloaddl_name").val();
        var device = $("input[name='devicedl[]']:checked").map(function () {
            return this.value;
        }).get();
        if (device.length <= 0) {
            Swal.fire({
                icon: "warning",
                title: "โปรดเลือกรายการ!",
            });
            return false;
        }
        if (download_name == "" || download_name == null) {
            Swal.fire({
                icon: "warning",
                title: "โปรดระบุชื่อไฟล์ที่ต้องการดาวน์โหลด!",
            });
            return false;
        }
    }
    function saveConfirmation() {
        var file_name = $("#file_name").val();
        var download_name = $("#download_name").val();
        var device = $("input[name='device[]']:checked").map(function () {
            return this.value;
        }).get();
        if (device.length <= 0) {
            Swal.fire({
                icon: "warning",
                title: "โปรดเลือกรายการ!",
            });
            return false;
        }
        if ($("#checkDownload").prop('checked')) {
            if (download_name == "" || download_name == null) {
                Swal.fire({
                    icon: "warning",
                    title: "โปรดระบุชื่อไฟล์ที่ต้องการดาวน์โหลด!",
                });
                return false;
            }
        }
        Swal.fire({
            icon: "warning",
            title: "ยืนยันการบันทึกข้อมูลใช่ไหม?",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ยืนยัน!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                const params = {
                    device: device,
                    file_name: file_name,
                    download_name: download_name
                };
                const options = {
                    credentials: 'same-origin',
                    method: 'POST',
                    body: JSON.stringify( params ),
                    headers: new Headers({
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    }),
                };
                return fetch(`/leave/manage/fingerprint/store`, options)
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
                if (result.value.success == true) {
                    Swal.fire({
                        icon: "success",
                        title: result.value.message,
                        timer: 2000,
                    });
                    if (result.value.file_name != "" && result.value.file_name != null) {
                        location.href = "{{ url('/leave/manage/fingerprint/download') }}/"+result.value.file_name;
                    }
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    console.log(result.value.message);
                    Swal.fire({
                        icon: "warning",
                        title: result.value.message,
                    });
                }
            }
        });
    }
</script>
@endsection