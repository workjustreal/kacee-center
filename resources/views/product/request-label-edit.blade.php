@extends('layouts.master-layout', ['page_title' => "แก้ไขร้องขอสติ๊กเกอร์"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/css/label-color.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Product</a></li>
                        <li class="breadcrumb-item active">แก้ไขร้องขอสติ๊กเกอร์ #{{ $id }}</li>
                    </ol>
                </div>
                <h4 class="page-title">แก้ไขร้องขอสติ๊กเกอร์ #{{ $id }}</h4>
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
                        method="POST" enctype="multipart/form-data" action="{{ route('request-label.update') }}" onsubmit="return SubmitForm(this);">
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control" id="session_act" name="session_act" value="edit">
                        <input type="hidden" class="form-control" id="request_id" name="request_id" value="{{ $id }}">
                        <div class="mt-4">
                            <label for="label_size" class="form-label">ขนาดสติ๊กเกอร์</label><br>
                            @foreach ($label as $label)
                            <div class="radio radio-success form-check-inline ml-2 mb-2 align-top">
                                <input type="radio" id="label_{{ $label["label"] }}" value="{{ $label["label"] }}" name="label" @if ($label["label"]==$header->label) checked @endif>
                                <label for="label_{{ $label["label"] }}">
                                    {{ $label["label_detail"] }}
                                    <br><img src="{{asset('assets/images/'.$label["label"].'.png')}}" width="140" class="rounded border">
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <div class="radio radio-success form-check-inline ml-2 mb-2 align-top">
                                <input type="radio" id="barcode_white" value="white" name="barcode_color" @if ($header->label_color == "white") checked @endif>
                                <label for="barcode_white">
                                    ขาว
                                    <br><span class="dotwhite"></span>
                                </label>
                            </div>
                            <div class="radio radio-success form-check-inline ml-2 mb-2 align-top">
                                <input type="radio" id="barcode_pink" value="pink" name="barcode_color" @if ($header->label_color == "pink") checked @endif>
                                <label for="barcode_pink">
                                    ชมพู
                                    <br><span class="dotpink"></span>
                                </label>
                            </div>
                            <div class="radio radio-success form-check-inline ml-2 mb-2 align-top">
                                <input type="radio" id="barcode_yellow" value="yellow" name="barcode_color" @if ($header->label_color == "yellow") checked @endif>
                                <label for="barcode_yellow">
                                    เหลือง
                                    <br><span class="dotyel"></span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5 class="mt-0">หมายเหตุ:</h5>
                            <p><input type="text" class="form-control" id="remark" name="remark" value="{{ $header->remark }}" autocomplete="off" placeholder="หมายเหตุ"></p>
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
<!-- third party js ends -->
<script type="text/javascript">
    var session_act = document.getElementById("session_act").value;
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
