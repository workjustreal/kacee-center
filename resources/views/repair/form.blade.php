@extends('layouts.master-nopreloader-layout', ['page_title' => 'ฟอร์มแจ้งซ่อม'])
@section('css')
    <link href="{{ asset('assets/libs/selectize/selectize.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .hide {
            display: none;
        }

        html * {
            box-sizing: border-box;
        }

        p {
            margin: 0;
        }

        .upload__btn-box {
            margin-bottom: 10px;
        }

        .upload__img-wrap {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }

        .upload__img-box {
            width: 80px;
            padding: 0 10px;
            margin-bottom: 12px;
        }

        /* .upload__img-close {
                                                            width: 24px;
                                                            height: 24px;
                                                            border-radius: 50%;
                                                            background-color: rgba(0, 0, 0, 0.5);
                                                            position: absolute;
                                                            top: 10px;
                                                            right: 10px;
                                                            text-align: center;
                                                            line-height: 24px;
                                                            z-index: 1;
                                                            cursor: pointer;
                                                        } */

        .upload__img-close {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.5);
            font-size: 14px;
            color: white;
        }

        .img-bg {
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            position: relative;
            padding-bottom: 100%;
        }
    </style>
@endsection

@section('content')
    {{-- Form electric --}}
    @if ($request == 'A03050100')
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Repair</a></li>
                                <li class="breadcrumb-item active">ใบแจ้งซ่อม แผนกไฟฟ้าและสุขาภิบาล</li>
                            </ol>
                        </div>
                        <h4 class="page-title">ใบแจ้งซ่อม แผนกไฟฟ้าและสุขาภิบาล</h4>
                    </div>
                </div>
            </div>
            <!-- end page title  -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card border">
                                <div class="card-header ">
                                    <b>ส่วนที่ 1 ผู้แจ้งซ่อม กรอกรายละเอียดให้ครบถ้วนชัดเจน</b>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('repair.store') }}" id="soform" class="wow fadeInLeft"
                                        method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-md-12 col-lg-8">
                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group ">
                                                    <label class="control-label">วันที่</label>
                                                    <input type="date"
                                                        class="form-control form-control-md form-control-required"
                                                        id="order_date" name="order_date"
                                                        value="@php echo date("Y-m-d"); @endphp" required>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group ">
                                                    <label class="control-label">อาคาร</label>
                                                    <select class="form-select form-select-md form-control-required"
                                                        aria-label=".form-select-md" id="order_location"
                                                        name="order_location" required>
                                                        <option value="" selected="selected"> กรุณาเลือก
                                                        </option>

                                                        @foreach ($location as $item)
                                                            <option value="{{ $item->location }}">อาคาร :
                                                                {{ $item->location }}
                                                            </option>
                                                        @endforeach
                                                        <option value="other"> อื่นๆ </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group" id="sl_build">
                                                    <label class="control-label">สถานที่ซ่อม</label>
                                                    <select name="order_address" id="order_address"
                                                        class="form-select form-select-md form-control-required"
                                                        aria-label=".form-select-md" required>
                                                        <option value=""> กรุณาเลือก </option>
                                                    </select>
                                                </div>

                                                <div class="form-group" id="sl_other" style="display: none;">
                                                    <label class="control-label">สถานที่อื่นๆ โปรดระบุ</label>
                                                    <input type="text"
                                                        class="form-control form-control-md form-control-required"
                                                        id="address_other" name="address_other" placeholder="กรุณากรอก"
                                                        value="">
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group ">
                                                    <label class="control-label">ประเภทงานซ่อม</label>
                                                    <select class="form-select form-select-md form-control-required"
                                                        aria-label=".form-select-md" id="order_type" name="order_type"
                                                        onchange="otherCheck(this);" required>
                                                        <option value="" selected="selected"> กรุณาเลือก

                                                        </option>
                                                        @foreach ($repair_type as $item)
                                                            @if ($item->dept_id == $request)
                                                                <option value="{{ $item->name }}">{{ $item->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                        <option value="other">อื่นๆ...</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2" id="ifYes" style="display: none;">
                                                <div class="form-group ">
                                                    <label class="control-label">อื่นๆ โปรดระบุ</label>
                                                    <input type="text"
                                                        class="form-control form-control-md form-control-required"
                                                        id="order_other" name="order_other" placeholder="กรุณากรอก"
                                                        value="">
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group">
                                                    <label class="control-label">อุปกรณ์ที่แจ้งซ่อม</label>
                                                    <input type="text"
                                                        class="form-control form-control-md form-control-required"
                                                        id="order_tool" name="order_tool" placeholder="กรุณากรอกอุปกรณ์"
                                                        value="" required>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="fom-group ">
                                                    <label class="control-label">สาเหตุ / อาการเสีย</label>
                                                    <textarea class="form-control form-control-md form-control-required" id="order_detail" name="order_detail"
                                                        placeholder="กรุณากรอกสาเหตุ / อาการเสีย" required rows="4" value=""></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="fom-group ">
                                                    <label class="control-label">รูปภาพประกอบ (** เพิ่มรูปภาพได้ไม่เกิน 3
                                                        รูป, เฉพาะไฟล์นามสกุล .png/.jpg/.jpeg)</label>
                                                    <div class="upload__box">
                                                        <div class="upload__btn-box ">
                                                            <input type="file" id="fileInput" name="order_image[]"
                                                                accept=".jpg,.jpeg,.png"
                                                                class="upload__inputfile form-control form-control-md form-control-required"
                                                                multiple>
                                                        </div>
                                                        <div id="preview" class="row"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="col-lg-8 col-md-12">

                                            <div class="col-lg-12 col-md-12 col-sm-12 pt-3">
                                                <input type="hidden" name="order_dept" value="{{ $request }}">
                                                <input type="hidden" name="status" value="INS">
                                                <a class="btn btn-white" href="{{ url('/repair/repair') }}"><i
                                                        class="fe-arrow-left"></i> ย้อนกลับ</a>
                                                <button type="button" class="btn btn-primary mx-2" id="btn-submit"
                                                    onclick="validateAndConfirm()"><i class="fe-save"></i> บันทึก</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- End Form electric --}}

    {{-- Form Car --}}
    @if ($request == 'A03050200')
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Repair</a></li>
                                <li class="breadcrumb-item active">ใบแจ้งซ่อมแผนกยานยนต์</li>
                            </ol>
                        </div>
                        <h4 class="page-title">ใบแจ้งซ่อมแผนกยานยนต์</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card border">
                                <div class="card-header ">
                                    <b>ส่วนที่ 1 ผู้แจ้งซ่อม กรอกรายละเอียดให้ครบถ้วนชัดเจน</b>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('repair.store') }}" id="soform" class="wow fadeInLeft"
                                        method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12 col-lg-8">
                                                <div class="col-md-12 col-lg-8 pt-2">
                                                    <div class="form-group">
                                                        <label class="control-label">วันที่แจ้งซ่อม</label>
                                                        <input type="date"
                                                            class="form-control form-control-md form-control-required"
                                                            id="order_date" name="order_date"
                                                            value="@php echo date("Y-m-d"); @endphp" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8">
                                                <div class="col-md-12 col-lg-8 pt-2">
                                                    <div class="form-group">
                                                        <label class="control-label">ทะเบียนรถ</label>
                                                        <select name="car_id" required
                                                            class="form-control form-control-md selectize-programmatic">
                                                            <option value="" selected="selected">
                                                                กรุณาเลือกทะเบียนรถ </option>
                                                            @foreach ($car as $row)
                                                                <option value="{{ $row->car_id }}">
                                                                    {{ $row->car_id }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8">
                                                <div class="col-md-12 col-lg-8 pt-2">
                                                    <div class="form-group">
                                                        <label class="control-label">เลขไมล์ล่าสุด</label>
                                                        <input type="number" placeholder="กรอกเลขไมล์"
                                                            class="form-control form-control-md form-control-required"
                                                            id="car_mile" name="car_mile" min="0" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8">
                                                <div class="col-md-12 col-lg-8 pt-2">
                                                    <div class="form-group">
                                                        <label class="control-label">ประเภทงาน</label>
                                                        <select class="form-select form-select-md form-control-required"
                                                            aria-label=".form-select-md" id="order_type"
                                                            name="order_type" required>
                                                            <option value="" selected="selected">
                                                                กรุณาเลือกประเภทงาน </option>
                                                            <option value="งานเช็คระยะ">งานเช็คระยะ</option>
                                                            <option value="งานระบบ">งานระบบ</option>
                                                            <option value="งานล้อ">งานล้อ</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- start Job 1 --}}
                                            <div class="col-md-12 col-lg-12 pt-3 pb-3 box A hide" id="">
                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="oil" value="ถ่ายน้ำมันเครื่อง"
                                                        required>
                                                    <label class="form-check-label"
                                                        for="oil">ถ่ายน้ำมันเครื่อง</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="gear" value="ถ่ายน้ำมันเกียร์"
                                                        required>
                                                    <label class="form-check-label"
                                                        for="gear">ถ่ายน้ำมันเกียร์</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="filter" value="กรองเครื่อง" required>
                                                    <label class="form-check-label" for="filter">กรองเครื่อง</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="Diff_oil" value="น้ำมันเฟืองท้าย"
                                                        required>
                                                    <label class="form-check-label" for="Diff_oil">น้ำมันเฟืองท้าย</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="solar" value="กรองโซล่า" required>
                                                    <label class="form-check-label" for="solar">กรองโซล่า</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="filter_water" value="กรองดักน้ำ"
                                                        required>
                                                    <label class="form-check-label" for="filter_water">กรองดักน้ำ</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="brake" value="น้ำมันเบรค" required>
                                                    <label class="form-check-label" for="brake">น้ำมันเบรค</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="power" value="น้ำมันเพาเวอร์"
                                                        required>
                                                    <label class="form-check-label" for="power">น้ำมันเพาเวอร์</label>
                                                </div>

                                            </div>
                                            {{-- end job 1 --}}

                                            {{-- start Job 2 --}}
                                            <div class="col-md-12 col-lg-12 pt-3 pb-3 box B hide" id="">
                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="sbrake" value="ระบบเบรค" required>
                                                    <label class="form-check-label" for="sbrake">ระบบเบรค</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="eletric" value="ระบบไฟฟ้า" required>
                                                    <label class="form-check-label" for="eletric">ระบบไฟฟ้า</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="machine" value="ระบบเครื่อง" required>
                                                    <label class="form-check-label" for="machine">ระบบเครื่อง</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="down" value="ระบบช่วงล่าง" required>
                                                    <label class="form-check-label" for="down">ระบบช่วงล่าง</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="sgear" value="ระบบเกียร์" required>
                                                    <label class="form-check-label" for="sgear">ระบบเกียร์</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="clutch" value="ระบบคลัช" required>
                                                    <label class="form-check-label" for="clutch">ระบบคลัช</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="air" value="ระบบแอร์" required>
                                                    <label class="form-check-label" for="air">ระบบแอร์</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="fuel" value="ระบบเชื้อเพลิง"
                                                        required>
                                                    <label class="form-check-label" for="fuel">ระบบเชื้อเพลิง</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="color" value="เคลมทำสีประกัน"
                                                        required>
                                                    <label class="form-check-label" for="color">เคลมทำสีประกัน</label>
                                                </div>

                                            </div>
                                            {{-- end job 2 --}}

                                            {{-- start Job 3 --}}
                                            <div class="col-md-12 col-lg-12 pt-3 pb-3 box C hide" id="">
                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="change" value="เปลี่ยนยาง" required>
                                                    <label class="form-check-label" for="change">เปลี่ยนยาง</label>

                                                </div>

                                                <div class="form-check form-check-inline range_full">
                                                    <input class="form-check-input checkboxes" type="checkbox"
                                                        name="order_tool[]" id="swap" value="สลับยาง" required>
                                                    <label class="form-check-label" for="swap">สลับยาง</label>
                                                </div>
                                            </div>
                                            {{-- end job 3 --}}
                                        </div>

                                        <div class="col-md-12 col-lg-8">
                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="fom-group">
                                                    <label class="control-label">สาเหตุ / อาการเสีย</label>
                                                    <textarea class="form-control form-control-md form-control-required" id="order_detail" name="order_detail"
                                                        placeholder="กรุณากรอกสาเหตุ / อาการเสีย" required rows="4" value=""></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-lg-8">
                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="fom-group ">
                                                    <label class="control-label">รูปภาพประกอบ (** เพิ่มรูปภาพได้ไม่เกิน 3
                                                        รูป, เฉพาะไฟล์นามสกุล .png/.jpg/.jpeg)</label>
                                                    <div class="upload__box">
                                                        <div class="upload__btn-box ">
                                                            <input type="file" id="fileInput" name="order_image[]"
                                                                accept=".jpg,.jpeg,.png"
                                                                class="upload__inputfile form-control form-control-md form-control-required"
                                                                multiple>
                                                        </div>
                                                        <div id="preview" class="row"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-lg-8">
                                            <hr class="col-lg-8 col-md-12">
                                        </div>

                                        <div class="col-lg-4 col-md-12 col-sm-12 pt-3">
                                            <input type="hidden" name="order_dept" value="{{ $request }}">
                                            <input type="hidden" name="status" value="INS">
                                            <a class="btn btn-white" href="{{ url('/repair/repair') }}"><i
                                                    class="fe-arrow-left"></i> ย้อนกลับ</a>
                                            <button type="button" class="btn btn-primary mx-2" id="btn-submit"
                                                onclick="validateAndConfirm()"><i class="fe-save"></i> บันทึก</button>
                                        </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        </div>
    @endif
    {{-- End Form Car --}}

    {{-- Form maintenance --}}
    @if ($request == 'A03060100')
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Repair</a></li>
                                <li class="breadcrumb-item active">ใบแจ้งซ่อมแผนกซ่อมบำรุง</li>
                            </ol>
                        </div>
                        <h4 class="page-title">ใบแจ้งซ่อมแผนกซ่อมบำรุง</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card border">
                                <div class="card-header ">
                                    <b>ส่วนที่ 1 ผู้แจ้งซ่อม กรอกรายละเอียดให้ครบถ้วนชัดเจน</b>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('repair.store') }}" id="soform" class="wow fadeInLeft"
                                        method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-md-12 col-lg-8">
                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group">
                                                    <label class="control-label">วันที่</label>
                                                    <input type="date"
                                                        class="form-control form-control-md form-control-required"
                                                        id="order_date" name="order_date"
                                                        value="@php echo date("Y-m-d"); @endphp" required>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group ">
                                                    <label class="control-label">อาคาร</label>
                                                    <select class="form-select form-select-md form-control-required"
                                                        aria-label=".form-select-md" id="order_location"
                                                        name="order_location" required>
                                                        <option value="" selected="selected"> กรุณาเลือก
                                                        </option>

                                                        @foreach ($location as $item)
                                                            <option value="{{ $item->location }}">อาคาร :
                                                                {{ $item->location }}
                                                            </option>
                                                        @endforeach
                                                        <option value="other"> อื่นๆ </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                {{-- <div class="form-group ">
                                                    <label class="control-label">สถานที่ซ่อม</label>
                                                    <select name="order_address" id="order_address"
                                                        class="form-select form-select-md form-control-required"
                                                        aria-label=".form-select-md" required>
                                                        <option value=""> กรุณาเลือก </option>
                                                    </select>
                                                </div> --}}

                                                <div class="form-group" id="sl_build">
                                                    <label class="control-label">สถานที่ซ่อม</label>
                                                    <select name="order_address" id="order_address"
                                                        class="form-select form-select-md form-control-required"
                                                        aria-label=".form-select-md" required>
                                                        <option value=""> กรุณาเลือก </option>
                                                    </select>
                                                </div>

                                                <div class="form-group" id="sl_other" style="display: none;">
                                                    <label class="control-label">สถานที่อื่นๆ โปรดระบุ</label>
                                                    <input type="text"
                                                        class="form-control form-control-md form-control-required"
                                                        id="address_other" name="address_other" placeholder="กรุณากรอก"
                                                        value="">
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group">
                                                    <label class="control-label">ประเภทงานซ่อม</label>
                                                    <select class="form-select form-select-md form-control-required"
                                                        aria-label=".form-select-md" id="order_type" name="order_type"
                                                        onchange="otherCheck(this);" required>
                                                        <option value="" selected="selected"> กรุณาเลือก
                                                        </option>
                                                        @foreach ($repair_type as $item)
                                                            @if ($item->dept_id == $request)
                                                                <option value="{{ $item->name }}">{{ $item->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                        <option value="other">อื่นๆ...</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2" id="ifYes" style="display: none;">
                                                <div class="form-group">
                                                    <label class="control-label">อื่นๆ โปรดระบุ</label>
                                                    <input type="text" class="form-control form-control-md"
                                                        id="order_other" name="order_other" placeholder="กรุณากรอก"
                                                        value="">
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group">
                                                    <label class="control-label">อุปกรณ์ที่แจ้งซ่อม</label>
                                                    <input type="text"
                                                        class="form-control form-control-md form-control-required"
                                                        id="order_tool" name="order_tool" placeholder="กรุณากรอกอุปกรณ์"
                                                        value="" required>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="fom-group">
                                                    <label class="control-label">สาเหตุ / อาการเสีย</label>
                                                    <textarea class="form-control form-control-md form-control-required" id="order_detail" name="order_detail"
                                                        placeholder="กรุณากรอกสาเหตุ / อาการเสีย" required rows="4" value=""></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="fom-group ">
                                                    <label class="control-label">รูปภาพประกอบ (** เพิ่มรูปภาพได้ไม่เกิน 3
                                                        รูป, เฉพาะไฟล์นามสกุล .png/.jpg/.jpeg)</label>
                                                    <div class="upload__box">
                                                        <div class="upload__btn-box ">
                                                            <input type="file" id="fileInput" name="order_image[]"
                                                                accept=".jpg,.jpeg,.png"
                                                                class="upload__inputfile form-control form-control-md form-control-required"
                                                                multiple>
                                                        </div>
                                                        <div id="preview" class="row"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="col-lg-8 col-md-12">

                                            <div class="col-lg-12 col-md-12 col-sm-12 pt-3">
                                                <input type="hidden" name="order_dept" value="{{ $request }}">
                                                <input type="hidden" name="status" value="INS">
                                                <a class="btn btn-white" href="{{ url('/repair/repair') }}"><i
                                                        class="fe-arrow-left"></i> ย้อนกลับ</a>
                                                <button type="button" class="btn btn-primary mx-2" id="btn-submit"
                                                    onclick="validateAndConfirm()"><i class="fe-save"></i> บันทึก</button>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    @endif
    {{-- End Form maintenance --}}

    {{-- Form IT --}}
    @if ($request == 'A01100100')
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Repair</a></li>
                                <li class="breadcrumb-item active">ใบแจ้งซ่อมแผนกไอที</li>
                            </ol>
                        </div>
                        <h4 class="page-title">ใบแจ้งซ่อมแผนกไอที</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card border">
                                <div class="card-header ">
                                    <b>ส่วนที่ 1 ผู้แจ้งซ่อม กรอกรายละเอียดให้ครบถ้วนชัดเจน</b>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('repair.store') }}" id="soform" class="wow fadeInLeft"
                                        method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-md-12 col-lg-8">
                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group">
                                                    <label class="control-label">วันที่</label>
                                                    <input type="date"
                                                        class="form-control form-control-md form-control-required"
                                                        id="order_date" name="order_date"
                                                        value="@php echo date("Y-m-d"); @endphp" required>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group ">
                                                    <label class="control-label">อาคาร</label>
                                                    <select class="form-select form-select-md form-control-required"
                                                        aria-label=".form-select-md" id="order_location"
                                                        name="order_location" required>
                                                        <option value="" selected="selected"> กรุณาเลือก
                                                        </option>

                                                        @foreach ($location as $item)
                                                            <option value="{{ $item->location }}">อาคาร :
                                                                {{ $item->location }}
                                                            </option>
                                                        @endforeach
                                                        <option value="other"> อื่นๆ </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group" id="sl_build">
                                                    <label class="control-label">สถานที่ซ่อม</label>
                                                    <select name="order_address" id="order_address"
                                                        class="form-select form-select-md form-control-required"
                                                        aria-label=".form-select-md" required>
                                                        <option value=""> กรุณาเลือก </option>
                                                    </select>
                                                </div>

                                                <div class="form-group" id="sl_other" style="display: none;">
                                                    <label class="control-label">สถานที่อื่นๆ โปรดระบุ</label>
                                                    <input type="text"
                                                        class="form-control form-control-md form-control-required"
                                                        id="address_other" name="address_other" placeholder="กรุณากรอก"
                                                        value="">
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group">
                                                    <label class="control-label">ประเภทงานซ่อม</label>
                                                    <select class="form-select form-select-md form-control-required"
                                                        aria-label=".form-select-md" id="order_type" name="order_type"
                                                        required onchange="otherCheck(this);" required>
                                                        <option value="" selected="selected"> กรุณาเลือก
                                                        </option>
                                                        @foreach ($repair_type as $item)
                                                            @if ($item->dept_id == $request)
                                                                <option value="{{ $item->name }}">{{ $item->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                        <option value="other">อื่นๆ...</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2" id="ifYes" style="display: none;">
                                                <div class="form-group">
                                                    <label class="control-label">อื่นๆ โปรดระบุ</label>
                                                    <input type="text" class="form-control form-control-md"
                                                        id="order_other" name="order_other" placeholder="กรุณากรอก"
                                                        value="">
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="form-group">
                                                    <label class="control-label">อุปกรณ์ที่แจ้งซ่อม</label>
                                                    <input type="text"
                                                        class="form-control form-control-md form-control-required"
                                                        id="order_tool" name="order_tool" placeholder="กรุณากรอกอุปกรณ์"
                                                        value="" required>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="fom-group">
                                                    <label class="control-label">สาเหตุ / อาการเสีย</label>
                                                    <textarea class="form-control form-control-md form-control-required" id="order_detail" name="order_detail"
                                                        placeholder="กรุณากรอกสาเหตุ / อาการเสีย" required rows="4" value=""></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-8 pt-2">
                                                <div class="fom-group ">
                                                    <label class="control-label">รูปภาพประกอบ (** เพิ่มรูปภาพได้ไม่เกิน 3
                                                        รูป, เฉพาะไฟล์นามสกุล .png/.jpg/.jpeg) </label>
                                                    <div class="upload__box">
                                                        <div class="upload__btn-box ">
                                                            <input type="file" id="fileInput" name="order_image[]"
                                                                accept=".jpg,.jpeg,.png"
                                                                class="upload__inputfile form-control form-control-md form-control-required"
                                                                multiple>
                                                        </div>
                                                        <div id="preview" class="row"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="col-lg-8 col-md-12">

                                            <div class="col-lg-12 col-md-12 col-sm-12 pt-3">
                                                <input type="hidden" name="order_dept" value="{{ $request }}">
                                                <input type="hidden" name="status" value="INS">
                                                <a class="btn btn-white" href="{{ url('/repair/repair') }}"><i
                                                        class="fe-arrow-left"></i> ย้อนกลับ</a>
                                                <button type="button" class="btn btn-primary mx-2" id="btn-submit"
                                                    onclick="validateAndConfirm()">
                                                    <i class="fe-save"></i> บันทึก</button>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    @endif
    {{-- End Form IT --}}


@endsection
@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/bootstrap-tables.init.js') }}"></script>
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <!-- third party js ends -->

    <script type="text/javascript">
        $(document).ready(function() {
            $('.selectize-programmatic').selectize();

            var checkboxes = $('.checkboxes');
            checkboxes.change(function() {
                if ($('.checkboxes:checked').length > 0) {
                    checkboxes.removeAttr('required');
                } else {
                    checkboxes.attr('required', 'required');
                }
            });

            $('input[type="file"]').change(function() {
                var files = this.files;
                console.log(files);
                if (files.length > 3) {
                    alert('สามารถใส่รูปภาพได้ไม่เกิน 3 รูป');
                    $(this).val("");
                    return;
                }
                $('#preview').empty();
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var img = $('<img>').attr('src', e.target.result).css('max-width', '100px');
                        var delBtn = $('<button>').text('X').click(function() {
                            var index = Array.from($('#preview img')).indexOf(img.get(0));
                            img.remove();
                            $(this).remove();
                            var inputFiles = $('input[type="file"]')[0].files;
                            var dataTransfer = new DataTransfer();
                            for (var j = 0; j < inputFiles.length; j++) {
                                if (j !== index) {
                                    dataTransfer.items.add(inputFiles[j])
                                }
                            }
                            document.getElementById('fileInput').files = dataTransfer.files;
                        }).addClass('upload__img-close');
                        var container = $('<div>').addClass('col-auto preview-image').append(img)
                            .append(delBtn);
                        $('#preview').append(container);

                        // $('#preview').append(img).append(delBtn);
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#order_location').change(function() {
                var _location = $(this).val();
                otherAddress(_location);
                $('#order_address').html('<option value=""> กรุณาเลือก </option>');
                if (_location == '') {
                    $('#order_address').html('<option value=""> กรุณาเลือก </option>');
                } else {
                    $.ajax({
                        url: "{{ route('repair.autoSearch') }}",
                        type: 'GET',
                        data: {
                            location: _location
                        },
                        success: function(data) {
                            let _class = '';
                            let _address = '';

                            for (var i = 0; i < data.length; i++) {
                                _address = (data[i].address) ? ' สถานที่ : ' + data[i].address :
                                    '';
                                $('#order_address').append('<option value="ชั้นที่ : ' + data[i]
                                    .class + _address + '">ชั้นที่ : ' + data[i].class +
                                    _address + ' </option>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log(error);
                        }
                    });
                }
            });
        });

        function otherCheck(that) {
            if (that.value == "other") {
                document.getElementById("ifYes").style.display = "block";
                $('#order_other').attr('required', 'required');
            } else {
                document.getElementById("ifYes").style.display = "none";
                $('#order_other').removeAttr('required');
            }
        }

        function otherAddress(data) {
            if (data == "other") {
                document.getElementById("sl_build").style.display = "none";
                document.getElementById("sl_other").style.display = "block";
                $('#address_other').attr('required', 'required');
                $('#order_address').removeAttr('required');
            } else {
                document.getElementById("sl_build").style.display = "block";
                document.getElementById("sl_other").style.display = "none";
                $('#address_other').removeAttr('required');
                $('#order_address').attr('required', 'required');
            }
        }
    </script>

    <script type="text/javascript">
        document.querySelector("select#order_type").addEventListener("change", () => {
            $("input[name='order_tool[]']").prop("checked", false);
            if (event.target.value == "งานเช็คระยะ") {
                display("A");
            } else if (event.target.value == "งานระบบ") {
                display("B");
            } else if (event.target.value == "งานล้อ") {
                display("C");
            } else if (event.target.value == "") {
                display("");
            }
        });
        const boxs = document.querySelectorAll("div.box");

        // Select box show/hide div
        function display(value) {
            for (const box of boxs) {
                if (box.classList.contains(value)) {
                    box.classList.remove("hide");
                } else {
                    box.classList.add("hide");
                }
            }
        }
    </script>
    <script>
        function validateAndConfirm() {
            var form = document.getElementById("soform");
            if (form.checkValidity()) {
                btnConfirm();
            } else {
                form.reportValidity(); // This line will show the browser's default validation message
            }
        }

        function btnConfirm() {
            Swal.fire({
                title: "คุณต้องการดำเนินการต่อ ใช่ไหม?",
                icon: "warning",
                showCancelButton: true,
                showLoaderOnConfirm: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ใช่",
                cancelButtonText: "ยกเลิก",
                allowOutsideClick: () => !Swal.isLoading(),
            }).then((willDelete) => {
                if (willDelete.isConfirmed) {
                    document.getElementById("soform").submit();
                }
            });
        }
    </script>
@endsection
