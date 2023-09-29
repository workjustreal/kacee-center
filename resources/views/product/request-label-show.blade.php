@extends('layouts.master-layout', ['page_title' => "ร้องขอสติ๊กเกอร์"])
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
                        <li class="breadcrumb-item active">ร้องขอสติ๊กเกอร์ #{{ $id }}</li>
                    </ol>
                </div>
                <h4 class="page-title">ร้องขอสติ๊กเกอร์ #{{ $id }}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">รายละเอียด</h4>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <input type="hidden" class="form-control" id="request_id" name="request_id" value="{{ $id }}">
                                <h5 class="mt-0">ผู้ร้องขอ:</h5>
                                <p>{{ $header->name . ' ' . $header->surname }} @if($header->nickname!="")({{ $header->nickname }})@endif</p>
                            </div>
                            <div class="mb-3">
                                <h5 class="mt-0">หน่วยงาน/แผนก:</h5>
                                <p>{{ $header->dept_name }}</p>
                            </div>
                            <div class="mb-3">
                                <h5 class="mt-0">วันที่ร้องขอ:</h5>
                                <p>{{ \Carbon\Carbon::parse($header->created_at)->format('d/m/Y H:i:s') }}</p>
                            </div>
                            <div class="mb-3">
                                <h5 class="mt-0">รหัสสินค้ารวม(รหัส):</h5>
                                <p>{{ $header->sku_total }}</p>
                            </div>
                            <div class="mb-3">
                                <h5 class="mt-0">จำนวนรวม(ดวง):</h5>
                                <p>{{ $header->qty_total }}</p>
                            </div>
                            <div class="mb-3">
                                <h5 class="mt-0">หมายเหตุ:</h5>
                                <p>{{ $header->remark }}</p>
                            </div>
                            <div class="mb-3">
                                <h5 class="mt-0">สถานะเอกสาร:</h5>
                                <div class="status_text">
                                @if ($header->status == 1)
                                    <span class="badge bg-secondary fw-normal">ร้องขอ</span>
                                @elseif ($header->status == 2)
                                    <span class="badge bg-warning fw-normal">รอคิวปริ้น</span>
                                @elseif ($header->status == 3)
                                    <span class="badge bg-success fw-normal">เสร็จสิ้น</span>
                                @endif
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
                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="header-title">รายการที่ร้องขอ</h4>
                        <div class="form-check float-end">
                            <input type="checkbox" class="form-check-input" id="sortSKU" name="sortSKU" value="1">
                            <label class="form-check-label" for="sortSKU">เรียงตามรหัสสินค้า</label>
                        </div>
                    </div>
                    <table id="table" data-toggle="table" data-page-size="10" data-buttons-class="xs btn-light" data-pagination="true"
                        class="table-bordered" data-search="false" data-ajax="ajaxRequest" data-query-params="queryParams">
                        <thead class="table-light">
                            <tr>
                                <th data-field="no" data-sortable="false" data-width="100">ลำดับ</th>
                                <th data-field="sku" data-sortable="false">รหัสสินค้า</th>
                                <th data-field="barcode" data-sortable="false">บาร์โค้ด</th>
                                <th data-field="name" data-sortable="false">ชื่อสินค้า</th>
                                <th data-field="qty" data-sortable="false" data-width="150">จำนวน(ดวง)</th>
                            </tr>
                        </thead>
                    </table>
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
                        <label for="printer_id">เลือกปริ้นเตอร์</label>
                        <select class="form-select" name="printer_id" id="printer_id">
                            <option value="-" selected>เลือกปริ้นเตอร์</option>
                            @foreach ($printer as $printer)
                                <option value="{{ $printer["id"] }}">
                                    {{ $printer["name"] }} {{ ($printer["role"]==1) ? " (Admin)" : "" }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-4">
                        <a class="btn btn-secondary" href="{{ url('product/request-label') }}">ย้อนกลับ</a>
                        <button type="button" class="btn btn-success mx-2 hidd" id="btn-print" onclick="printLabelConfirmation();">พิมพ์</button>
                    </div>
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
<!-- third party js ends -->
<script type="text/javascript">
    var $table = $('#table');
    $(function () {
        print_status();
        $table.bootstrapTable({
            sortStable: true
        });
        $("#sortSKU").click(function () {
            $table.bootstrapTable('refreshOptions', {sortStable: $('#sortSKU').prop('checked')});
        });
        $("#printer_id").change(function () {
            if ($(this).val() == "-") {
                $("#btn-print").hide();
            } else {
                $("#btn-print").show();
            }
        })
    });
    function queryParams(params) {
        params.request_id = $("#request_id").val();
        params.sortSKU = $("input[name=sortSKU]:checked").val();
        return params;
    }
    function ajaxRequest(params) {
        var url = "{{ url('product/request-label/show-search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function printLabelConfirmation() {
        var request_id = document.getElementById('request_id').value;
        var printer_id = document.getElementById('printer_id').value;
        var sortSKU = $("input[name='sortSKU']:checked").val();
        var label = $("input[name='label']:checked").val();
        var barcode_color = $("input[name='barcode_color']:checked").val();
        if (!label) {
            Swal.fire({
                icon: "warning",
                title: "โปรดเลือกขนาดสติ๊กเกอร์!",
                timer: 2000,
            });
            return false;
        }
        if (!barcode_color) {
            Swal.fire({
                icon: "warning",
                title: "โปรดเลือกสีสติ๊กเกอร์!",
                timer: 2000,
            });
            return false;
        }
        if (printer_id == "-") {
            Swal.fire({
                icon: "warning",
                title: "โปรดเลือกเครื่องปริ้น!",
                timer: 2000,
            });
            return false;
        }
        Swal.fire({
            icon: "warning",
            title: "คุณต้องการปริ้นสติ๊กเกอร์ใช่ไหม?",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ปริ้น!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                const params = {
                    request_id: request_id,
                    printer_id: printer_id,
                    sortSKU: sortSKU,
                    label: label,
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
                return fetch(`/product/request-label/print-label`, options)
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
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    Swal.fire({
                        icon: "warning",
                        title: result.value.message,
                        timer: 2000,
                    });
                }
            }
        });
    }
    function print_status() {
        var request_id = document.getElementById('request_id').value;
        $.ajax({
            url: "{{ url('product/request-label/print_status/') }}/"+request_id,
            method: "GET",
            dataType: "json",
            success: function (res) {
                if (res.success == true) {
                    $('.status_text').html(res.message);
                }
                setTimeout(print_status, 5000);
            },
        });
    }
</script>
@endsection
