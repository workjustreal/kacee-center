@extends('layouts.master-layout', ['page_title' => "สร้างบาร์โค้ดสินค้า"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
<style>
    /* div.divInput {
        height: 500px;
        overflow-y: scroll;
    } */
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
                        <li class="breadcrumb-item active">บาร์โค้ดสินค้า</li>
                    </ol>
                </div>
                <h4 class="page-title">สร้างบาร์โค้ดสินค้า</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5 col-sm-12 mb-2">
                            <form class="form-horizontal" id="upload-form" name="upload-form"
                                method="POST" enctype="multipart/form-data" action="javascript:void(0)">
                                {{ csrf_field() }}
                                <div class="d-flex justify-content-between">
                                    <h4>รหัสสินค้าใหม่</h4>
                                    <div class="form-check form-check-primary pt-2">
                                        <input class="form-check-input" type="checkbox" value="" id="chkFileUpload">
                                        <label class="form-check-label" for="chkFileUpload">อัปโหลดไฟล์</label>
                                    </div>
                                </div>
                                <div class="mb-3 update-file hidd">
                                    <div class="fallback">
                                        <label for="file" class="form-label">ไฟล์ (.xlsx, .xls) </label><br>
                                        <input id="file" name="file" class="form-control" type="file" title="Upload File" onchange="$('#btn-upload').submit();"
                                            accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                                        {{-- {!! $errors->first('file', '<span class="text-danger">:message</span>') !!} --}}
                                    </div>
                                    <div class="mt-2">
                                        <label for="detail" class="form-label">ตัวอย่างไฟล์ฟอร์แมต</label>
                                        <table class="table table-sm table-bordered" width="100%">
                                            <tr>
                                                <th>รหัสสินค้า</th>
                                                <th>รายละเอียด</th>
                                            </tr>
                                            <tr>
                                                <td>ZKTEQVBSBL20</td>
                                                <td>เครื่องหั่นผักตั้งโต๊ะแบบมือหมุน สีฟ้า</td>
                                            </tr>
                                            <tr>
                                                <td>ZKTEQVBSGN30</td>
                                                <td>เครื่องหั่นผักตั้งโต๊ะแบบมือหมุน สีเขียว</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <button type="submit" id="btn-upload" class="btn btn-primary hidd">Submit</button>
                            </form>
                            <hr class="mt-2">
                            <form class="form-horizontal" id="generateForm" action="{{ route('barcode.generate') }}" method="POST"
                                enctype="multipart/form-data" onsubmit="return SubmitFormGenerate(this);">
                                {{ csrf_field() }}
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="input-group my-3">
                                    <span class="input-group-text">หมายเหตุ</span>
                                    <input type="text" class="form-control" id="remark" name="remark"
                                        value="{{ old('remark') }}" placeholder="ระบุหมายเหตุ" autocomplete="off">
                                </div>
                                <div class="mt-2 mb-5">
                                    <button type="submit" class="btn btn-primary">สร้างบาร์โค้ด</button>
                                    <button type="reset" class="btn btn-secondary"
                                        onclick="location.reload()">ยกเลิก</button>
                                </div>
                                <div class="divInput">
                                @for ($i = 0; $i < 50; $i++)
                                    <div class="input-group mb-1">
                                        <span class="input-group-text bg-soft-primary border border-primary text-dark"
                                            style="width: 50px;">{{ $i + 1 }}.</span>
                                        <input type="text" class="form-control border border-primary" id="sku[{{ $i }}]"
                                            name="sku[]" value="{{ old('sku.'.$i) }}"
                                            placeholder="รหัสสินค้า ({{ $i + 1 }})" autocomplete="off"
                                            style="text-transform: uppercase;">
                                        <input type="text" class="form-control border border-primary" id="description[{{ $i }}]"
                                        name="description[]" value="{{ old('description.'.$i) }}"
                                        placeholder="ชื่อสินค้า, รายละเอียด ({{ $i + 1 }})" autocomplete="off"
                                        style="width: 20%;">
                                    </div>
                                    <small class="text-danger sku-msg" id="sku_msg[{{ $i }}]"></small>
                                    @if(session()->has('errors'))
                                        @foreach (session()->get('errors') as $error)
                                            @if ($error->status == "error" && $error->index == $i)
                                                <small class="text-danger sku-msg">{{ $error->message }}</small>
                                            @endif
                                        @endforeach
                                    @endif
                                @endfor
                                </div>
                            </form>
                        </div>
                        <div class="col-md-7 col-sm-12 mb-2">
                        <h4>รายการที่สร้าง</h4>
                        <hr>
                        @if(session()->has('data'))
                        <div class="alert alert-success">
                            สร้างบาร์โค้ดสำเร็จ!
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-bordered" data-toggle="table" data-page-size="100"
                                data-buttons-class="xs btn-light" data-pagination="false" data-search="false">
                                <thead class="table-light">
                                    <tr>
                                        <th data-field="no" data-sortable="false">ลำดับ</th>
                                        <th data-field="sku" data-sortable="false">รหัสสินค้า</th>
                                        <th data-field="description" data-sortable="false">ชื่อ, รายละเอียด</th>
                                        <th data-field="barcode" data-sortable="false">บาร์โค้ดสินค้า</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(session()->has('data'))
                                    @php
                                    $data = session()->get('data');
                                    @endphp
                                    @foreach ($data as $list)
                                    <tr>
                                        <td class="lh35">{{$loop->index+1}}</td>
                                        <td class="lh35">{{$list->sku}}</td>
                                        <td class="lh35">{{$list->description}}</td>
                                        <td class="lh35">{{$list->barcode}}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
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
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    $(document).ready(function() {
        setTimeout(() => {
            var id = '{{ Session::get("generate_id") }}';
            if (id != "") {
                excelExport(id);
            }
        }, 1000);
        $("#chkFileUpload").change(function(){
            $(".update-file").toggle();
        });
        $('#upload-form').submit(function(e) {
            $(".update-file").hide();
            document.getElementById("generateForm").reset();
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ route('barcode.upload-generate')}}",
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                success: (data) => {
                    this.reset();
                    // console.log(data);
                    if (data.success) {
                        for (var i = 0; i < data.result.length; i++) {
                            document.getElementById("sku["+i+"]").value = data.result[i].sku;
                            document.getElementById("description["+i+"]").value = data.result[i].description;
                        }
                    }
                },
                error: function(data){
                    console.log(data);
                }
            });
        });
    });
    function excelExport(id) {
        var url = "/product/barcode-export/"+id;
        window.location = url;
    }
    function SubmitFormGenerate(frm){
            $(".sku-msg").html("");
            var sku_values = $("input[name='sku[]']").map(function(){if($(this).val()!=""){return $(this).val();}}).get();
            if (sku_values == "") {
                Swal.fire({
                    icon: "warning",
                    title: "โปรดใส่รหัสสินค้าและชื่อ,รายละเอียด",
                    timer: 2000,
                    showConfirmButton: false,
                });
                return false;
            } else {
                var skus = $("input[name='sku[]']").map(function(){return $(this).val().trim();}).get();
                var descriptions = $("input[name='description[]']").map(function(){return $(this).val().trim();}).get();
                var error = 0;
                for (var i = 0; i < skus.length; i++) {
                    if (skus[i].length >= 5) {
                        if (descriptions[i] == "") {
                            document.getElementById("sku_msg["+i+"]").innerHTML = "* โปรดใส่ชื่อสินค้า, รายละเอียด";
                            document.getElementById("description["+i+"]").focus();
                            error++;
                        } else {
                            if (skus[i].search(/^[a-zA-Z0-9ก-ฮ-#\s+]+$/) == -1) {
                                var msg = "รหัสสินค้ากรอกได้เฉพาะตัวเลข 0-9 ตัวอักษร A-Z, ก-ฮ, และอักขระพิเศษเฉพาะ # และ (-) ขีด เท่านั้น !";
                                Swal.fire({
                                    icon: "warning",
                                    title: "โปรดโปรดตรวจสอบข้อมูลให้ถูกต้อง",
                                    html: '<span class="text-danger">'+msg+'</span>',
                                    timer: 3000,
                                    showConfirmButton: false,
                                });
                                document.getElementById("sku_msg["+i+"]").innerHTML = msg;
                                document.getElementById("sku["+i+"]").focus();
                                error++;
                            } else {
                                if(!isNaN(skus[i])){
                                    var msg = "รหัสสินค้าห้ามเป็นตัวเลขอย่างเดียว ต้องมีตัวอักษรด้วย!";
                                    Swal.fire({
                                        icon: "warning",
                                        title: "โปรดโปรดตรวจสอบข้อมูลให้ถูกต้อง",
                                        html: '<span class="text-danger">'+msg+'</span>',
                                        timer: 3000,
                                        showConfirmButton: false,
                                    });
                                    document.getElementById("sku_msg["+i+"]").innerHTML = msg;
                                    document.getElementById("sku["+i+"]").focus();
                                    error++;
                                }
                            }
                        }
                    } else {
                        if (skus[i] != "") {
                            document.getElementById("sku_msg["+i+"]").innerHTML = "* โปรดใส่รหัสสินค้า 5 หลักขึ้นไป";
                            document.getElementById("sku["+i+"]").focus();
                            error++;
                        }
                    }
                }
                if (error > 0) {
                    return false;
                } else {
                    Swal.fire({
                        icon: "warning",
                        title: "ยืนยันการสร้างบาร์โค้ดหรือไม่",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "ยืนยัน!",
                        cancelButtonText: "ยกเลิก",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            frm.submit();
                        }
                    });
                    return false;
                }
            }
        }
</script>
@endsection
