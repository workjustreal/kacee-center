@extends('layouts.master-nopreloader-layout', ['page_title' => 'ใบแจ้งซ่อม'])

@section('css')
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
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Kacee</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">repairs</a></li>
                            <li class="breadcrumb-item active">ใบแจ้งซ่อม</li>
                        </ol>
                    </div>
                    <h4 class="page-title">ใบแจ้งซ่อม{{ $order_dept->dept_name }} รหัส : {{ $repairs->order_id }}</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- start layout -->
        @inject('thaiDateHelper', '\App\Services\ThaiDateHelperService')
        <div class="row">
            <!-- start status layout -->
            <div class="col-lg-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="header-title mb-3">สถานะใบแแจ้งซ่อม</h4>
                        <div class="track-order-list">
                            <ul class="list-unstyled">
                                @php
                                    // รออนุมัติ
                                    $pending = '';
                                    $pendingActive = '';
                                    // หัวหน้าอนุมัติ
                                    $approved1 = '';
                                    $approved1Active = '';
                                    // ดำเนินการ
                                    $approved2 = '';
                                    $approved2Active = '';
                                    $progress = '';
                                    $progressActive = '';
                                    // รอตรวจสอบ
                                    $approved2check = '';
                                    $approved2checkActive = '';
                                    // ผ่านการตรวจสอบ
                                    $user_approved = '';
                                    $user_approvedActive = '';
                                    // เสร็จสิ้น
                                    $completed = '';
                                    $completedActive = '';
                                    // ยกเลิกโดยผู้แจ้ง
                                    $cancel1 = '';
                                    $cancel1Active = '';
                                    // ยกเลิกโดยผู้อนุมัติ
                                    $cancel2 = '';
                                    $cancel2Active = '';
                                    // ยกเลิกโดยผู้รับงาน
                                    $cancel3 = '';
                                    $cancel3Active = '';
                                    
                                    if ($repairs->status == 'รออนุมัติ') {
                                        $pending = '';
                                        $pendingActive = '<span class="active-dot dot"></span>';
                                    } elseif ($repairs->status == 'หัวหน้าอนุมัติ') {
                                        $pending = 'completed';
                                        $approved1Active = '<span class="active-dot dot"></span>';
                                    } elseif ($repairs->status == 'ดำเนินการ') {
                                        $pending = 'completed';
                                        $approved1 = 'completed';
                                        $approved2 = 'completed';
                                        $progressActive = '<span class="active-dot dot"></span>';
                                    } elseif ($repairs->status == 'รอตรวจสอบ') {
                                        $pending = 'completed';
                                        $approved1 = 'completed';
                                        $approved2 = 'completed';
                                        $progress = 'completed';
                                        $approved2checkActive = '<span class="active-dot dot"></span>';
                                    } elseif ($repairs->status == 'ผ่านการตรวจสอบ') {
                                        $pending = 'completed';
                                        $approved1 = 'completed';
                                        $approved2 = 'completed';
                                        $progress = 'completed';
                                        $approved2check = 'completed';
                                        $user_approvedActive = '<span class="active-dot dot"></span>';
                                    } elseif ($repairs->status == 'เสร็จสิ้น') {
                                        $pending = 'completed';
                                        $approved1 = 'completed';
                                        $approved2 = 'completed';
                                        $progress = 'completed';
                                        $approved2check = 'completed';
                                        $user_approved = 'completed';
                                        $completedActive = '<span class="active-dot dot"></span>';
                                    } elseif ($repairs->status == 'ยกเลิกโดยผู้แจ้ง') {
                                        $pending = 'completed';
                                        $cancel1Active = '<span class="active-dot dot"></span>';
                                    } elseif ($repairs->status == 'ยกเลิกโดยหัวหน้า') {
                                        $pending = 'completed';
                                        $approved1 = 'completed';
                                        $cancel2Active = '<span class="active-dot dot"></span>';
                                    } elseif ($repairs->status == 'ยกเลิกโดยผู้รับงาน') {
                                        $pending = 'completed';
                                        $approved1 = 'completed';
                                        $cancel3Active = '<span class="active-dot dot"></span>';
                                    }
                                @endphp

                                @if ($repairs->status == 'ยกเลิกโดยผู้แจ้ง')
                                    <li class="{{ $pending }}">
                                        {!! $pendingActive !!}
                                        <h5 class="mt-0 mb-1">รออนุมัติ</h5>
                                        <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($repairs->created_at) }}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($repairs->created_at)->format('H:i') . ' น.' }}</small>
                                        </p>
                                    </li>
                                    <li class="{{ $cancel1 }}">
                                        {!! $cancel1Active !!}
                                        <h5 class="mt-0 mb-1">ยกเลิกโดยผู้แจ้ง</h5>
                                        @if ($repairs->approve_date == '')
                                            <p class="text-muted">
                                                {{ $thaiDateHelper->shortDateFormat($repairs->updated_at) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($repairs->updated_at)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                @elseif ($repairs->status == 'ยกเลิกโดยหัวหน้า')
                                    <li class="{{ $pending }}">
                                        {!! $pendingActive !!}
                                        <h5 class="mt-0 mb-1">รออนุมัติ</h5>
                                        <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($repairs->created_at) }}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($repairs->created_at)->format('H:i') . ' น.' }}</small>
                                        </p>
                                    </li>
                                    <li class="{{ $cancel2 }}">
                                        {!! $cancel2Active !!}
                                        <h5 class="mt-0 mb-1">ยกเลิกโดยผู้อนุมัติ</h5>
                                        @if ($repairs->manager_date == '')
                                            <p class="text-muted">
                                                {{ $thaiDateHelper->shortDateFormat($repairs->updated_at) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($repairs->updated_at)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                @elseif ($repairs->status == 'ยกเลิกโดยผู้รับงาน')
                                    <li class="{{ $pending }}">
                                        {!! $pendingActive !!}
                                        <h5 class="mt-0 mb-1">รออนุมัติ</h5>
                                        <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($repairs->created_at) }}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($repairs->created_at)->format('H:i') . ' น.' }}</small>
                                        </p>
                                    </li>
                                    <li class="{{ $approved1 }}">
                                        {!! $approved1Active !!}
                                        <h5 class="mt-0 mb-1">ผู้อนุมัติ</h5>
                                        @if ($repairs->approve_date != '')
                                            <p class="text-muted">
                                                {{ $thaiDateHelper->shortDateFormat($repairs->approve_date) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($repairs->approve_date)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                    <li class="{{ $cancel3 }}">
                                        {!! $cancel3Active !!}
                                        <h5 class="mt-0 mb-1">ยกเลิกโดยผู้รับงาน</h5>
                                        @if ($repairs->start_date == '')
                                            <p class="text-muted">
                                                {{ $thaiDateHelper->shortDateFormat($repairs->manager_date) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($repairs->manager_date)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                @else
                                    <li class="{{ $pending }}">
                                        {!! $pendingActive !!}
                                        <h5 class="mt-0 mb-1">รออนุมัติ</h5>
                                        <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($repairs->created_at) }}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($repairs->created_at)->format('H:i') . ' น.' }}</small>
                                        </p>
                                    </li>
                                    <li class="{{ $approved1 }}">
                                        {!! $approved1Active !!}
                                        <h5 class="mt-0 mb-1">ผู้อนุมัติ</h5>
                                        @if ($repairs->approve_date != '')
                                            <p class="text-muted">
                                                {{ $thaiDateHelper->shortDateFormat($repairs->approve_date) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($repairs->approve_date)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                    <li class="{{ $approved2 }}">
                                        {!! $approved2Active !!}
                                        <h5 class="mt-0 mb-1">ผู้รับแจ้ง</h5>
                                        @if ($repairs->manager_date != '')
                                            <p class="text-muted">
                                                {{ $thaiDateHelper->shortDateFormat($repairs->manager_date) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($repairs->manager_date)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                    <li class="{{ $progress }}">
                                        {!! $progressActive !!}
                                        <h5 class="mt-0 mb-1">กำลังดำเนินการ</h5>
                                        @if ($tech_detail)
                                            @foreach ($tech_detail as $dt)
                                                <p class="text-muted">
                                                    {{ $thaiDateHelper->shortDateFormat($dt['start_date']) }}
                                                    <small
                                                        class="text-muted">{{ \Carbon\Carbon::parse($dt['start_date'])->format('H:i') . ' น.' }}</small>
                                                </p>
                                            @endforeach
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                    <li class="{{ $approved2check }}">
                                        {!! $approved2checkActive !!}
                                        <h5 class="mt-0 mb-1">ผู้ตรวจสอบ</h5>
                                        @if ($ap_detail)
                                            @foreach ($ap_detail as $ap)
                                                <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($ap['date']) }}
                                                    <small
                                                        class="text-muted">{{ \Carbon\Carbon::parse($ap['date'])->format('H:i') . ' น.' }}</small>
                                                </p>
                                            @endforeach
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                    <li class="{{ $user_approved }}">
                                        {!! $user_approvedActive !!}
                                        <h5 class="mt-0 mb-1">ผู้ตรวจรับงาน</h5>
                                        @if ($user_detail)
                                            @foreach ($user_detail as $ud)
                                                <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($ud['date']) }}
                                                    <small
                                                        class="text-muted">{{ \Carbon\Carbon::parse($ud['date'])->format('H:i') . ' น.' }}</small>
                                                </p>
                                            @endforeach
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                    <li class="{{ $completed }}">
                                        {!! $completedActive !!}
                                        <h5 class="mt-0 mb-1">เสร็จสิ้น</h5>
                                        @if ($repairs->status == 'เสร็จสิ้น')
                                            <p class="text-muted">
                                                {{ $thaiDateHelper->shortDateFormat($repairs->updated_at) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($repairs->updated_at)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                @endif
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end status layout -->

            <!-- start detail layout -->
            <div class="col-lg-9 mb-3">
                <div class="card ribbon-custom-box h-100">
                    <div class="card-body">
                        {{-- ส่วนที่ 0 Head paper --}}
                        <div class="col-12">
                            <div
                                class="ribbon-custom ribbon-custom-{{ $status['color'] }} ribbon-custom-top-right text-{{ $status['text'] }}">
                                @php
                                    if ($status['name'] == 'ยกเลิกโดยหัวหน้า') {
                                        $status_name = 'ยกเลิกโดยผู้อนุมัติ';
                                    } else {
                                        $status_name = $status['name'];
                                    }
                                @endphp
                                <span>{{ $status_name }}</span>
                            </div>

                            <div class="row">
                                <div class="col-auto">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('assets/images/logo-kacee.png') }}" alt="logo"
                                            width="60" height="60">
                                        <div class="mx-2 py-auto">
                                            <span>บริษัท อี .แอนด์. วี จำกัด</span><br>
                                            <span>259 ถนนเลียบคลองภาษีเจริญฝั่งใต้ แขวงหนองแขม เขตหนองแขม กรุงเทพฯ
                                                10160</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h5 class="text-center text-decoration-underline text-dark fw-bold">
                                        ใบแจ้งซ่อม{{ $order_dept->dept_name }}</h5>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <p class="text-end">วันที่ <span
                                            class="text-decoration-dotted text-dark">{{ \Carbon\Carbon::parse($repairs->created_at)->format('d') }}</span>
                                        เดือน <span
                                            class="text-decoration-dotted text-dark">{{ \Carbon\Carbon::parse($repairs->created_at)->locale('th_TH')->isoFormat('MMMM') }}</span>
                                        พ.ศ. <span
                                            class="text-decoration-dotted text-dark">{{ \Carbon\Carbon::parse($repairs->created_at)->format('Y') + 543 }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- ส่วนที่ 1 ผู้แจ้ง --}}
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
                                                        class="text-dark @if ($user->nickname) px-3 @else px-5 @endif">{{ $user->nickname ? $user->nickname : '' }}</span>
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
                                                    class="text-dark @if ($user->tel) px-3 @else px-5 @endif">{{ $user->tel ? $user->tel : '-' }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                @if ($repairs->order_dept == 'A03050200')
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="rtv">
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
                                    @if ($repairs->order_dept != 'A03050200')
                                        <div class="col-12 mt-3 mb-5">
                                            <div class="rtv">
                                                <div class="full-underline abs">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">อุปกรณ์ที่แจ้งซ่อม : </span>
                                                        <span
                                                            class="text-dark @if ($repairs->order_tool) px-3 @else px-5 @endif">{{ $repairs->order_tool }}</span>
                                                    </span>

                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">สถานที่ซ่อม : </span>
                                                        <span
                                                            class="text-dark @if ($repairs->order_address) px-3 @else px-5 @endif">{{ $repairs->order_address }}</span>
                                                    </span>

                                                    <span class="full-dotted mb-1">
                                                        <span class="bg-white">รายละเอียดปัญหา : </span>
                                                        <span
                                                            class="text-dark @if ($repairs->order_detail) px-3 @else px-5 @endif">{{ $repairs->order_detail }}</span>
                                                    </span>
                                                    <span class="full-dotted mb-1"></span>
                                                    <span class="full-dotted mb-3"></span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-12 mt-3 mb-4">
                                            <div class="rtv">
                                                <div class="full-underline abs">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">สถานที่ซ่อม : </span>
                                                        <span
                                                            class="text-dark @if ($repairs->order_address) px-3 @else px-5 @endif">{{ $repairs->order_address }}</span>
                                                    </span>

                                                    <span class="full-dotted mb-1">
                                                        <span class="bg-white">รายละเอียดปัญหา : </span>
                                                        <span
                                                            class="text-dark @if ($repairs->order_detail) px-3 @else px-5 @endif">{{ $repairs->order_detail }}</span>
                                                    </span>
                                                    <span class="full-dotted mb-1"></span>
                                                    <span class="full-dotted mb-3"></span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @php $images = json_decode($repairs->order_image, true); @endphp
                                @if (count($images) > 0)
                                    <div class="row mt-5">
                                        <div class="col-sm-2 mt-2">รูปภาพประกอบ : </div>
                                        <div class="col-sm-10 mt-2">
                                            @foreach ($images as $key => $image)
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal"
                                                    id="image{{ $key + 1 }}">
                                                    <img src="{{ url('assets/images/repair/' . $image) }}"
                                                        onerror="this.onerror=null;this.src='{{ url('assets/images/NoImage.jpg') }}'"
                                                        alt="Image {{ $key + 1 }}"
                                                        style="width: 100px;padding: 0 10px;margin-bottom: 12px;">
                                                </a>

                                                <div class="modal fade" id="imageModal" tabindex="-1" role="dialog"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <!-- START carousel-->
                                                                <div id="carouselExampleControls" class="carousel slide"
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
                                                                        href="#carouselExampleControls" role="button"
                                                                        data-bs-slide="prev">
                                                                        <span class="carousel-control-prev-icon"
                                                                            aria-hidden="true"></span>
                                                                        <span class="visually-hidden">Previous</span>
                                                                    </a>
                                                                    <a class="carousel-control-next"
                                                                        href="#carouselExampleControls" role="button"
                                                                        data-bs-slide="next">
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
                                    <div class="row col-12 mt-5 mb-5">
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

                        {{-- ส่วนที่ 2 ผู้ปฏิบัติงาน --}}
                        @if ($repairs->manager_id)
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header">
                                        ส่วนที่ 2 สำหรับผู้ปฏิบัติงาน
                                    </div>

                                    <div class="card-body">
                                        <div class="col-sm-12 mb-2">
                                            <div class="rtv">
                                                <div class="full-underline abs">
                                                    <span class="full-dotted mb-2">
                                                        <span class="bg-white">เริ่มปฏิบัติงาน : </span>
                                                        <span class="text-dark px-5">
                                                            {{ $repairs->start_date ? $thaiDateHelper->shortDateFormat($repairs->start_date) : '-' }}
                                                        </span>
                                                    </span>
                                                    <span class="full-dotted mb-2">
                                                        <span class="bg-white">ผู้ปฏิบัติงาน : </span>
                                                        <span class="text-dark px-3">
                                                            @if ($tech_name)
                                                                @php
                                                                    $i = 1;
                                                                @endphp
                                                                @foreach ($tech_name as $row)
                                                                    @php
                                                                        $worker = Auth::User()->findEmployee($row['emp_id']);
                                                                        if ($worker->nickname) {
                                                                            $work = ' ( ' . $worker->nickname . ' )';
                                                                        } else {
                                                                            $work = '';
                                                                        }
                                                                    @endphp
                                                                    <span class="px-2">
                                                                        {{ $i++ . '. ' . $worker->title . ' ' . $worker->name . ' ' . $worker->surname . $work }}
                                                                    </span>
                                                                @endforeach
                                                            @else
                                                                <span class="px-2">-</span>
                                                            @endif
                                                        </span>
                                                    </span>
                                                    <span class="full-dotted mb-2">
                                                        <span class="bg-white">หมายเหตุของผู้รับแจ้ง : </span>
                                                        <span class="text-dark px-3">
                                                            {{ $repairs->manager_detail ? $repairs->manager_detail : '-' }}
                                                        </span>
                                                    </span>
                                                    <span class="full-dotted"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-sm-12 mt-5 mb-3">
                                                @if ($tech_detail)
                                                    @foreach ($tech_detail as $list)
                                                        @php
                                                            if ($list['continue_date']) {
                                                                $con_date = 'ดำเนินงานต่อ / ' . \Carbon\Carbon::parse($list['continue_date'])->thaidate('j M Y');
                                                            } elseif ($list['end_date']) {
                                                                $con_date = 'ปิดงาน / ' . \Carbon\Carbon::parse($list['end_date'])->thaidate('j M Y');
                                                            }
                                                        @endphp
                                                        <div class="post-user-comment-box bg-light rounded my-1">
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
                                                                    <small
                                                                        class="text-muted ms-2">{{ $con_date }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>

                                            <div class="col-sm-6 text-center"></div>
                                            <div class="col-sm-6 mt-3 text-center">
                                                <div class="rtv">
                                                    <div class="full-underline abs px-3">
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
                                        </div>
                                    </div>

                                    <div class="card-footer"></div>
                                </div>
                            </div>
                        @endif

                        {{-- ส่วนที่ 3 ผู้ตรวจสอบ --}}
                        @if ($ap_detail)
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header">
                                        ส่วนที่ 3 สำหรับผู้ตรวจสอบ
                                    </div>

                                    <div class="card-body">
                                        <div class="col-sm-12 mb-5">
                                            <div class="rtv">
                                                <div class="full-underline abs">
                                                    <span class="full-dotted mb-2">
                                                        <span class="bg-white">วันที่ซ่อมเสร็จ : </span>
                                                        <span class="text-dark px-5">
                                                            {{ $thaiDateHelper->shortDateFormat($repairs->end_date) }}
                                                        </span>
                                                    </span>

                                                    <span class="full-dotted mb-2">
                                                        <span class="bg-white">ค่าใช้จ่ายในงาน : </span>
                                                        @if ($repairs->price)
                                                            <span class="text-dark px-5">
                                                                {{ $repairs->price }}
                                                                &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp บาท</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($ap_detail)
                                            @foreach ($ap_detail as $rows)
                                                <div class="post-user-comment-box bg-light rounded my-1">
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
                                                            <small class="text-muted ms-2">
                                                                {{ \Carbon\Carbon::parse($rows['date'])->thaidate('D j M Y, เวลา H:i น.') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                        @if ($user_detail)
                                            @foreach ($user_detail as $rowu)
                                                <div class="post-user-comment-box bg-light rounded my-1">
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
                                                            {{ $rowu['detail'] ? $rowu['detail'] : 'ขอบคุณครับ/ค่ะ' }}
                                                            <small class="text-muted ms-2">
                                                                {{ \Carbon\Carbon::parse($rowu['date'])->thaidate('D j M Y, เวลา H:i น.') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                        <div class="row mt-5">
                                            <div class="col-sm-6 text-center">
                                                <div class="rtv">
                                                    <div class="full-underline abs px-3">
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
                                                <p class="fw-bold">ผู้ตรวจสอบ / ว.ด.ป.</p>
                                            </div>
                                            <div class="col-sm-6 text-center">
                                                <div class="rtv">
                                                    <div class="full-underline abs px-3">
                                                        <span class="full-dotted">
                                                            @if ($user_detail)
                                                                @foreach ($user_detail as $rowu)
                                                                    @php $userDetail = Auth::User()->findEmployee($rowu['emp_id']); @endphp
                                                                    {{ $userDetail->name . ' ' . $userDetail->surname }} /
                                                                    {{ $thaiDateHelper->shortDateFormat($rowu['date']) }}
                                                                @endforeach
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                <br>
                                                <p class="fw-bold">ผู้ตรวจรับ / ว.ด.ป.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer"></div>
                                </div>
                            </div>
                        @endif

                        {{-- ส่วนที่4 รายการอุปกรณ์ที่ใช้ในงาน --}}
                        @if (count($withdraw) > 0)
                            <div class="card border">
                                <div class="card-header">รายการอุปกรณ์ที่ใช้ในงาน</div>
                                <div class="card-body">
                                    <table class="table">
                                        <thead>
                                            <th>ลำดับ</th>
                                            <th>ชื่ออุปกรณ์</th>
                                            <th class="text-center">จำนวนชิ้น</th>
                                            <th>เบิกอุปกรณ์</th>
                                            <th>ผู้เบิก</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($withdraw as $wd)
                                                @php
                                                    $_user = Auth::User()->findEmployee($wd->emp_id);
                                                    $inventory = ($wd->status_inventory == 0) ? 'เบิกจากคลัง': 'สั่งซื้อใหม่' ;
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>{{ $wd->products_name }}</td>
                                                    <td class="text-center">{{ $wd->qty }}</td>
                                                    <td>{{ $inventory }}</td>
                                                    <td>{{ $_user->name }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer"></div>
                            </div>
                        @endif

                        {{-- Print File --}}
                        <div class="mt-3">
                            <div class="text-end d-print-none">
                                <button type="button" class="btn btn-secondary waves-effect waves-light me-2"
                                    onclick="javascript:window.history.back();">
                                    <i class="mdi mdi-keyboard-backspace me-1"></i> Back</button>

                                <button type="button" class="btn btn-primary waves-effect waves-light"
                                    data-bs-toggle="modal" data-bs-target="#printLeaveModal"><i
                                        class="mdi mdi-printer me-1"></i> Print</button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <!-- end detail layout -->
        </div>
        <!-- end layout -->

        <!-- start modal -->
        <div id="printLeaveModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            role="dialog" aria-labelledby="printLeaveModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-full-width modal-dialog-centered">
                {{-- <div class="modal-dialog modal-lg modal-dialog-centered"> --}}
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="printLeaveModalLabel">พิมพ์ใบแจ้งซ่อม</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <embed src="{{ url('repair/detail-print/pdf', $repairs->id) }}" frameborder="0" width="100%"
                            height="700px">
                        {{-- <embed src="{{ url('leave/document-record-working/pdf', $leave->id) }}" frameborder="0" width="100%" height="600px"> --}}
                    </div>
                </div>
            </div>
        </div>
        <!-- end modal -->

        {{-- Modal Image --}}
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
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
    </script>
@endsection
