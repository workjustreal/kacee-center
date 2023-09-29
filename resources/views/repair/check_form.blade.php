@extends('layouts.master-nopreloader-layout', ['page_title' => $repairs->order_id])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->
    <style>
        .text-decoration-dotted {
            text-decoration-line: underline;
            text-decoration-style: dotted;
            text-decoration-thickness: 0px;
        }

        .rtv {
            position: relative;
        }

        .abs {
            position: absolute;
        }

        .full-underline {
            width: 100%;
        }

        .full-underline span.full-dotted {
            display: block;
            width: 100%;
            height: 16px;
            border-bottom: 0.8px dotted #6c757d;
        }

        .dotshed {
            border-bottom: 1px dotted;
        }
    </style>
@endsection
@section('content')
    @inject('thaiDateHelper', '\App\Services\ThaiDateHelperService')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Repair</a></li>
                            <li class="breadcrumb-item active">รหัสใบแจ้งซ่อม {{ $repairs->order_id }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">รหัสใบแจ้งซ่อม{{ $order_dept->dept_name }} รหัส: {{ $repairs->order_id }}</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- start status form -->
        @include('repair.status-head')
        <!-- end status form -->

        <!-- start button form -->
        <div class="col-12">
            <div class="row">
                <div class="col-7">
                    <div class="card">
                        <div class="card-body">

                            <div class="card border">
                                <div class="card-header">ข้อมูลผู้แจ้ง</div>
                                <div class="card-body">
                                    <div class="row pb-4">
                                        <div class="col-sm-12">
                                            <div class="rtv">
                                                <div class="full-underline abs">
                                                    <span class="full-dotted mb-2">
                                                        @php
                                                            $user = Auth::User()->findEmployee($repairs->user_id);
                                                        @endphp
                                                        @if ($user->title == 'นาย')
                                                            <span class="bg-white">ชื่อ (นาย,<span
                                                                    class="text-decoration-line-through">นาง</span>,<span
                                                                    class="text-decoration-line-through">นางสาว</span>)</span>
                                                        @elseif ($user->title == 'นาง')
                                                            <span class="bg-white">ชื่อ (<span
                                                                    class="text-decoration-line-through">นาย</span>,นาง,<span
                                                                    class="text-decoration-line-through">นางสาว</span>)</span>
                                                        @elseif ($user->title == 'นางสาว')
                                                            <span class="bg-white">ชื่อ (<span
                                                                    class="text-decoration-line-through">นาย</span>,<span
                                                                    class="text-decoration-line-through">นาง</span>,นางสาว)</span>
                                                        @else
                                                            <span class="bg-white">ชื่อ (นาย,นาง,นางสาว)</span>
                                                        @endif
                                                        <span
                                                            class="text-dark px-5">{{ $user->name . ' ' . $user->surname }}</span>

                                                        <span class="bg-white">ชื่อเล่น </span>
                                                        <span
                                                            class="text-dark @if ($user->nickname) px-3 @else px-5 @endif">{{ $user->nickname }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 pb-4">
                                        <div class="rtv">
                                            <div class="full-underline abs">
                                                <span class="full-dotted mb-2">
                                                    <span class="bg-white">ฝ่าย / แผนก</span>
                                                    <span
                                                        class="text-dark @if ($dept_parent) px-5 @else px-5 @endif">{{ $dept_parent->dept_name }}</span>

                                                    <span class="bg-white">เบอร์โทรภายใน </span>
                                                    <span
                                                        class="text-dark @if ($user->tel) px-3 @else px-5 @endif">{{ $user->tel }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($repairs->order_dept == 'A03050200')
                                        <div class="row">
                                            <div class="full-underline abs">
                                                <span class="full-dotted">
                                                    <span class="bg-white">เลขทะเบียนรถ : </span>
                                                    <span
                                                        class="text-dark @if ($repairs->car_id) px-5 @else px-5 @endif">{{ $repairs->car_id }}</span>

                                                    <span class="bg-white">เลขไมค์ : </span>
                                                    <span
                                                        class="text-dark @if ($repairs->car_mile) px-5 @else px-5 @endif">{{ $repairs->car_mile }}</span>

                                                    <span class="bg-white">ประเภทงานซ่อม : </span>
                                                    <span
                                                        class="text-dark @if ($repairs->order_type) px-5 @else px-5 @endif">{{ $repairs->order_type }}</span>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-sm-2">แจ้งปัญหา : </div>
                                            <div class="col-sm-10">
                                                <div class="row">
                                                    @php
                                                        $chk = false;
                                                        $type_id = '';
                                                        switch ($repairs->order_type) {
                                                            case 'งานเช็คระยะ':
                                                                $type_id = 1;
                                                                break;
                                                            case 'งานระบบ':
                                                                $type_id = 2;
                                                                break;
                                                            case 'งานล้อ':
                                                                $type_id = 3;
                                                                break;
                                                        }
                                                        
                                                    @endphp
                                                    @foreach ($repair_type as $list)
                                                        @if ($repairs->order_dept == $list->dept_id && $type_id == $list->type_id)
                                                            <div class="col-sm-4 mb-1">
                                                                @if (str_contains($repairs->order_tool, $list->name))
                                                                    @php $chk = true; @endphp
                                                                    <img src="{{ asset('assets/images/checkbox-mark.png') }}"
                                                                        height="18">
                                                                @else
                                                                    <img src="{{ asset('assets/images/checkbox.png') }}"
                                                                        height="18">
                                                                @endif
                                                                <span>{{ $list->name }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                    <div class="col-sm-4 mb-1">
                                                        @if ($chk == false)
                                                            <img src="{{ asset('assets/images/checkbox-mark.png') }}"
                                                                height="18">
                                                            <span>อื่นๆ <span
                                                                    class="dotshed px-2">{{ $repairs->order_type }}</span></span>
                                                        @else
                                                            <img src="{{ asset('assets/images/checkbox.png') }}"
                                                                height="18">
                                                            <span>อื่นๆ <span class="dotshed px-2"></span></span>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-sm-12 row">
                                            <div class="col-sm-2">แจ้งปัญหา : </div>
                                            <div class="col-sm-10">
                                                <div class="row">
                                                    @php $chk = false; @endphp
                                                    @foreach ($repair_type as $list)
                                                        @if ($repairs->order_dept == $list->dept_id)
                                                            <div class="col-sm-3 mb-1">
                                                                @if ($repairs->order_type == $list->name)
                                                                    @php $chk = true; @endphp
                                                                    <img src="{{ asset('assets/images/checkbox-mark.png') }}"
                                                                        height="18">
                                                                @else
                                                                    <img src="{{ asset('assets/images/checkbox.png') }}"
                                                                        height="18">
                                                                @endif
                                                                <span>{{ $list->name }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach

                                                    <div class="col-sm-3 mb-1">
                                                        @if ($chk == false)
                                                            <img src="{{ asset('assets/images/checkbox-mark.png') }}"
                                                                height="18">
                                                            <span>อื่นๆ <span
                                                                    class="dotshed px-3">{{ $repairs->order_type }}</span></span>
                                                        @else
                                                            <img src="{{ asset('assets/images/checkbox.png') }}"
                                                                height="18">
                                                            <span>อื่นๆ <span class="dotshed px-3"></span></span>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-12 mt-3 mb-5">
                                            <div class="rtv">
                                                <div class="full-underline abs">
                                                    @if ($repairs->order_dept != 'A03050200')
                                                        <span class="full-dotted mb-2">
                                                            <span class="bg-white">อุปกรณ์ที่แจ้งซ่อม : </span>
                                                            <span
                                                                class="text-dark @if ($repairs->order_tool) px-5 @else px-5 @endif">{{ $repairs->order_tool }}</span>
                                                        </span>
                                                    @endif

                                                    <span class="full-dotted mb-2">
                                                        <span class="bg-white">สถานที่ซ่อม : </span>
                                                        <span
                                                            class="text-dark @if ($repairs->order_address) px-5 @else px-5 @endif">{{ $repairs->order_address }}</span>
                                                    </span>

                                                    <span class="full-dotted mb-1">
                                                        <span class="bg-white">รายละเอียดปัญหา : </span>
                                                        <span
                                                            class="text-dark @if ($repairs->order_detail) px-2 @else px-5 @endif">{{ $repairs->order_detail }}</span>
                                                    </span>
                                                    <span class="full-dotted mb-2"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @php 
                                        $images = json_decode($repairs->order_image, true); 
                                        if ($repairs->order_dept != 'A03050200') {
                                            $css = 'mt-3';
                                        } else {
                                            $css = '';
                                        }
                                        
                                    @endphp
                                    @if ($images)
                                        <div class="row {{$css}}">
                                            <div class="col-sm-2 mt-2">รูปภาพประกอบ : </div>
                                            <div class="col-sm-10 mt-2">
                                                @foreach ($images as $key => $image)
                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#imageModal" id="image{{ $key + 1 }}">
                                                        <img src="{{ url('assets/images/repair/' . $image) }}"
                                                            onerror="this.onerror=null;this.src='{{ url('assets/images/NoImage.jpg') }}'"
                                                            alt="Image {{ $key + 1 }}"
                                                            style="width: 100px;padding: 0 10px;margin-bottom: 12px;">
                                                    </a>

                                                    <div class="modal fade" id="imageModal" tabindex="-1"
                                                        role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <!-- START carousel-->
                                                                    <div id="carouselExampleControls"
                                                                        class="carousel slide"
                                                                        data-bs-ride="carousel">
                                                                        <div class="carousel-inner" role="listbox">
                                                                            @foreach ($images as $_key => $_image)
                                                                                <div
                                                                                    class="carousel-item @if ($key == $_key) active @endif">
                                                                                    <img class="d-block img-fluid w-100"
                                                                                        src="{{ url('assets/images/repair/' . $_image) }}"
                                                                                        onerror="this.onerror=null;this.src='{{ url('assets/images/NoImage.jpg') }}'"
                                                                                        alt="{{ $_image }}">
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <a class="carousel-control-prev"
                                                                            href="#carouselExampleControls"
                                                                            role="button" data-bs-slide="prev">
                                                                            <span class="carousel-control-prev-icon"
                                                                                aria-hidden="true"></span>
                                                                            <span
                                                                                class="visually-hidden">Previous</span>
                                                                        </a>
                                                                        <a class="carousel-control-next"
                                                                            href="#carouselExampleControls"
                                                                            role="button" data-bs-slide="next">
                                                                            <span class="carousel-control-next-icon"
                                                                                aria-hidden="true"></span>
                                                                            <span class="visually-hidden">Next</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="row col-12 {{$css}} mb-5">
                                            <div class="rtv">
                                                <div class="full-underline abs">
                                                    <span class="full-dotted mt-2">
                                                        <span class="bg-white">รูปภาพประกอบ : </span>
                                                        <span class="text-dark px-5">-</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row mt-4">
                                        <div class="col-sm-6 text-center">
                                            <div class="rtv">
                                                <div class="full-underline abs px-3">
                                                    <span class="full-dotted">
                                                        @if ($repairs->user_id)
                                                            {{ $user->name . ' ' . $user->surname }} /
                                                            {{ $thaiDateHelper->shortDateFormat($repairs->order_date) }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <br>
                                            <p class="fw-bold">ผู้แจ้ง / ว.ด.ป.</p>
                                        </div>
                                        <div class="col-sm-6 text-center">
                                            <div class="rtv">
                                                <div class="full-underline abs px-3">
                                                    <span class="full-dotted">
                                                        @php
                                                            $approve = Auth::User()->findEmployee($repairs->approve_name);
                                                        @endphp
                                                        @if ($repairs->approve_name)
                                                            {{ $approve->name . ' ' . $approve->surname }} /
                                                            {{ $thaiDateHelper->shortDateFormat($repairs->approve_date) }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <br>
                                            <p class="fw-bold">ผู้อนุมัติ / ว.ด.ป.</p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            @if ($repairs->status == 'รอตรวจสอบ')
                                <div class="card border">
                                    <div class="card-header">ส่วนที่ 5 ผู้ตรวจสอบ</div>
                                    <div class="card-body pt-2">
                                        <form action="{{ route('repair.check_update') }}" class="wow fadeInLeft"
                                            method="post">
                                            @csrf
                                            <div class="row">
                                                <div class="col-12 pt-2">
                                                    <div class="col-md-9 col-lg-9">
                                                        <div class="form-group">
                                                            <label class="control-label">แจ้งสถานะของงาน</label>
                                                            <select name="status" onchange="otherCheck(this);"
                                                                class="form-control form-control-md form-control-required"
                                                                required>
                                                                <option value="ผ่านการตรวจสอบ">ผ่านการตรวจสอบ</option>
                                                                <option value="ดำเนินการ">กลับไปดำเนินการต่อ</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-9 col-lg-9 pt-2" id="prices">
                                                        <div class="fom-group">
                                                            <label class="control-label">ค่าใช้จ่ายรวม :</label>
                                                            <input type="number"
                                                                class="form-control form-control-md form-control-required"
                                                                name="price"
                                                                id="price"
                                                                placeholder="ไม่มีกรอก 0 , มีให้กรอกราคารวมอุปกรณ์นั้นๆ"
                                                                required>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 col-lg-12 pt-2">
                                                        <div class="fom-group">
                                                            <label class="control-label">หมายเหตุ :</label>
                                                            <textarea class="form-control form-control-md form-control-required" id="approve_detail" name="approve_detail"
                                                                placeholder="กรุณากรอกรายละเอียด" required="" rows="3" value=""></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12 col-md-12 col-sm-12 pt-3">
                                                        <input type="hidden" name="id"
                                                            value="{{ $repairs->id }}">
                                                        <input type="hidden" name="sdate"
                                                            value="@php echo date("Y-m-d H:i:s"); @endphp">
                                                        {{-- <input type="hidden" name="status" value="ผ่านการตรวจสอบ"> --}}
                                                        <a class="btn btn-white" href="{{ url('/repair/action') }}"><i
                                                                class="fe-arrow-left"></i> ย้อนกลับ</a>
                                                        <button type="submit" class="btn btn-primary mx-2"
                                                            id="btn-submit"> <i class="fe-save"></i> รับทราบ</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            @if ($repairs->status == 'ผ่านการตรวจสอบ')
                                <div class="card border">
                                    <div class="card-header">ส่วนที่ 6 ผู้ตรวจรับงาน</div>
                                    <div class="card-body pt-2">
                                        <form action="{{ route('repair.check_update') }}" class="wow fadeInLeft"
                                            method="post">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-9 col-lg-9 pt-2">
                                                    <div class="form-group">
                                                        <label class="control-label">ความคิดเห็น/ข้อเสนอแนะ :</label>
                                                        <textarea class="form-control form-control-md form-control-required" id="approve_detail" name="approve_detail"
                                                            placeholder="กรุณากรอกรายละเอียด" rows="3" value=""></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12 col-md-12 col-sm-12 pt-3">
                                                    <input type="hidden" name="id" value="{{ $repairs->id }}" />
                                                    <input type="hidden" name="sdate"
                                                        value="@php echo date("Y-m-d H:i:s"); @endphp" />
                                                    <input type="hidden" name="status" value="เสร็จสิ้น" />

                                                    <a class="btn btn-white" href="{{ url('/repair/repair') }}"><i
                                                            class="fe-arrow-left"></i> ย้อนกลับ</a>
                                                    <button type="submit" class="btn btn-primary mx-2" id="btn-submit">
                                                        <i class="fe-save"></i> รับทราบ</button>

                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
                {{-- </div>  --}}

                <div class="col-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="card border">
                                <div class="card-header">ข้อมูลผู้ปฏิบัติงาน</div>
                                <div class="card-body">
                                    <div class="row ">
                                        <div class="col-12 mb-4">
                                            <div class="rtv">
                                                <div class="full-underline abs">
                                                    <span class="full-dotted mb-2">
                                                        <span class="bg-white">เริ่มปฏิบัติงาน : </span>
                                                        <span class="text-dark px-3">
                                                            {{ $thaiDateHelper->shortDateFormat($repairs->start_date) }}
                                                        </span>
                                                    </span>

                                                    <span class="full-dotted mb-1">
                                                        <span class="bg-white">ผู้ปฏิบัติงาน : </span>
                                                        <span class="text-dark px-2">
                                                            @if ($tech_name)
                                                                @foreach ($tech_name as $row)
                                                                    @php
                                                                        $worker = Auth::User()->findEmployee($row['emp_id']);
                                                                        if ($worker->nickname) {
                                                                            $work = ' ( ' . $worker->nickname . ' )';
                                                                        } else {
                                                                            $work = '';
                                                                        }
                                                                    @endphp
                                                                    <span class="px-1">
                                                                        {{ $worker->title . $worker->name . ' ' . $worker->surname . $work }}
                                                                    </span>
                                                                @endforeach
                                                            @endif
                                                        </span>
                                                    </span>

                                                    <span class="full-dotted mb-2"></span>

                                                    <span class="full-dotted mb-2">
                                                        <span class="bg-white">หมายเหตุของผู้รับแจ้ง : </span>
                                                        <span class="text-dark px-3">
                                                            {{ $repairs->manager_detail ? $repairs->manager_detail : '-' }}
                                                        </span>
                                                    </span>

                                                    <span class="full-dotted mb-2">
                                                        <span class="bg-white">ค่าใช้จ่ายรวมในงาน : </span>
                                                        <span class="text-dark px-3">
                                                            {{ $repairs->price ? $repairs->price : '-' }}
                                                            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp บาท</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-sm-6 text-center mt-5">
                                            <div class="rtv">
                                                <div class="full-underline abs px-2">
                                                    <span class="full-dotted">
                                                        @php
                                                            $manager = Auth::User()->findEmployee($repairs->manager_id);
                                                        @endphp
                                                        @if ($manager)
                                                            {{ $manager->name . ' ' . $manager->surname }} /
                                                            {{ $thaiDateHelper->shortDateFormat($repairs->manager_date) }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <br>
                                            <p class="fw-bold">ผู้รับแจ้ง / ว.ด.ป.</p>
                                        </div>

                                        <div class="col-sm-6 text-center mt-5">
                                            <div class="rtv">
                                                <div class="full-underline abs px-2">
                                                    <span class="full-dotted">
                                                        @if ($ap_detail)
                                                            @foreach ($ap_detail as $rows)
                                                                @php $check = Auth::User()->findEmployee($rows['emp_id']); @endphp
                                                                {{ $check->name . ' ' . $check->surname }} /
                                                                {{ $thaiDateHelper->shortDateFormat($rows['date']) }}
                                                            @endforeach
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <br>
                                            <p class="fw-bold">ผู้ตรวจสอบงาน / ว.ด.ป.</p>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            @if (count($withdraw) > 0)
                                <div class="card border">
                                    <div class="card-header">รายการอุปกรณ์</div>
                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                                <th>ชื่ออุปกรณ์</th>
                                                <th class="text-center">จำนวนชิ้น</th>
                                                <th>เบิกอุปกรณ์</th>
                                                <th>ผู้เบิก</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($withdraw as $wd)
                                                    @php
                                                        $_user = Auth::User()->findEmployee($wd->emp_id );
                                                        $inventory = ($wd->status_inventory == 0) ? 'เบิกจากคลัง': 'สั่งซื้อใหม่' ;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $wd->products_name }}</td>
                                                        <td class="text-center">{{ $wd->qty }}</td>
                                                        <td>{{ $inventory }}</td>
                                                        <td>{{ $_user->name }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="border-0 border-bottom text-center">Action</div>
                            <div class="border border-light p-2 mb-3">
                                @if ($tech_detail)
                                    @foreach ($tech_detail as $list)
                                        @php
                                            if ($list['continue_date']) {
                                                $con_date = 'ดำเนินงานต่อ / ' . \Carbon\Carbon::parse($list['continue_date'])->thaidate('j M Y');
                                            } elseif ($list['end_date']) {
                                                $con_date = 'ปิดงาน / ' . \Carbon\Carbon::parse($list['end_date'])->thaidate('j M Y');
                                            }
                                        @endphp
                                        <div class="post-user-comment-box bg-white rounded my-1">
                                            <div class="d-flex align-items-start">
                                                <img class="me-2 avatar-sm rounded-circle"
                                                    src="{{ url('assets/images/users/thumbnail/user-1.jpg') }}"
                                                    onerror="this.onerror=null;this.src='{{ url('assets/images/users/thumbnail/user-1.jpg') }}'"
                                                    alt="image">
                                                <div class="w-100">
                                                    <h5 class="mt-0">
                                                        {{ $list['name'] }}
                                                        <small class="text-muted">
                                                            {{ \Carbon\Carbon::parse($list['start_date'])->locale('th_TH')->diffForHumans() }}
                                                        </small>
                                                    </h5>
                                                    <i class="mdi mdi-share-outline me-1"></i>
                                                    {{ $list['s_job'] . ' ' . $list['s_tool'] . ' ' . $list['detail'] }}
                                                    <small class="text-muted ms-2">{{ $con_date }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                @if ($ap_detail)
                                    @foreach ($ap_detail as $rows)
                                        @php
                                            if ($rows['date']) {
                                                $check_date = 'ตรวจสอบ / ' . \Carbon\Carbon::parse($rows['date'])->thaidate('j M Y');
                                            }
                                        @endphp
                                        <div class="post-user-comment-box bg-white rounded my-1">
                                            <div class="d-flex align-items-start">
                                                <img class="me-2 avatar-sm rounded-circle"
                                                    src="{{ url('assets/images/users/thumbnail/user-1.jpg') }}"
                                                    onerror="this.onerror=null;this.src='{{ url('assets/images/users/thumbnail/user-1.jpg') }}'"
                                                    alt="image" />
                                                <div class="w-100">
                                                    <h5 class="mt-0">
                                                        {{ $rows['name'] }}
                                                        <small class="text-muted">
                                                            {{ \Carbon\Carbon::parse($rows['date'])->locale('th_TH')->diffForHumans() }}
                                                        </small>
                                                    </h5>
                                                    <i class="mdi mdi-share-outline me-1"></i>
                                                    {{ $rows['detail'] }}
                                                    <small class="text-muted ms-2">{{ $check_date }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                @if ($user_detail)
                                    @foreach ($user_detail as $rowu)
                                        @php
                                            if ($rowu['date']) {
                                                $usercheck_date = 'ผู้แจ้ง / ' . \Carbon\Carbon::parse($rowu['date'])->thaidate('j M Y');
                                            }
                                        @endphp
                                        <div class="post-user-comment-box bg-white rounded my-1">
                                            <div class="d-flex align-items-start">
                                                <img class="me-2 avatar-sm rounded-circle"
                                                    src="{{ url('assets/images/users/thumbnail/user-1.jpg') }}"
                                                    onerror="this.onerror=null;this.src='{{ url('assets/images/users/thumbnail/user-1.jpg') }}'"
                                                    alt="image" />
                                                <div class="w-100">
                                                    <h5 class="mt-0">
                                                        {{ $rowu['name'] }}
                                                        <small class="text-muted">
                                                            {{ \Carbon\Carbon::parse($rowu['date'])->locale('th_TH')->diffForHumans() }}
                                                        </small>
                                                    </h5>
                                                    <i class="mdi mdi-share-outline me-1"></i>
                                                    {{ $rowu['detail'] }}
                                                    <small class="text-muted ms-2">{{ $usercheck_date }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- end button form -->
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('a[data-bs-toggle="modal"]').click(function() {
                var imageId = $(this).attr('id');
                if (typeof imageId !== 'undefined') {
                    var index = imageId.slice(-1);
                    $('#imageModal .carousel-inner div').removeClass('active');
                    $('#imageModal .carousel-indicators div:nth-child(' + index + ')').addClass('active');
                    $('#imageModal .carousel-item').removeClass('active');
                    $('#imageModal .carousel-item:nth-child(' + index + ')').addClass('active');
                }
            });
        });

        function otherCheck(that) {
            const prices = document.getElementById("prices");
            const price = document.getElementById("price");
            console.log(that.value);
            if (that.value == "ผ่านการตรวจสอบ") {
                document.getElementById("prices").style.display = "block";
                price.setAttribute("required", "");
            } else if (that.value == "ดำเนินการ") {
                document.getElementById("prices").style.display = "none";
                price.removeAttribute("required");
            }
        }
    </script>
@endsection
