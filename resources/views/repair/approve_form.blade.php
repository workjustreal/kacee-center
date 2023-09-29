@extends('layouts.master-nopreloader-layout', ['page_title' => 'อนุมัติ&แก้ไขรายการ'])
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

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9 col-lg-7">
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
                                            <div class="row {{ $css }}">
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
                                            <div class="row col-12 {{ $css }} mb-5">
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
                            </div>
                        </div>

                        {{-- Start Approve --}}
                        @if ($repairs->status == 'รออนุมัติ')
                            <div class="card border">
                                <div class="card-header">ส่วนที่ 2 ผู้อนุมัติ</div>
                                <div class="card-body pt-2">
                                    <form action="{{ route('repair.approve_update') }}" class="wow fadeInLeft"
                                        method="post">
                                        @csrf
                                        <div class="col-lg-12 col-md-12 col-sm-12 pt-3">
                                            <input type="hidden" name="id" value="{{ $repairs->id }}">
                                            <input type="hidden" name="approve_date"
                                                value="{{ \Carbon\Carbon::now() }}">

                                            <a class="btn btn-white" href="{{ url('/repair/approve') }}"><i
                                                    class="fe-arrow-left"></i> ย้อนกลับ</a>
                                            <button type="submit" class="btn btn-primary mx-2" id="btn-submit"> <i
                                                    class="fe-save"></i> รับทราบ</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                        {{-- End Approve --}}

                        {{-- Start  --}}
                        @if ($repairs->status != 'รออนุมัติ')
                            <div class="col-md-12 col-lg-12">
                                <div class="card border">
                                    <div class="card-header">ส่วนที่ 3 ผู้รับแจ้ง</div>
                                    <div class="card-body pt-2">
                                        <form action="{{ route('repair.work_update') }}" class="wow fadeInLeft"
                                            method="post">
                                            @csrf
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="col-md-6 col-lg-5 pt-2">
                                                        <div class="form-group">
                                                            <label class="control-label">วันที่เริ่มดำเนินการซ่อม</label>
                                                            <input type="date"
                                                                class="form-control form-control-md form-control-required"
                                                                id="start_date" name="start_date"
                                                                value="@php echo date("Y-m-d"); @endphp" required="">
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($repairs->order_dept == 'A03050200')
                                                    <div class="col-12">
                                                        <div class="col-md-6 col-lg-5 pt-2">
                                                            <div class="form-group ">
                                                                <label class="control-label">สถานที่ซ่อม</label>
                                                                <select
                                                                    class="form-select form-select-md form-control-required"
                                                                    aria-label=".form-select-md" id="order_address"
                                                                    name="order_address" onchange="otherCheck(this);"
                                                                    required>
                                                                    <option value="" selected="selected">-</option>
                                                                    <option value="อี.แอนด์ วี.">อี.แอนด์ วี.</option>
                                                                    <option value="ดี ธวัชชัย">ดี ธวัชชัย</option>
                                                                    <option value="NT การาจ">NT การาจ</option>
                                                                    <option value="ช.การยาง">ช.การยาง</option>
                                                                    <option value="อู่ธนกร">อู่ธนกร</option>
                                                                    <option value="ศูนย์ TOYOTA">ศูนย์ TOYOTA</option>
                                                                    <option value="ศูนย์ ISUZU">ศูนย์ ISUZU</option>
                                                                    <option value="other">อื่นๆ...</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="col-md-6 col-lg-5 pt-2" id="ifYes"
                                                            style="display: none;">
                                                            <div class="form-group ">
                                                                <label class="control-label">อื่นๆ โปรดระบุ</label>
                                                                <input type="text" class="form-control form-control-md"
                                                                    placeholder="กรุณากรอกสถานที่..." name="order_other"
                                                                    value="" autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="col-12">
                                                    <div class="col-md-6 col-lg-5 pt-2">
                                                        <div class="form-group">
                                                            <label class="control-label">ผู้ปฏิบัติงาน</label>
                                                            <select name="technician_name[]" multiple="multiple"
                                                                required=""
                                                                class="form-control form-control-md form-control-required js-example-basic-multiple">
                                                                @foreach ($empname_parent as $row)
                                                                    @php
                                                                        $emp_ids = Auth::User()->findEmployee($row->emp_id);
                                                                        if ($emp_ids->nickname) {
                                                                            $work = ' ( ' . $emp_ids->nickname . ' )';
                                                                        } else {
                                                                            $work = '';
                                                                        }
                                                                        $worker = 'รหัส : ' . $emp_ids->emp_id . ' , ชื่อ ' . $emp_ids->title . $emp_ids->name . ' ' . $emp_ids->surname . $work;
                                                                    @endphp
                                                                    <option
                                                                        value="{{ $row->emp_id . ',' . $row->name . ' ' . $row->surname }}">
                                                                        {{ $worker }}
                                                                    </option>
                                                                @endforeach
                                                                @if ($tech_name)
                                                                    @foreach ($tech_name as $row)
                                                                        <h5 class="mb-2">ผู้ปฏิบัติงานคนที่
                                                                            {{ $loop->index + 1 }} :
                                                                            <span
                                                                                class="text-primary me-2">{{ $row['name'] . ' ' . $row['surname'] }}</span>
                                                                        </h5>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="col-md-6 col-lg-5 pt-2">
                                                        <div class="fom-group ">
                                                            <label class="control-label">หมายเหตุ :</label>
                                                            <textarea class="form-control form-control-md form-control-required" id="manager_detail" name="manager_detail"
                                                                placeholder="หมายเหตุ" required="" rows="4" value=""></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12 col-md-12 col-sm-12 pt-3">
                                                    <input type="hidden" name="id" value="{{ $repairs->id }}">
                                                    <input type="hidden" name="approve_date"
                                                        value="{{ \Carbon\Carbon::now() }}">
                                                    <a class="btn btn-white" href="{{ url('/repair/action') }}"><i
                                                            class="fe-arrow-left"></i> ย้อนกลับ</a>
                                                    <button type="submit" class="btn btn-primary mx-2" id="btn-submit">
                                                        <i class="fe-save"></i> บันทึก</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{-- End --}}
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
            $('.js-example-basic-multiple').select2();

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
            if (that.value == "other") {
                document.getElementById("ifYes").style.display = "block";
            } else {
                document.getElementById("ifYes").style.display = "none";
            }
        }
    </script>
@endsection
