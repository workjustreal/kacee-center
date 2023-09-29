@extends('layouts.master-nopreloader-layout', ['page_title' => 'ฟอร์มใบการอนุมัติการลงสินค้า'])
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Sales Doc.</a></li>
                            <li class="breadcrumb-item active">ฟอร์มใบการอนุมัติการลงสินค้า</li>
                        </ol>
                    </div>
                    <h4 class="page-title">รหัสใบแจ้ง {{ $datas->gen_id }}</h4>
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
                        <h4 class="header-title mb-3">สถานะใบแจ้ง {{ $datas->gen_id }}</h4>
                        <div class="track-order-list">
                            <ul class="list-unstyled">
                                @php
                                    // รออนุมัติ
                                    $pending = '';
                                    $pendingActive = '';
                                    // หัวหน้าอนุมัติ
                                    $approved = '';
                                    $approvedActive = '';
                                    // หัวหน้าอนุมัติ
                                    $complete = '';
                                    $completeActive = '';
                                    // ยกเลิกโดยผู้แจ้ง
                                    $cancel1 = '';
                                    $cancel1Active = '';
                                    // ยกเลิกโดยผู้อนุมัติ
                                    $cancel2 = '';
                                    $cancel2Active = '';
                                    // ยกเลิกโดยผู้ดูแลระบบ
                                    $cancel3 = '';
                                    $cancel3Active = '';
                                    
                                    if ($datas->status == 'รออนุมัติ') {
                                        $pending = '';
                                        $pendingActive = '<span class="active-dot dot"></span>';
                                    }
                                    // elseif ($datas->status == 'อนุมัติ') {
                                    //     $pending = 'completed';
                                    //     $approvedActive = '<span class="active-dot dot"></span>';
                                    // }
                                    elseif ($datas->status == 'เสร็จสิ้น') {
                                        $pending = 'completed';
                                        $approved = 'completed';
                                        $completeActive = '<span class="active-dot dot"></span>';
                                    } elseif ($datas->status == 'ยกเลิกโดยผู้แจ้ง') {
                                        $pending = 'completed';
                                        $cancel1Active = '<span class="active-dot dot"></span>';
                                    } elseif ($datas->status == 'ยกเลิกโดยผู้อนุมัติ') {
                                        $pending = 'completed';
                                        $cancel2Active = '<span class="active-dot dot"></span>';
                                    } elseif ($datas->status == 'ยกเลิกโดยผู้ดูแลระบบ') {
                                        $pending = 'completed';
                                        $approved = 'completed';
                                        $cancel3Active = '<span class="active-dot dot"></span>';
                                    }
                                @endphp

                                @if ($datas->status == 'ยกเลิกโดยผู้แจ้ง')
                                    <li class="{{ $pending }}">
                                        {!! $pendingActive !!}
                                        <h5 class="mt-0 mb-1">รออนุมัติ</h5>
                                        <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($datas->created_at) }}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($datas->created_at)->format('H:i') . ' น.' }}</small>
                                        </p>
                                    </li>
                                    <li class="{{ $cancel1 }}">
                                        {!! $cancel1Active !!}
                                        <h5 class="mt-0 mb-1">ยกเลิกโดยผู้แจ้ง</h5>
                                        @if ($datas->approve_date == '')
                                            <p class="text-muted">
                                                {{ $thaiDateHelper->shortDateFormat($datas->updated_at) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($datas->updated_at)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                @elseif ($datas->status == 'ยกเลิกโดยผู้อนุมัติ')
                                    <li class="{{ $pending }}">
                                        {!! $pendingActive !!}
                                        <h5 class="mt-0 mb-1">รออนุมัติ</h5>
                                        <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($datas->created_at) }}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($datas->created_at)->format('H:i') . ' น.' }}</small>
                                        </p>
                                    </li>
                                    <li class="{{ $approved }}">
                                        {!! $approvedActive !!}
                                        <h5 class="mt-0 mb-1">ยกเลิกโดยผู้อนุมัติ</h5>
                                        @if ($datas->approve_date != '')
                                            <p class="text-muted">
                                                {{ $thaiDateHelper->shortDateFormat($datas->approve_date) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($datas->approve_date)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                @elseif ($datas->status == 'ยกเลิกโดยผู้ดูแลระบบ')
                                    <li class="{{ $pending }}">
                                        {!! $pendingActive !!}
                                        <h5 class="mt-0 mb-1">รออนุมัติ</h5>
                                        <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($datas->created_at) }}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($datas->created_at)->format('H:i') . ' น.' }}</small>
                                        </p>
                                    </li>
                                    <li class="{{ $approved }}">
                                        {!! $approvedActive !!}
                                        <h5 class="mt-0 mb-1">ผู้รับทราบ</h5>
                                        @if ($datas->approve_date != '')
                                            <p class="text-muted">
                                                {{ $thaiDateHelper->shortDateFormat($datas->approve_date) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($datas->approve_date)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                    <li class="{{ $cancel3 }}">
                                        {!! $cancel3Active !!}
                                        <h5 class="mt-0 mb-1">ยกเลิกโดยผู้ดูแลระบบ</h5>
                                        @if ($datas->approve_date != '')
                                            <p class="text-muted">
                                                {{ $thaiDateHelper->shortDateFormat($datas->updated_at) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($datas->updated_at)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li>
                                @else
                                    <li class="{{ $pending }}">
                                        {!! $pendingActive !!}
                                        <h5 class="mt-0 mb-1">รอรับทราบ</h5>
                                        {{-- <h5 class="mt-0 mb-1">รออนุมัติ</h5> --}}
                                        <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($datas->created_at) }}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($datas->created_at)->format('H:i') . ' น.' }}</small>
                                        </p>
                                    </li>
                                    {{-- <li class="{{ $approved }}">
                                        {!! $approvedActive !!}
                                        <h5 class="mt-0 mb-1">ผู้อนุมัติ</h5>
                                        @if ($datas->approve_date != '')
                                            <p class="text-muted">
                                                {{ $thaiDateHelper->shortDateFormat($datas->approve_date) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($datas->approve_date)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @else
                                            <p class="text-muted">&nbsp;</p>
                                        @endif
                                    </li> --}}
                                    <li class="{{ $complete }}">
                                        {!! $completeActive !!}
                                        <h5 class="mt-0 mb-1">ผู้รับทราบ</h5>
                                        @if ($datas->submit_date != '')
                                            <p class="text-muted">
                                                {{ $thaiDateHelper->shortDateFormat($datas->submit_date) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($datas->submit_date)->format('H:i') . ' น.' }}</small>
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
                                <span>{{ $status['name'] == 'รออนุมัติ' ? 'รอรับทราบ' : $status['name'] }}</span>
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
                                    <h5 class="text-center text-decoration-underline text-dark fw-bold">ฝ่ายขาย & ฝ่ายขนส่ง
                                    </h5>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <p class="text-end">วันที่ <span
                                            class="text-decoration-dotted text-dark">{{ \Carbon\Carbon::parse($datas->created_at)->format('d') }}</span>
                                        เดือน <span
                                            class="text-decoration-dotted text-dark">{{ \Carbon\Carbon::parse($datas->created_at)->locale('th_TH')->isoFormat('MMMM') }}</span>
                                        พ.ศ. <span
                                            class="text-decoration-dotted text-dark">{{ \Carbon\Carbon::parse($datas->created_at)->format('Y') + 543 }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- ส่วนที่ 1 --}}
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header">แบบฟอร์ม การอนุมัติให้ลงสินค้าให้ลูกค้า KC</div>
                                <div class="card-body pb-5">
                                    <div class="col-sm-12 pb-5">
                                        <div class="rtv">
                                            <div class="full-underline abs row">
                                                <div class="col-6">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">รหัสลูกค้า : </span>
                                                        <span
                                                            class="text-dark @if ($datas->customer_code) px-4 @else px-5 @endif">
                                                            {{ $datas->customer_code }}
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">ชื่อลูกค้า : </span>
                                                        <span
                                                            class="text-dark @if ($datas->customer_name) px-4 @else px-5 @endif">
                                                            {{ $datas->customer_name }}
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">เลขที่ IV : </span>
                                                        <span
                                                            class="text-dark @if ($datas->invoice) px-4 @else px-5 @endif">
                                                            {{ $datas->invoice }}
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">ยอดเงิน : </span>
                                                        <span
                                                            class="text-dark @if ($datas->pay) px-4 @else px-5 @endif">
                                                            {{ $_pay }} บาท
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-12">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">หมายเหตุ : </span>
                                                        <span
                                                            class="text-dark @if ($datas->comment) px-4 @else px-5 @endif">
                                                            {{ $datas->comment }}
                                                        </span>
                                                    </span>
                                                </div>

                                                <div class="col-6 p-3 text-center">
                                                    <div class="rtv">
                                                        <div class="full-underline abs px-3">
                                                            <span class="full-dotted">
                                                                @if ($datas->emp_id)
                                                                    @php
                                                                        $emp = Auth::User()->findEmployee($datas->emp_id);
                                                                        if ($emp->nickname) {
                                                                            $emp_name = $emp->name . ' ' . $emp->surname . ' ( ' . $emp->nickname . ' )';
                                                                        } else {
                                                                            $emp_name = $emp->name . ' ' . $emp->surname;
                                                                        }
                                                                    @endphp
                                                                    {{ $emp_name }} /
                                                                    {{ $thaiDateHelper->shortDateFormat($datas->created_at) }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <p class="fw-bold">ผู้ลงบันทึก / ว.ด.ป.</p>
                                                </div>
                                                <div class="col-6 p-3 text-center">
                                                    <div class="rtv">
                                                        <div class="full-underline abs px-3">
                                                            <span class="full-dotted">
                                                                @if ($datas->submit_id)
                                                                    @php
                                                                        $sub = Auth::User()->findEmployee($datas->submit_id);
                                                                        if ($sub->nickname) {
                                                                            $sub_name = $sub->name . ' ' . $sub->surname . ' ( ' . $sub->nickname . ' )';
                                                                        } else {
                                                                            $sub_name = $sub->name . ' ' . $sub->surname;
                                                                        }
                                                                    @endphp
                                                                    {{ $sub_name }} /
                                                                    {{ $thaiDateHelper->shortDateFormat($datas->submit_date) }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <p class="fw-bold">ผู้รับทราบ / ว.ด.ป.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer mt-5"></div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary waves-effect waves-light me-2"
                            onclick="history.back();">
                            <i class="mdi mdi-keyboard-backspace me-1"></i> Back
                        </button>

                    </div>
                </div>
            </div>
            <!-- end detail layout -->

            <div class="col-3"></div>
            <div class="col-9">
                @if ($log)
                <div class="col-sm-12">
                    <div class="border-0 border-bottom text-center">Action</div>
                        <div class="border border-light p-2 mb-3">
                        @foreach ($log as $list)
                            @php
                                $_log = Auth::User()->findEmployee($list->emp_id);
                                $text = '';
                                switch ($list->description) {
                                    case 'รออนุมัติ':
                                        $text = 'สร้างใบลงสินค้าให้ลูกค้า (โดยฟอร์มบันทึก)';
                                        break;
                                    case 'แก้ไข':
                                        $text = 'แก้ไขข้อมูล';
                                        break;
                                    case 'เสร็จสิ้น':
                                        $text = 'ฝ่ายขนส่งรับทราบเรียบร้อย';
                                        break;
                                    case 'ยกเลิกโดยผู้แจ้ง':
                                        $text = 'ยกเลิกใบลงสินค้า';
                                        break;
                                    case 'ยกเลิกโดยผู้อนุมัติ':
                                        $text = 'ยกเลิกใบลงสินค้า';
                                        break;
                                    default:
                                        $text = $list->description;
                                }
                            @endphp
                            <div class="post-user-comment-box bg-white rounded my-1 mb-2">
                                <div class="d-flex align-items-start">
                                    <img class="me-2 avatar-sm rounded-circle"
                                        src="{{ url('assets/images/users/thumbnail/user-1.jpg') }}"
                                        onerror="this.onerror=null;this.src='{{ url('assets/images/users/thumbnail/user-1.jpg') }}'"
                                        alt="image">
                                    <div class="w-100">
                                        <h5 class="mt-0">
                                            {{ $_log->name. ' ' . $_log->surname}}
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($list->created_at)->locale('th_TH')->diffForHumans() }}
                                            </small>
                                        </h5>
                                        <i class="mdi mdi-share-outline me-1"></i>
                                        {{ $text }}
                                        <small class="text-muted ms-2">
                                            {{ \Carbon\Carbon::parse($list->created_at)->thaidate('D j M Y, เวลา H:i น.') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <!-- end layout -->

    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
@endsection
