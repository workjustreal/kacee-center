@extends('layouts.master-layout', ['page_title' => 'ดูรายละเอียดคำขอ'])
@section('css')
    <link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css -->
    <style>
        .text-decoration-dotted {
            text-decoration-line: underline;
            text-decoration-style: dotted;
            text-decoration-thickness: 0px;
        }

        .detail {
            text-decoration: underline;
            text-decoration-style: dotted;
        }

        .full-underline {
            width: 100%;
        }

        .full-underline span.full-dotted {
            display: block;
            width: 100%;
            height: 16px;
            border-bottom: 0.8px dotted #e7e7e7;
        }

        .comment-underline {
            display: block;
            width: 100%;
            border-bottom: 0.8px dotted #e7e7e7 !important;
        }

        .line {
            display: block;
            margin: 25px
        }

        .line h2 {
            font-size: 15px;
            text-align: center;
            border-bottom: 1.5px solid #e7e7e7;
            position: relative;
        }

        .line h2 span {
            background-color: #f2f3f5;
            position: relative;
            top: 10px;
            padding: 0 20px;
        }

        .carousel-custom {
            -webkit-box-shadow: -1px 6px 20px -4px rgba(0, 0, 0, 0.75);
            -moz-box-shadow: -1px 6px 20px -4px rgba(0, 0, 0, 0.75);
            box-shadow: -1px 6px 20px -4px rgba(0, 0, 0, 0.75);
        }
    </style>
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
                            <li class="breadcrumb-item active">Product Discount</li>
                        </ol>
                    </div>
                    <h4 class="page-title">รายละเอียดคำขอ</h4>
                </div>
            </div>
        </div>
        @error('file')
            <div class="alert alert-danger mt-2 mb-2">ไฟล์ไม่ถูกต้อง</div>
        @enderror
        <!-- end page title -->
        @inject('thaiDateHelper', '\App\Services\ThaiDateHelperService')
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="track-order-list">
                            <ul class="list-unstyled">
                                @foreach ($result as $val)
                                    <li class="completed">
                                        <h5 class="mt-0 mb-1">สร้างคำขอ</h5>
                                        <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($val->created_at) }}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($val->created_at)->format('H:i') . ' น.' }}</small>
                                        </p>
                                    </li>
                                    <li class="{{ $mn_approve }}">
                                        {!! $mn_dot !!}
                                        @if ($status == 3)
                                            <h5 class="mt-0 mb-1 text-danger">หัวหน้าไม่อนุมัติ</h5>
                                        @else
                                            <h5 class="mt-0 mb-1">รอหัวหน้าอนุมัติ</h5>
                                        @endif
                                        @if ($mn_date == '')
                                            <p class="text-muted mb-4"><small class="text-muted"></small>
                                            </p>
                                        @else
                                            <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($mn_date) }}<small
                                                    class="text-muted">
                                                    {{ \Carbon\Carbon::parse($mn_date)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @endif
                                    </li>
                                    <li class="{{ $st_approve }}">
                                        {!! $secretary_dot !!}
                                        @if ($status == 0)
                                            <h5 class="mt-0 mb-1 text-danger">เลขาไม่อนุมัติ</h5>
                                        @else
                                            <h5 class="mt-0 mb-1">รอเลขาอนุมัติ</h5>
                                        @endif
                                        @if ($st_date == '')
                                            <p class="text-muted mb-4"><small class="text-muted"></small></p>
                                        @else
                                            <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($st_date) }}<small
                                                    class="text-muted">
                                                    {{ \Carbon\Carbon::parse($st_date)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @endif
                                    </li>
                                    <li class="{{ $st_approve }}">
                                        {!! $succes_dot !!}
                                        <h5 class="mt-0 mb-1">เสร็จสิ้น</h5>
                                        @if ($st_date == '')
                                            <p class="text-muted mb-4"><small class="text-muted"></small></p>
                                        @elseif($status == 9)
                                            <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($st_date) }}<small
                                                    class="text-muted">
                                                    {{ \Carbon\Carbon::parse($st_date)->format('H:i') . ' น.' }}</small>
                                            </p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <span class="line">
                    <h2><span class="text-muted">action</span></h2>
                </span>
                @php
                    $name_mnapp = [];
                @endphp
                @foreach ($log as $logact)
                    <div class="bg-white mb-2 rounded">
                        <div class="col-12">
                            <div class="d-flex flex-row">
                                <div class="p-2">
                                    <img src="{{ URL::asset('assets/images/users/thumbnail/user-1.jpg') }}"
                                        class="rounded-circle" width="36px" height="36px" alt="">
                                </div>
                                <div class="p-2">
                                    <h5 class="text-dark">
                                        <span></span>{{ $logact->name }}
                                        {{ $logact->surname }}
                                        ({{ $logact->nickname }})
                                        <small class="text-muted">
                                            {{ $thaiDateHelper->shortDateFormat($logact->updated_at) }},
                                            {{ \Carbon\Carbon::parse($logact->updated_at)->format('H:i') . ' น.' }}</small>
                                    </h5>
                                    @if (
                                        $logact->description == 'ManagerApprove' ||
                                            $logact->description == 'Manager DisApprove' ||
                                            $logact->description == 'SecretaryApprove' ||
                                            $logact->description == 'Secretary DisApprove' ||
                                            $logact->description == 'Secretary Comment')
                                        @if ($logact->description == 'ManagerApprove')
                                            @php
                                                $name_mnapp[] = $logact->name . ' ' . $logact->surname . ' ' . '(' . $logact->nickname . ')';
                                            @endphp
                                            <i class="mdi mdi-share-outline me-1"></i><small
                                                class="text-success">(อนุมัติ)</small> {{ $logact->comment }}
                                        @elseif($logact->description == 'Manager DisApprove')
                                            <i class="mdi mdi-share-outline me-1"></i><small
                                                class="text-danger">(ไม่อนุมัติ)</small> {{ $logact->comment }}
                                            @php
                                                $name_mnapp[] = $logact->name . ' ' . $logact->surname . ' ' . '(' . $logact->nickname . ')';
                                            @endphp
                                        @endif
                                        @if ($logact->description == 'SecretaryApprove')
                                            <i class="mdi mdi-share-outline me-1"></i><small
                                                class="text-success">(อนุมัติ)</small> {{ $logact->comment }}
                                            @php
                                                $comment_secapp = $logact->comment;
                                            @endphp
                                        @elseif($logact->description == 'Secretary DisApprove')
                                            <i class="mdi mdi-share-outline me-1"></i><small
                                                class="text-danger">(ไม่อนุมัติ)</small> {{ $logact->comment }}
                                            @php
                                                $comment_secapp = $logact->comment;
                                            @endphp
                                        @elseif($logact->description == 'Secretary Comment')
                                            <i class="mdi mdi-share-outline me-1"></i><small
                                                class="text-blue">(เพิ่มเติม)</small> {{ $logact->comment }}
                                        @endif
                                    @else
                                        <i class="mdi mdi-share-outline me-1"></i> {{ $logact->description }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        @foreach ($result as $detail)
                            <div class="d-flex justify-content-end">{{ $detail->doc_id }}</div>
                            <div class="d-flex align-items-center">
                                <img src="{{ URL::asset('assets/images/logo-kacee.png') }}" alt="logo" width="60"
                                    height="60">
                                <div class="mx-2 py-auto">
                                    <span>บริษัท อี .แอนด์. วี จำกัด</span><br>
                                    <span>259 ถนนเลียบคลองภาษีเจริญฝั่งใต้ แขวงหนองแขม เขตหนองแขม กรุงเทพฯ
                                        10160</span>
                                </div>
                            </div>
                            @if ($status == 9)
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary btn-rounded waves-effect waves-light"
                                        data-bs-toggle="modal" data-bs-target="#printModal" id="btnPrint"
                                        value="{{ $detail->doc_id }}">
                                        <span class="btn-label"><i class="mdi mdi-printer me-1"></i></span>Print
                                    </button>
                                </div>
                            @endif
                            <h5 class="text-center mb-3"><b><u>ใบคำขอค่าส่วนลดสินค้า</u></b></h5>
                            <div class="card border ribbon-custom-box">
                                <div class="card-header">
                                    แบบฟอร์มขอส่วนลดค่าสินค้า/ค่าซ่อม เนื่องจากความผิดพลาด
                                </div>
                                <div class="card-body mb-5">
                                    @if ($detail->doc_status == 9)
                                        <div class="ribbon-custom ribbon-custom-success ribbon-custom-top-right text-white">
                                            <span>อนุมัติแล้ว</span>
                                        </div>
                                    @elseif($detail->doc_status == 0 || $detail->doc_status == 3)
                                        <div class="ribbon-custom ribbon-custom-danger ribbon-custom-top-right text-white">
                                            <span>ไม่อนุมัติ</span>
                                        </div>
                                    @else
                                        <div class="ribbon-custom ribbon-custom-blue ribbon-custom-top-right text-white">
                                            <span>กำลังดำเนินการ</span>
                                        </div>
                                    @endif
                                    <div class="col-sm-12 pb-5">
                                        <div class="rtv">
                                            <div class="full-underline abs row">
                                                <div class="col-6">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">รหัสลูกค้า : </span>
                                                        <span class="text-dark px-3">
                                                            {{ $detail->customer_code }}
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">ชื่อลูกค้า : </span>
                                                        <span class="text-dark px-3">
                                                            {{ $detail->customer_name }}
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">สถานะลูกค้า : </span>
                                                        <span class="text-dark px-3">
                                                            {{ $detail->customer_status }}
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">วงเงินอนุมัติ : </span>
                                                        <span class="text-dark px-3">
                                                            {{ $detail->limit }}
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-12">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">เจ้าหน้าที่ขายที่รับผิดชอบ : </span>
                                                        <span class="text-dark px-3">
                                                            {{ $detail->employee }}
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="row mt-2 mb-4">
                                                    <div class="col-sm-1">สาเหตุ : </div>
                                                    <div class="col-sm-10">
                                                        <div class="row">
                                                            <div class="col-sm-4 mb-1">
                                                                @if ($detail->mistake == 'ความผิดพลาดจากการผลิตสินค้า')
                                                                    <img src="{{ asset('assets/images/checkbox-mark.png') }}"
                                                                        height="18">
                                                                @else
                                                                    <img src="{{ asset('assets/images/checkbox.png') }}"
                                                                        height="18">
                                                                @endif
                                                                <span>ความผิดพลาดจากการผลิตสินค้า</span>
                                                            </div>
                                                            <div class="col-sm-4 mb-1">
                                                                @if ($detail->mistake == 'ความผิดพลาดจากการรับคำสั่งซื้อ')
                                                                    <img src="{{ asset('assets/images/checkbox-mark.png') }}"
                                                                        height="18">
                                                                @else
                                                                    <img src="{{ asset('assets/images/checkbox.png') }}"
                                                                        height="18">
                                                                @endif
                                                                <span>ความผิดพลาดจากการรับคำสั่งซื้อ</span>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4 mb-1">
                                                                @if ($detail->mistake == 'ความผิดพลาดจากการขนส่งสินค้า')
                                                                    <img src="{{ asset('assets/images/checkbox-mark.png') }}"
                                                                        height="18">
                                                                @else
                                                                    <img src="{{ asset('assets/images/checkbox.png') }}"
                                                                        height="18">
                                                                @endif
                                                                <span>ความผิดพลาดจากการขนส่งสินค้า</span>
                                                            </div>
                                                            <div class="col-sm-4 mb-1">
                                                                @if ($detail->mistake == 'ความผิดพลาดจากสินค้าและอุปกรณ์')
                                                                    <img src="{{ asset('assets/images/checkbox-mark.png') }}"
                                                                        height="18">
                                                                @else
                                                                    <img src="{{ asset('assets/images/checkbox.png') }}"
                                                                        height="18">
                                                                @endif
                                                                <span>ความผิดพลาดจากสินค้าและอุปกรณ์ </span>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4 mb-1">
                                                                @if ($detail->mistake == 'ความผิดพลาดที่เกิดจากลูกค้าเอง')
                                                                    <img src="{{ asset('assets/images/checkbox-mark.png') }}"
                                                                        height="18">
                                                                @else
                                                                    <img src="{{ asset('assets/images/checkbox.png') }}"
                                                                        height="18">
                                                                @endif
                                                                <span>ความผิดพลาดที่เกิดจากลูกค้าเอง</span>
                                                            </div>
                                                            <div class="col-sm-4 mb-1">
                                                                @if ($detail->mistake == 'other')
                                                                    <img src="{{ asset('assets/images/checkbox-mark.png') }}"
                                                                        height="18">
                                                                @else
                                                                    <img src="{{ asset('assets/images/checkbox.png') }}"
                                                                        height="18">
                                                                @endif
                                                                <span>อื่นๆ</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($detail->mistake == 'other')
                                                    <div class="col-12">
                                                        <span class="full-dotted mb-3">
                                                            <span class="bg-white">สาเหตุอื่นๆ : </span>
                                                            <span class="text-dark px-3">
                                                                {{ $detail->note }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                @endif
                                                <div class="col-6">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">รายการสินค้า : </span>
                                                        <span class="text-dark px-3">
                                                            {{ $detail->product_list }}
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">เลขที่ IV : </span>
                                                        <span class="text-dark px-3">
                                                            {{ $detail->invoice }}
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-12">
                                                    <span class="full-dotted mb-3">
                                                        <span class="bg-white">ยอดเงินที่ขอชดเชย /
                                                            ค่าเสียหายที่ลูกค้าร้องขอ
                                                            :
                                                        </span>
                                                        <span class="text-dark px-3">
                                                            {{ $detail->customer_request }}
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-12">
                                                    <span class="mb-5">
                                                        <span class="bg-white">รายละเอียด : </span>
                                                        <span class="text-dark px-3">
                                                            {{ $detail->description }}
                                                        </span>
                                                    </span>
                                                </div>
                                                @foreach ($log as $again)
                                                    @if ($again->description == 'แก้ไขเพื่อขออีกครั้ง')
                                                        <div class="col-12">
                                                            <span class="detail mb-3">
                                                                <span class="bg-white text-primary"><b
                                                                        class="text-danger">**</b> รายละเอียด (ขออีกครั้ง)
                                                                    : </span>
                                                                <span class="text-dark px-3">
                                                                    {{ $again->comment }}
                                                                </span>
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                                <div class="row justify-content-end mt-3">
                                                    <div class="col-5 text-center">
                                                        <div class="full-underline abs px-3">
                                                            <span class="full-dotted">
                                                                {{ $detail->name }} &nbsp; {{ $detail->surname }}
                                                                ({{ $detail->nickname }})
                                                                /
                                                                {{ $thaiDateHelper->shortDateFormat($detail->created_at) }}
                                                            </span>
                                                        </div>
                                                        @php
                                                            $_dept = Auth::User()->findDepartment($detail->dept_id);
                                                        @endphp
                                                        ( {{ $_dept->dept_name }} )
                                                        <br>
                                                        <p class="fw-bold">ผู้ลงบันทึกร้องขอ / ว.ด.ป.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer"></div>
                            </div>
                        @endforeach
                        @if ($wimage->isNotEmpty())
                            <div class="card border">
                                <div class="card-header">
                                    รูปภาพ
                                </div>
                                <div class="card-body">
                                    @foreach ($result as $img)
                                        @php
                                            $img = json_decode($img->image);
                                        @endphp
                                    @endforeach
                                    <div class="row">
                                        @foreach ($img as $key => $valimg)
                                            <div class="col-6 col-lg-2">
                                                <a href="javascript:void(0)">
                                                    <img src="{{ asset('assets/images/discount/' . $valimg) }}"
                                                        class="w-100" alt="discount image" title="discount image"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#imageslide{{ $key }}">
                                                </a>
                                            </div>
                                            <div class="modal fade" id="imageslide{{ $key }}" tabindex="-1"
                                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div id="carouselExample{{ $key }}"
                                                                class="carousel slide">
                                                                <div class="carousel-inner">
                                                                    @foreach ($img as $index => $slide)
                                                                        <div
                                                                            class="carousel-item {{ $index == $key ? 'active' : '' }}">
                                                                            <a href="{{ asset('assets/images/discount/' . $slide) }}"
                                                                                target="_blank">
                                                                                <img src="{{ asset('assets/images/discount/' . $slide) }}"
                                                                                    class="d-block w-100"
                                                                                    alt="discount image">
                                                                            </a>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <button class="carousel-control-prev" type="button"
                                                                    data-bs-target="#carouselExample{{ $loop->index }}"
                                                                    data-bs-slide="prev">
                                                                    <i class="fe-chevron-left fs-1 text-muted me-5"></i>
                                                                    <span class="visually-hidden">Previous</span>
                                                                </button>
                                                                <button class="carousel-control-next" type="button"
                                                                    data-bs-target="#carouselExample{{ $loop->index }}"
                                                                    data-bs-slide="next">
                                                                    <i class="fe-chevron-right fs-1 text-muted ms-5"></i>
                                                                    <span class="visually-hidden">Next</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($wfile->isNotEmpty())
                            <div class="card border">
                                <div class="card-header">
                                    ไฟล์
                                </div>
                                <div class="card-body">
                                    {{-- download file --}}
                                    @if ($wfile->isNotEmpty())
                                        <div class="col-xl-3 col-lg-6">
                                            @foreach ($result as $file)
                                                <a href="{{ url('sales-document/discount-mistake/productdiscount/file-download/' . $file->file) }}"
                                                    class="text-muted fw-bold">
                                                    <div class="card m-1 shadow-none border">
                                                        <div class="p-2">
                                                            <div class="row align-items-center">
                                                                <div class="col-auto pe-0">
                                                                    <div class="avatar-sm">
                                                                        <span
                                                                            class="avatar-title bg-soft-primary text-primary rounded">
                                                                            <i class="mdi mdi-folder-zip font-18"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col text-primary">
                                                                    {{ $file->file }} <br>
                                                                    <small class="mb-0 font-10 text-dark">download</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @php
                    $a = 0;
                    $b = 0;
                    $c = 0;
                    $d = 0;
                    $_manager = '';
                    $_secretary = '';
                    $_mncomment = [];
                    $_seccomment = [];
                    $_mn_app_date = [];
                    foreach ($log as $applog) {
                        if ($applog->description == 'ManagerApprove') {
                            $_manager = 'true';
                            $_mncomment[] = '<span class="text-success">(อนุมัติ)</span>' . ' ' . $applog->comment . '';
                            $_mn_app_date[] = $thaiDateHelper->shortDateFormat($applog->updated_at);
                        } elseif ($applog->description == 'Manager DisApprove') {
                            $_manager = 'true';
                            $_mncomment[] = '<span class="text-danger">(ไม่อนุมัติ)</span>' . ' ' . $applog->comment . '';
                            $_mn_app_date[] = $thaiDateHelper->shortDateFormat($applog->updated_at);
                        }
                        if ($applog->description == 'SecretaryApprove') {
                            $_secretary = 'true';
                            $_seccomment[] = '<span class="text-success">(อนุมัติ)</span>' . ' ' . $applog->comment . '';
                        } elseif ($applog->description == 'Secretary DisApprove') {
                            $_secretary = 'true';
                            $_seccomment[] = '<span class="text-danger">(ไม่อนุมัติ)</span>' . ' ' . $applog->comment . '';
                        } elseif ($applog->description == 'Secretary Comment') {
                            $_secretary = 'true';
                            $_seccomment[] = '<span class="text-blue">(เพิ่มเติม)</span>' . ' ' . $applog->comment . '';
                        }
                    }
                @endphp
                @if (
                    ($status == 2 && Auth::User()->checkApproveMar() && is_int($admin_manager)) ||
                        $status == 1 ||
                        $status == 3 ||
                        $status == 9 ||
                        $status == 0)
                    <div class="card ribbon-box">
                        <div class="card-body">
                            <div class="card border">
                                <div class="card-header px-5">
                                    ส่วนรับทราบ (หัวหน้า)
                                </div>
                                <div class="card-body">
                                    @if ($status == 3)
                                        <div class="ribbon-two ribbon-two-danger">
                                            <span>ไม่อนุมัติ</span>
                                        </div>
                                    @elseif($status == 1 || $status == 9 || $status == 0)
                                        <div class="ribbon-two ribbon-two-success">
                                            <span>อนุมัติ</span>
                                        </div>
                                    @else
                                        <div class="ribbon-two ribbon-two-blue">
                                            <span>รออนุมัติ</span>
                                        </div>
                                    @endif
                                    @if ($_manager)
                                        <div class="col-sm-12">
                                            <div class="rtv">
                                                <div class="full-underline abs row">
                                                    <div class="col-12">
                                                        <span class="full-dotted mb-3">
                                                            <span class="bg-white">ผู้รับทราบ : </span>
                                                            <span class="text-dark px-3">
                                                                @foreach ($name_mnapp as $name_mnapp)
                                                                    <span
                                                                        class="text-primary px-1">{{ $a > 0 ? '/' : '' }}</span>
                                                                    @php
                                                                        $a++;
                                                                    @endphp
                                                                    {{ $name_mnapp }}
                                                                @endforeach
                                                                @foreach ($_mn_app_date as $dateMN)
                                                                    <small class="text-muted"><i
                                                                            class="mdi mdi-clock-outline"></i>
                                                                        {{ $dateMN }}</small>
                                                                @endforeach
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="col-12">
                                                        <span class="full-dotted mb-3">
                                                            <span class="bg-white">ความคิดเห็น : </span>
                                                            <span class="text-dark px-3">
                                                                @foreach ($_mncomment as $_mncomment)
                                                                    <span
                                                                        class="text-primary px-1">{{ $c > 0 ? '/' : '' }}</span>
                                                                    @php
                                                                        $c++;
                                                                    @endphp
                                                                    {!! $_mncomment !!}
                                                                @endforeach
                                                            </span>

                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @foreach ($result as $mnfile)
                                            @if ($mnfile->image_mn != '')
                                                @php
                                                    $imgmn = json_decode($mnfile->image_mn);
                                                @endphp
                                                <div class="row" id="old_image">
                                                    @foreach ($imgmn as $key => $mnval)
                                                        <div class="col-6 col-lg-2">
                                                            <a href="javascript:void(0)">
                                                                <img src="{{ asset('assets/images/discount/' . $mnval) }}"
                                                                    class="w-100" alt="discount image"
                                                                    title="discount image" data-bs-toggle="modal"
                                                                    data-bs-target="#mnfile{{ $key }}">
                                                                <input type="text" name="old_image[]"
                                                                    value="{{ $mnval }}" hidden>
                                                            </a>
                                                        </div>
                                                        <div class="modal fade" id="mnfile{{ $key }}"
                                                            tabindex="-1" aria-labelledby="exampleModalLabel"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div id="slideMn{{ $key }}"
                                                                            class="carousel slide">
                                                                            <div class="carousel-inner">
                                                                                @foreach ($imgmn as $index => $slideMn)
                                                                                    <div
                                                                                        class="carousel-item {{ $index == $key ? 'active' : '' }}">
                                                                                        <a href="{{ asset('assets/images/discount/' . $slideMn) }}"
                                                                                            target="_blank">
                                                                                            <img src="{{ asset('assets/images/discount/' . $slideMn) }}"
                                                                                                class="d-block w-100"
                                                                                                alt="discount image">
                                                                                        </a>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                            <button class="carousel-control-prev"
                                                                                type="button"
                                                                                data-bs-target="#slideMn{{ $loop->index }}"
                                                                                data-bs-slide="prev">
                                                                                <i
                                                                                    class="fe-chevron-left fs-1 text-muted me-5"></i>

                                                                                <span
                                                                                    class="visually-hidden">Previous</span>
                                                                            </button>
                                                                            <button class="carousel-control-next"
                                                                                type="button"
                                                                                data-bs-target="#slideMn{{ $loop->index }}"
                                                                                data-bs-slide="next">
                                                                                <i
                                                                                    class="fe-chevron-right fs-1 text-muted ms-5"></i>
                                                                                <span class="visually-hidden">Next</span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if ($mnfile->file_mn != '')
                                                <div class="col-lg-3 mt-2" id="old_file">
                                                    <a href="{{ url('sales-document/discount-mistake/productdiscount/file-download/' . $mnfile->file_mn) }}"
                                                        class="text-muted fw-bold">
                                                        <div class="card m-1 shadow-none border">
                                                            <div class="p-2">
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto pe-0">
                                                                        <div class="avatar-sm">
                                                                            <span
                                                                                class="avatar-title bg-soft-primary text-primary rounded">
                                                                                <i class="mdi mdi-folder-zip font-18"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col text-primary">
                                                                        {{ $mnfile->file_mn }} <br>
                                                                        <small
                                                                            class="mb-0 font-10 text-dark">download</small>
                                                                    </div>
                                                                    <input type="text" value="{{ $mnfile->file_mn }} "
                                                                        name="old_file" hidden>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if ($status == 2)
                                        <form class="form-horizontal" method="post" id="managerForm"
                                            enctype="multipart/form-data"
                                            action="{{ route('admin.managerApprove', [$detail->doc_id, $detail->id]) }}">
                                            {{ csrf_field() }}
                                            <input type="text" name="approve" id="approve" value="" hidden>
                                            <div class="col-12">
                                                <div class="fom-group">
                                                    <label class="control-label">ความคิดเห็น</label>
                                                    <textarea class="form-control form-control-md form-control-required" id="comment" name="comment" placeholder="..."
                                                        rows="7" value=""></textarea>
                                                </div>
                                            </div>
                                            <hr>
                                            <h5 class="card-title mt-2 text-primary text-decoration-underline">ส่วนแนบไฟล์
                                            </h5>
                                            <div class="col-12 mt-2">
                                                <label class="control-label">ไฟล์<span class="text-blue">
                                                        (csv,txt,xlx,xls,xlxs,pdf)</span></label>
                                                <div class="fom-group mb-2">
                                                    <input type="file"
                                                        class="form-control form-control-md form-control-required"
                                                        id="file" name="file"
                                                        onchange="$('#old_file').attr('hidden',true);"
                                                        accept=".csv,.txt,.xlx,.xls,.xlxs,.pdf">
                                                </div>
                                            </div>
                                        </form>
                                        <label class="control-label">รูปภาพ<span class="text-blue">
                                                (เฉพาะไฟล์นามสกุล .png/.jpg/.jpeg)</span></label>
                                        <form action="{{ url('sales-document/discount-mistake/uplod-image/request') }}"
                                            method="POST" enctype="multipart/form-data"
                                            onsubmit="return SubmitForm(this);" class="dropzone" id="dropzone"
                                            data-plugin="dropzone">
                                            @csrf
                                            <div class="fallback">
                                                <input id="file" name="file" type="file" accept="image/*"
                                                    multiple />
                                            </div>
                                            <div class="dz-message needsclick">
                                                <i class="h1 text-muted dripicons-cloud-upload"></i>
                                                <h3>วางรูปที่นี่ หรือ คลิกเพื่ออัพโหลด.</h3>
                                                <h4 class="text-danger">**เพิ่มรูปภาพได้ไม่เกิน 5 รูป**</h4>
                                                <span class="text-muted font-13">(เฉพาะไฟล์นามสกุล
                                                    <strong>.png/.jpg/.jpeg</strong>)</span>
                                            </div>
                                        </form>

                                        <div class="mt-2 d-grid gap-2 d-md-flex justify-content-md-start">
                                            <button type="button" id="managerapprove"
                                                onclick="managerApprove(),$('#approve').val('yes')"
                                                class="btn btn-success btn-rounded waves-effect waves-light">
                                                <span class="btn-label"><i class="mdi mdi-check-all"></i></span>อนุมัติ
                                            </button>
                                            <button type="button" id="managerdisapprove"
                                                class="btn btn-danger btn-rounded waves-effect waves-light"
                                                {{-- onclick="managerApprove('{{ $detail->doc_id }}',{{ $detail->id }}, 'no')"> --}} onclick="managerApprove(),$('#approve').val('no')">
                                                <span class="btn-label"><i
                                                        class="mdi mdi-close-circle-outline"></i></span>ไม่อนุมัติ
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer"></div>
                            </div>
                        </div>
                    </div>
                @endif
                @if (($status == 1 && Auth::User()->checkSecretary()) || $status == 9 || $status == 0)
                    <div class="card ribbon-box">
                        <div class="card-body">
                            <div class="card border">
                                <div class="card-header px-5">
                                    ส่วนอนุมัติ (เลขา)
                                </div>
                                <div class="card-body">
                                    @if ($status == 0)
                                        <div class="ribbon-two ribbon-two-danger">
                                            <span>ไม่อนุมัติ</span>
                                        </div>
                                    @elseif($status == 9)
                                        <div class="ribbon-two ribbon-two-success">
                                            <span>อนุมัติ</span>
                                        </div>
                                    @else
                                        <div class="ribbon-two ribbon-two-blue">
                                            <span>รออนุมัติ</span>
                                        </div>
                                    @endif
                                    @if ($_secretary)
                                        <div class="col-sm-12">
                                            <div class="rtv">
                                                <div class="full-underline abs row">
                                                    <div class="col-12">
                                                        <span class="comment-underline mb-3">
                                                            <span class="bg-white">ผู้อนุมัติ : </span>
                                                            <span class="text-dark px-3">
                                                                @foreach ($name_secapp as $name_secapp)
                                                                    <span
                                                                        class="text-primary px-1">{{ $d > 0 ? '/' : '' }}</span>
                                                                    @php
                                                                        $d++;
                                                                    @endphp
                                                                    {!! $name_secapp !!}
                                                                @endforeach
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="col-12">
                                                        <span class="comment-underline mb-3">
                                                            <span class="bg-white">ความคิดเห็น : </span>
                                                            <span class="text-dark px-3">
                                                                @foreach ($_seccomment as $_seccomment)
                                                                    <span
                                                                        class="text-primary px-1">{{ $b > 0 ? '/' : '' }}</span>
                                                                    @php
                                                                        $b++;
                                                                    @endphp
                                                                    {!! $_seccomment !!}
                                                                @endforeach
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (Auth::User()->checkSecretary())
                                        @if ($status == 1 || $status == 9)
                                            <div class="col-12">
                                                <div class="fom-group">
                                                    <label class="control-label">ความคิดเห็น</label>
                                                    <textarea class="form-control form-control-md form-control-required" id="comment" name="comment" placeholder="..."
                                                        rows="7" value=""></textarea>
                                                </div>
                                            </div>
                                            <div class="mt-2 d-grid gap-2 d-md-flex justify-content-md-start">
                                                @if ($status == 9)
                                                    <button type="button"
                                                        class="btn btn-blue btn-rounded waves-effect waves-light"
                                                        onclick="secretaryApprove('{{ $detail->doc_id }}',{{ $detail->id }}, 'more')">
                                                        <span class="btn-label"><i
                                                                class="mdi mdi-check-all"></i></span>บันทึก
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-success btn-rounded waves-effect waves-light"
                                                        onclick="secretaryApprove('{{ $detail->doc_id }}',{{ $detail->id }}, 'yes')">
                                                        <span class="btn-label"><i
                                                                class="mdi mdi-check-all"></i></span>อนุมัติ
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-danger btn-rounded waves-effect waves-light"
                                                        onclick="secretaryApprove('{{ $detail->doc_id }}',{{ $detail->id }}, 'no')">
                                                        <span class="btn-label"><i
                                                                class="mdi mdi-close-circle-outline"></i></span>ไม่อนุมัติ
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <div class="card-footer"></div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-lg-6 mt-3 mb-3"><a onclick="history.back()"
                        class="btn btn-dark btn-rounded waves-effect waves-light">
                        <i class="fe-corner-down-left"></i>กลับ</a>
                </div>
            </div>
        </div>
    </div>
    <div id="printModal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="printModalLabel">พิมพ์ใบแบบฟอร์มขอส่วนลดค่าสินค้า/ค่าซ่อม
                        เนื่องจากความผิดพลาด KACEE</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <embed src="" id='pdf' frameborder="0" width="100%" height="800px">
                </div>
            </div>
        </div>
        <!-- end modal -->
    </div>
    <input type="text" id="chksubmit" hidden>
@endsection
@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>

    <script>
        window.addEventListener('beforeunload', function(event) {
            if ($('#chksubmit').val() == '') {
                $.ajax({
                    type: 'GET',
                    url: "{{ url('/sales-document/discount-mistake/clear/image') }}",
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        console.log('ok');
                    }
                });
            }
            // event.returnValue = 'Are you sure you want to leave?';
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#fileInput').change(function() {
            var files = this.files;
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
                        var inputFiles = $('#fileInput')[0].files;
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

        function managerApprove() {
            Swal.fire({
                title: "ยืนยันการอนุมัติ ใช่ไหม?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ยืนยัน!",
                cancelButtonText: "ยกเลิก",
            }).then((willDelete) => {
                if (willDelete.isConfirmed) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'เรียบร้อย',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    setTimeout(() => {
                        $('#chksubmit').val('submit');
                        $("#managerForm").submit(); // Submit the form
                    }, 2000);
                }
            });
        }

        Dropzone.options.dropzone = {
            maxFilesize: 12,
            parallelUploads: 1,
            maxFiles: 5,
            maxfilesexceeded: function(file) {
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาตรวจสอบรูปภาพ',
                    text: 'เพิ่มรูปภาพได้ไม่เกิน 5 รูป',
                })
                this.removeFile(file);
            },
            renameFile: function(file) {
                var dt = new Date();
                var time = dt.getTime();
                var im_name = time + file.name;
                return im_name;
            },
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            addRemoveLinks: true,
            removedfile: function(file, im_name) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('removefile') }}",
                    data: {
                        name: file.upload.filename,
                    },
                });
                var _ref;
                return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) :
                    void 0;
            },
            timeout: 5000,
            // success: function(file, response) {
            //     console.log(response);
            // },
            error: function(file, response) {
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาตรวจสอบไฟล์รูปภาพ',
                    text: 'ขนาดไฟล์ใหญ่เกินไป',
                })
                this.removeFile(file);
            }
        }
        // clear all dropzone
        // Dropzone.forElement("#dropzone").removeAllFiles(true);


        function secretaryApprove(doc_id, id, approve) {
            if (approve == "yes") {
                confirmMsg = "ยืนยันการอนุมัติ";
            } else if (approve == "no") {
                confirmMsg = "ไม่ต้องการอนุมัติ";
            } else {
                confirmMsg = "ยืนยันเพิ่มความคิดเห็น";
            }
            Swal.fire({
                icon: "warning",
                title: confirmMsg + " ใช่ไหม?",
                showCancelButton: true,
                confirmButtonColor: "#00bc9d",
                cancelButtonColor: "#d33",
                confirmButtonText: "ยืนยัน!",
                cancelButtonText: "ยกเลิก",
                showLoaderOnConfirm: true,
                stopKeydownPropagation: false,
                preConfirm: () => {
                    var comment = $("#comment").val();
                    return fetch(
                            '/sales-document/discount-mistake/approve/product-discount-repair/secretary-approve', {
                                method: 'POST',
                                headers: {
                                    'Content-type': 'application/json; charset=UTF-8',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({
                                    doc_id: doc_id,
                                    id: id,
                                    approve: approve,
                                    comment: comment,
                                }),
                            })
                        .then(function(response) {
                            if (!response.ok) {
                                throw new Error(response.statusText);
                            }
                            return response.json();
                        })
                        .then(function(data) {
                            if (data.success === false) {
                                Swal.fire({
                                    icon: "warning",
                                    title: data.message,
                                });
                                return false;
                            }
                        })
                        .catch((error) => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading(),
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: "success",
                        title: "เรียบร้อย!",
                    });
                    setTimeout(() => {
                        location.href =
                            "{{ url('/sales-document/discount-mistake/secretary-approve/product-discount-repair') }}";
                    }, 2000);
                }
            });
        }

        $("#btnPrint").click(function() {
            var objData = $(this).val();
            $("#pdf").attr("src", "{{ url('sales-document/discount-mistake/productdiscount/print') }}/" + objData
                .toString());

            $.ajax({
                type: 'POST',
                url: "{{ route('discount.log.print') }}",
                data: {
                    doc_id: $(this).val(),
                },
                // success: function(response) {
                //     console.log(response);
                // },
            });
        });
    </script>
@endsection
