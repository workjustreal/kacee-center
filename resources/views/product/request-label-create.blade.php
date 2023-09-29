@extends('layouts.master-layout', ['page_title' => "ร้องขอสติ๊กเกอร์บาร์โค้ด"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/ladda/ladda.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/css/label-color.css')}}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Product</a></li>
                        <li class="breadcrumb-item active">ร้องขอสติ๊กเกอร์บาร์โค้ด</li>
                    </ol>
                </div>
                <h4 class="page-title">ร้องขอสติ๊กเกอร์บาร์โค้ด</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">ค้นหาสินค้า</h4>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-4">
                                <h5 class="mt-0">รหัสสินค้า:</h5>
                                <p><input type="text" class="form-control typeahead" data-provide="typeahead" id="search" name="search" autocomplete="off" placeholder="ค้นหา"></p>
                            </div>
                            <div class="mb-4 bg-light sku-detail"></div>
                            <div class="mb-4 divAdd" style="display: none;">
                                <input type="hidden" class="form-control" id="search-selected" name="search-selected">
                                <div class="d-flex justify-content-end">
                                    <input type="number" class="form-control mx-2" id="qty" name="qty" autocomplete="off" placeholder="จำนวน" value="1" min="1" style="width: 150px;">
                                    <button type="button" class="btn btn-blue" id="btn-add">เพิ่ม</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div id="downloadForm" class="d-none"></div>
                    <a href="{{ url('/product/request-label/download-template') }}" class="btn btn-sm btn-soft-secondary waves-effect waves-light float-end"><i class="mdi mdi-download me-1"></i>แบบฟอร์ม</a>
                    <h4 class="header-title mb-3">อัปโหลดไฟล์</h4>
                    <div class="d-flex">
                        <div class="justify-content-between">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <form class="form-horizontal" id="upload-form" action="{{ route('request-label.upload') }}" method="POST"
                                enctype="multipart/form-data" onsubmit="return SubmitUploadForm(this);">
                                {{ csrf_field() }}
                                <input type="hidden" class="form-control" id="session_act_up" name="session_act_up" value="create">
                                <input type="file" id="file" name="file" accept=".csv,.xls,.xlsx" data-plugins="dropify" data-height="200" data-max-file-size="5M" data-allowed-file-extensions='["csv","xls","xlsx"]' />
                                {!! $errors->first('file', '<span class="text-danger">:message</span>') !!}
                                <div class="mt-3 text-center">
                                    <button id="upload-loading" name="upload-loading" class="btn btn-primary hidd" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                        รอสักครู่...
                                    </button>
                                    <button type="submit" id="upload-submit" name="upload-submit" class="ladda-button btn btn-primary" dir="ltr" data-style="zoom-out" title="UPLOAD">อัปโหลด</button>
                                </div>
                            </form>
                            @if (\Session::has('err_data'))
                                @if (!empty(\Session::get('err_data')))
                                    <div class="alert alert-danger mt-3">
                                        <ul>
                                            @foreach (\Session::get('err_data') as $data)
                                                <li>{{ $data['sku'] }} : {{ $data['msg'] }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="header-title mb-3">รายการที่เพิ่ม</h4>
                        <button type="button" class="btn btn-sm btn-danger mb-3" id="btn-reset">ลบทั้งหมด</button>
                    </div>
                    <table data-toggle="table" data-page-size="10" data-buttons-class="xs btn-light" data-pagination="true"
                        class="table table-sm table-bordered" data-search="false">
                        <thead class="table-light">
                            <tr>
                                <th data-field="no" data-sortable="false" data-width="100">ลำดับ</th>
                                <th data-field="sku" data-sortable="false">รหัสสินค้า</th>
                                <th data-field="barcode" data-sortable="false">บาร์โค้ด</th>
                                <th data-field="name" data-sortable="false">ชื่อสินค้า</th>
                                <th data-field="qty" data-sortable="false" data-width="150">จำนวน(ดวง)</th>
                                <th data-field="del" data-sortable="false" data-width="80">ลบ</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <form class="form-horizontal" id="request-form" name="request-form"
                        method="POST" enctype="multipart/form-data" action="{{ route('request-label.store') }}" onsubmit="return SubmitForm(this);">
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control" id="session_act" name="session_act" value="create">
                        <div class="mt-4">
                            <label for="label_size" class="form-label">ขนาดสติ๊กเกอร์</label><br>
                            @foreach ($label as $label)
                            <div class="radio radio-success form-check-inline ml-2 mb-2 align-top">
                                <input type="radio" id="label_{{ $label["label"] }}" value="{{ $label["label"] }}" name="label">
                                <label for="label_{{ $label["label"] }}">
                                    {{ $label["label_detail"] }}
                                    <br><img src="{{asset('assets/images/'.$label["label"].'.png')}}" width="140" class="rounded border">
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <div class="radio radio-success form-check-inline ml-2 mb-2 align-top">
                                <input type="radio" id="barcode_white" value="white" name="barcode_color" required>
                                <label for="barcode_white">
                                    ขาว
                                    <br><span class="dotwhite"></span>
                                </label>
                            </div>
                            <div class="radio radio-success form-check-inline ml-2 mb-2 align-top">
                                <input type="radio" id="barcode_pink" value="pink" name="barcode_color" required>
                                <label for="barcode_pink">
                                    ชมพู
                                    <br><span class="dotpink"></span>
                                </label>
                            </div>
                            <div class="radio radio-success form-check-inline ml-2 mb-2 align-top">
                                <input type="radio" id="barcode_yellow" value="yellow" name="barcode_color" required>
                                <label for="barcode_yellow">
                                    เหลือง
                                    <br><span class="dotyel"></span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5 class="mt-0">หมายเหตุ:</h5>
                            <p><input type="text" class="form-control" id="remark" name="remark" autocomplete="off" placeholder="หมายเหตุ"></p>
                        </div>
                        <div class="mt-4">
                            <a class="btn btn-secondary" href="{{ url('/product/request-label') }}">ย้อนกลับ</a>
                            <button type="submit" class="btn btn-primary mx-2" id="btn-submit" disabled>บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
<script src="{{ asset('assets/js/bootstrap3-typeahead.js') }}"></script>
<script src="{{asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
<script src="{{asset('assets/libs/dropify/dropify.min.js')}}"></script>
<script src="{{ asset('assets/libs/ladda/ladda.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/loading-btn.init.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    var session_act = document.getElementById("session_act").value;
    !function(e){"use strict";var o=function(){this.$body=e("body")};o.prototype.init=function(){Dropzone.autoDiscover=!1,e('[data-plugin="dropzone"]').each((function(){var o=e(this).attr("action"),i=e(this).data("previewsContainer"),r={url:o};i&&(r.previewsContainer=i);var t=e(this).data("uploadPreviewTemplate");t&&(r.previewTemplate=e(t).html()),e(this).dropzone(r)}))},e.FileUpload=new o,e.FileUpload.Constructor=o}(window.jQuery),function(e){"use strict";window.jQuery.FileUpload.init()}(),$('[data-plugins="dropify"]').length>0&&$('[data-plugins="dropify"]').dropify({messages:{default:"Drag and drop a file here or click",replace:"Drag and drop or click to replace",remove:"Remove",error:"Ooops, something wrong appended."},error:{fileSize:"The file size is too big (5M max)."}});
    $(document).ready(function() {
        get_data();
        var base_url = window.location.protocol + "//" + window.location.host;
        var $myTypeahead = $("#search");
        $myTypeahead.typeahead({
            minLength: 1,
            items: 10,
            showHintOnFocus: "all",
            selectOnBlur: false,
            autoSelect: true,
            displayText: function (item) {
                html = '<div class="row">';
                    html += '<div class="col-md-3">';
                    if(item.images=="" || item.images==null){
                        html += '<img src="{{ url('assets/images/noimage.jpg') }}" width="50" height="50" />';
                    }else{
                        html += '<img src="{{ url('assets/images/thumbnail') }}/' + item.images + '" width="50" height="50" />';
                    }
                    html += '</div>';
                    html += '<div class="col-md-9">';
                        html += '<span class="m-0">' + item.stkcod + '</span>';
                        html += ' <small class="m-0">(' + item.barcod + ')</small>';
                        html += '<p class="m-0">' + item.names + '</p>';
                        html += '</div>';
                    html += '</div>';
                return html;
            },
            afterSelect: function (item) {
                this.$element[0].value = item.stkcod;
                $("#search").val(item.stkcod);
                $("#search-selected").val(item.stkcod);
                html = '<div class="p-2">';
                html += '<h5 class="mt-0">รายละเอียดสินค้า</h5>';
                html += '<p class="m-0">รหัสสินค้า: ' + item.stkcod + '</p>';
                html += '<p class="m-0">บาร์โค้ดสินค้า: ' + item.barcod + '</p>';
                html += '<p class="m-0">ชื่อสินค้า: ' + item.names + '</p>';
                html += '</div>';
                $(".sku-detail").html(html);
                $(".divAdd").show();
            },
            source: function (search, process) {
                return $.get(
                    base_url + "/product/request-label/search-sku",
                    { search: search },
                    function (data) {
                        return process(data);
                    }
                );
            },
        });
        $("#search").focusout(function(){
            if ($("#search").val() != $("#search-selected").val()) {
                $(".sku-detail").html('');
                $(".divAdd").hide();
            }
        });
        $('#qty').keypress(function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                $("#btn-add").click();
            }
        });
        $("#btn-add").click(function(){
            if ($("#search").val() != $("#search-selected").val()) {
                Swal.fire({
                    icon: "warning",
                    title: "คีย์รหัสสินค้าอีกครั้ง",
                    text: "รหัสสินค้าไม่ตรงกับที่เลือก",
                    showConfirmButton: false,
                    timer: 2000,
                });
                return false;
            }
            if ($("#qty").val() <= 0) {
                Swal.fire({
                    icon: "warning",
                    title: "ยังไม่ได้ใส่จำนวน",
                    showConfirmButton: false,
                    timer: 2000,
                });
                return false;
            }
            $.ajax({
                url: "{{ route('request-label.add_data') }}",
                method: 'GET',
                data: {session_act: session_act, sku: $("#search").val(), qty: $("#qty").val()},
                success: function(res) {
                    if (res.success == true) {
                        toast('success', 'เพิ่มรายการเรียบร้อย');
                        $("#qty").val(1);
                        get_data();
                    } else {
                        Swal.fire({
                            icon: "warning",
                            title: res.message,
                            showConfirmButton: false,
                            timer: 2000,
                        });
                    }
                }
            });
        });
        $("#btn-reset").click(function(){
            $.ajax({
                url: "{{ route('request-label.reset_data') }}",
                method: 'GET',
                data: {session_act: session_act},
                success: function(res) {
                    toast('success', 'ลบรายการทั้งหมดเรียบร้อย');
                    get_data();
                }
            });
        });
    });
    function edit_qty_press(i) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            document.getElementById("qty_edit["+i+"]").blur();
        }
    }
    function edit_qty(i) {
        var sku = document.getElementById("sku_edit["+i+"]").value;
        var qty = document.getElementById("qty_edit["+i+"]").value;
        var qty_old = document.getElementById("qty_edit_old["+i+"]").value;
        if (parseInt(qty_old) !== parseInt(qty)) {
            $.ajax({
                url: "{{ route('request-label.edit_qty') }}",
                method: 'GET',
                data: {session_act: session_act, sku: sku, qty: qty},
                success: function(res) {
                    get_data();
                }
            });
        }
    }
    function remove_data(sku) {
        $.ajax({
            url: "{{ route('request-label.remove_data') }}",
            method: 'GET',
            data: {session_act: session_act, sku: sku},
            success: function(res) {
                toast('success', 'ลบรายการเรียบร้อย');
                get_data();
            }
        });
    }
    function get_data() {
        $.ajax({
            url: "{{ route('request-label.get_data') }}",
            method: 'GET',
            data: {session_act: session_act},
            dataType: 'json',
            success: function(data) {
                $('tbody').html('');
                $('tbody').html(data.table_data);
                if (data.count_data > 0) {
                    $("#btn-submit").prop("disabled", false);
                }
            }
        });
    }
    function toast(icon, title) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 2000,
            // timerProgressBar: true,
            // didOpen: (toast) => {
            //     toast.addEventListener('mouseenter', Swal.stopTimer)
            //     toast.addEventListener('mouseleave', Swal.resumeTimer)
            // }
        });
        Toast.fire({
            icon: icon,
            title: title
        });
    }
    function SubmitUploadForm(form) {
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
        $('#upload-loading').show();
        $('#upload-submit').hide();
    }
    function SubmitForm(form){
        var label = $("input[name='label']:checked").val();
        if (!label) {
            Swal.fire({
                icon: "warning",
                title: "โปรดเลือกขนาดสติ๊กเกอร์!",
                timer: 2000,
            });
            return false;
        }
        var barcode_color = $("input[name='barcode_color']:checked").val();
        if (!barcode_color) {
            Swal.fire({
                icon: "warning",
                title: "โปรดเลือกสีสติ๊กเกอร์!",
                timer: 2000,
            });
            return false;
        }
        Swal.fire({
            icon: "warning",
            title: "ยืนยันการบันทึกข้อมูล?",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ยืนยัน!",
            cancelButtonText: "ยกเลิก",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    }
</script>
@endsection
