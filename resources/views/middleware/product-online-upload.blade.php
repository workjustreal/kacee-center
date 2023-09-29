@extends('layouts.master-layout', ['page_title' => "อัปเดตข้อมูลจัดกลุ่มสินค้าออนไลน์"])
@section('css')
    <!-- third party css -->
    <link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/ladda/ladda.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Middleware</a></li>
                        <li class="breadcrumb-item active">จัดกลุ่มสินค้าออนไลน์</li>
                    </ol>
                </div>
                <h4 class="page-title">อัปเดตข้อมูลจัดกลุ่มสินค้าออนไลน์</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" id="upload-form" action="{{ route('pdonline.category.upload') }}"
                            method="POST" enctype="multipart/form-data" onsubmit="return SubmitForm(this);">
                            {{ csrf_field() }}
                            <div class="mb-3">
                                <div class="fallback">
                                    <label for="file" class="form-label">ไฟล์อัปเดต (.xls, .xlsx) <span class="text-danger">* อัปเดตข้อมูลได้ครั้งละไม่เกิน 200 รายการ</span></label><br>
                                    <input id="file" name="file" class="form-control" type="file" title="Upload File"
                                        accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                                    {!! $errors->first('file', '<span class="text-danger">:message</span>') !!}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="detail" class="form-label">ตัวอย่างไฟล์ฟอร์แมต Excel</label>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered w-100">
                                        <tr class="text-center table-light">
                                            <th></th>
                                            <th>A</th>
                                            <th>B</th>
                                            <th>C</th>
                                            <th>D</th>
                                        </tr>
                                        <tr>
                                            <th>1</th>
                                            <th>ลำดับ</th>
                                            <th>รหัสสินค้า</th>
                                            <th>ชื่อสินค้า</th>
                                            <th>หมวดหมู่</th>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>1</td>
                                            <td>DS5500</td>
                                            <td>เครื่องคิดเลข KACEE รุ่น DS5500</td>
                                            <td>Home Kacee</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>2</td>
                                            <td>1ZRASETFR3022WH100</td>
                                            <td>KACEE ชุดราวม่านคู่ สำหรับแขวนผ้าม่าน 2 ชั้น รางผ้าม่านแบบทึบโปร่ง รางอะลูมิเนียมลายไม้ 2 ชั้น รุ่น FR30+22 สี White 1.00 ม.</td>
                                            <td>รางประดับ</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button id="loading" name="loading" class="btn btn-primary hidd" type="button" disabled>
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    รอสักครู่...
                                </button>
                                <button type="submit" id="submit" name="submit" class="ladda-button btn btn-primary" dir="ltr" data-style="zoom-out" title="UPLOAD">อัปโหลด</button>
                            </div>
                        </form>
                        @if ($data)
                        <hr class="mt-2">
                        <h4 class="text-center">ข้อมูลที่อัปโหลด</h4>
                        <form class="form-horizontal" id="update-form" name="update-form" action="{{ route('pdonline.category.update') }}"
                            method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div style="max-height: 600px;overflow-y: auto;">
                            <table data-toggle="table" data-page-size="10" data-buttons-class="xs btn-light"
                            data-pagination="false" class="table-bordered" data-search="false">
                                <thead class="table-light table-light">
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>รหัสสินค้า</th>
                                        <th>ชื่อสินค้า</th>
                                        <th>หมวดหมู่</th>
                                        <th>การทำงาน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php
                                    $count = count($data);
                                    $duplicate_product = 0;
                                    $no_product = 0;
                                    if ($count <= 0) {
                                        $no_product = 1;
                                    }
                                @endphp
                                @for ($i = 0; $i < $count; $i++)
                                    <tr @if ($data[$i]["action"] == "null" || $data[$i]["action"] == "duplicate") class="bg-soft-danger" @endif>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $data[$i]["sku"] }}</td>
                                        <td>{{ $data[$i]["name"] }}</td>
                                        <td>{{ $data[$i]["category"] }}</td>
                                        <td>
                                            @if ($data[$i]["action"] == "insert")
                                                <span class="badge badge-soft-success">ใหม่</span>
                                            @elseif ($data[$i]["action"] == "update")
                                                <span class="badge badge-soft-info">อัปเดต</span>
                                            @elseif ($data[$i]["action"] == "duplicate")
                                                <span class="badge badge-soft-danger">ค่าซ้ำ</span>
                                                @php
                                                    $duplicate_product++;
                                                @endphp
                                            @else
                                                <span class="badge badge-soft-danger">ไม่พบ</span>
                                                @php
                                                    $no_product++;
                                                @endphp
                                            @endif
                                        </td>
                                    </tr>
                                @endfor
                                </tbody>
                            </table>
                            </div>
                            @if ($no_product > 0)
                            <h4 class="text-danger">* พบข้อมูลบางรายการที่ไม่มีในสินค้า</h4>
                            @elseif ($duplicate_product > 0)
                            <h4 class="text-danger">* พบข้อมูลซ้ำกันบางรายการ</h4>
                            @else
                            <div class="mb-3">
                                <button type="submit" name="submit" class="ladda-button btn btn-success mt-3" dir="ltr" data-style="zoom-out" title="SAVE">บันทึก</button>
                            </div>
                            @endif
                        </form>
                        @endif
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
<script src="{{ asset('assets/libs/ladda/ladda.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/loading-btn.init.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    function SubmitForm(form){
        if (document.getElementById('file').value == "") {
            Swal.fire({
                icon: "warning",
                title: "ยังไม่ได้เลือกไฟล์",
                showConfirmButton: false,
                timer: 2000,
            });
            return false;
        }
        $('#loading').show();
        $('#submit').hide();
    }
</script>
@endsection