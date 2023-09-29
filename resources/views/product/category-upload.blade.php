@extends('layouts.master-layout', ['page_title' => "อัปเดตข้อมูลจัดกลุ่มสินค้า"])
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                        <li class="breadcrumb-item active">จัดกลุ่มสินค้า</li>
                    </ol>
                </div>
                <h4 class="page-title">อัปเดตข้อมูลจัดกลุ่มสินค้า</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" id="upload-form" action="{{ route('pd.category.upload') }}"
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
                                        <tr class="text-center">
                                            <th></th>
                                            <th>A</th>
                                            <th>B</th>
                                            <th>C</th>
                                            <th>D</th>
                                            <th>E</th>
                                            <th>F</th>
                                            <th>G</th>
                                            <th>H</th>
                                            <th>I</th>
                                            <th>J</th>
                                            <th>K</th>
                                            <th>L</th>
                                        </tr>
                                        <tr>
                                            <th>1</th>
                                            <th colspan="12" class="text-center">จัดกลุ่มสินค้า | Kacee Application</th>
                                        </tr>
                                        <tr>
                                            <th>2</th>
                                            <th>ลำดับ</th>
                                            <th>รหัสสินค้า</th>
                                            <th>บาร์โค้ด</th>
                                            <th>รายละเอียด</th>
                                            <th>หมวดหมู่ยอดขายรวม</th>
                                            <th>หมวดหมู่หลัก</th>
                                            <th>หมวดหมู่รอง</th>
                                            <th>รุ่นสินค้า</th>
                                            <th>สี</th>
                                            <th>ขนาด</th>
                                            <th>หมวดหมู่แผนกออนไลน์</th>
                                            <th>รายงานรายวัน</th>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>1</td>
                                            <td>ZBYBSHOES200XL</td>
                                            <td>3180000349100</td>
                                            <td>รองเท้าเดินชายหาด สี LAKE BLUE Size.XL</td>
                                            <td>46Home Kacee</td>
                                            <td>HOME KACEE</td>
                                            <td>รองเท้า</td>
                                            <td></td>
                                            <td>LAKE BLUE</td>
                                            <td>XL</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>2</td>
                                            <td>ZBYBSHOES200XXL</td>
                                            <td>3180000349111</td>
                                            <td>รองเท้าเดินชายหาด สี LAKE BLUE Size.XXL</td>
                                            <td>46Home Kacee</td>
                                            <td>HOME KACEE</td>
                                            <td>รองเท้า</td>
                                            <td></td>
                                            <td>LAKE BLUE</td>
                                            <td>XXL</td>
                                            <td></td>
                                            <td></td>
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
                        <form class="form-horizontal" id="update-form" name="update-form" action="{{ route('pd.category.update') }}"
                            method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div style="max-height: 600px;overflow-y: auto;">
                            <table data-toggle="table" data-page-size="10" data-buttons-class="xs btn-light"
                            data-pagination="false" class="table-bordered" data-search="false">
                                <thead class="table-light">
                                    <tr>
                                        <th colspan="13" class="text-center">จัดกลุ่มสินค้า | Kacee Application</th>
                                    </tr>
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>รหัสสินค้า</th>
                                        <th>บาร์โค้ด</th>
                                        <th>รายละเอียด</th>
                                        <th>หมวดหมู่ยอดขายรวม</th>
                                        <th>หมวดหมู่หลัก</th>
                                        <th>หมวดหมู่รอง</th>
                                        <th>รุ่นสินค้า</th>
                                        <th>สี</th>
                                        <th>ขนาด</th>
                                        <th>หมวดหมู่แผนกออนไลน์</th>
                                        <th>รายงานรายวัน</th>
                                        <th>การทำงาน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php
                                    $count = count($data);
                                    $no_product = 0;
                                    if ($count <= 0) {
                                        $no_product = 1;
                                    }
                                @endphp
                                @for ($i = 0; $i < $count; $i++)
                                    <tr @if ($data[$i]["action"] == "null") class="bg-soft-danger" @endif>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $data[$i]["stkcod"] }}</td>
                                        <td>{{ $data[$i]["barcod"] }}</td>
                                        <td>{{ $data[$i]["stkdes"] }}</td>
                                        <td>{{ $data[$i]["sale_category"] }}</td>
                                        <td>{{ $data[$i]["main_category"] }}</td>
                                        <td>{{ $data[$i]["sec_category"] }}</td>
                                        <td>{{ $data[$i]["model"] }}</td>
                                        <td>{{ $data[$i]["color_code"] }}</td>
                                        <td>{{ $data[$i]["size"] }}</td>
                                        <td>{{ $data[$i]["online_category"] }}</td>
                                        <td>{{ $data[$i]["daily_category"] }}</td>
                                        <td>
                                            @if ($data[$i]["action"] == "insert")
                                                <span class="badge badge-soft-success">ใหม่</span>
                                            @elseif ($data[$i]["action"] == "update")
                                                <span class="badge badge-soft-info">อัปเดต</span>
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