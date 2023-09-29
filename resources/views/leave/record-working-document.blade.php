@extends('layouts.master-layout', ['page_title' => "ใบบันทึกวันทำงาน"])
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Leave</a></li>
                        <li class="breadcrumb-item active">ใบบันทึกวันทำงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">ใบบันทึกวันทำงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    @inject('thaiDateHelper', '\App\Services\ThaiDateHelperService')
    <div class="row">
        <div class="col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="header-title mb-3">สถานะใบบันทึกวันทำงาน</h4>

                    <div class="track-order-list">
                        <ul class="list-unstyled">
                            @php
                                $pending = "";
                                $pendingActive = "";
                                $approved1 = "";
                                $approved1Active = "";
                                $approved2 = "";
                                $approved2Active = "";
                                $completed = "";
                                $completedActive = "";
                                $cancel1 = "";
                                $cancel1Active = "";
                                $cancel2 = "";
                                $cancel2Active = "";
                                $cancel3 = "";
                                $cancel3Active = "";
                                if ($leave->approve_status == "P") {
                                    $pending = "";
                                    $pendingActive = '<span class="active-dot dot"></span>';
                                } else if ($leave->approve_status == "A1") {
                                    $pending = "completed";
                                    $approved1Active = '<span class="active-dot dot"></span>';
                                } else if ($leave->approve_status == "A2") {
                                    $pending = "completed";
                                    $approved1 = "completed";
                                    $approved2Active = '<span class="active-dot dot"></span>';
                                } else if ($leave->approve_status == "S") {
                                    $pending = "completed";
                                    $approved1 = "completed";
                                    $approved2 = "completed";
                                    $completedActive = '<span class="active-dot dot"></span>';
                                } else if ($leave->approve_status == "C1") {
                                    $pending = "completed";
                                    $approved1 = "completed";
                                    $approved2 = "completed";
                                    $completed = "completed";
                                    $cancel1Active = '<span class="active-dot dot"></span>';
                                } else if ($leave->approve_status == "C2") {
                                    $pending = "completed";
                                    $approved1 = "completed";
                                    $approved2 = "completed";
                                    $completed = "completed";
                                    $cancel1 = "completed";
                                    $cancel2Active = '<span class="active-dot dot"></span>';
                                } else if ($leave->approve_status == "C3") {
                                    $pending = "completed";
                                    $approved1 = "completed";
                                    $approved2 = "completed";
                                    $completed = "completed";
                                    $cancel1 = "completed";
                                    $cancel2 = "completed";
                                    $cancel3Active = '<span class="active-dot dot"></span>';
                                }
                            @endphp
                            <li class="{{ $pending }}">
                                {!! $pendingActive !!}
                                <h5 class="mt-0 mb-1">รออนุมัติ</h5>
                                <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($leave->created_at) }} <small class="text-muted">{{ \Carbon\Carbon::parse($leave->created_at)->format('H:i') . ' น.' }}</small> </p>
                            </li>
                            <li class="{{ $approved1 }}">
                                {!! $approved1Active !!}
                                <h5 class="mt-0 mb-1">อนุมัติโดยหัวหน้า</h5>
                                @if ($leave->approve_ldate != "")
                                <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($leave->approve_ldate) }} <small class="text-muted">{{ \Carbon\Carbon::parse($leave->approve_ldate)->format('H:i') . ' น.' }}</small> </p>
                                @else
                                <p class="text-muted">&nbsp;</p>
                                @endif
                            </li>
                            <li class="{{ $approved2 }}">
                                {!! $approved2Active !!}
                                <h5 class="mt-0 mb-1">อนุมัติโดยผู้จัดการ</h5>
                                @if ($leave->approve_mdate != "")
                                <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($leave->approve_mdate) }} <small class="text-muted">{{ \Carbon\Carbon::parse($leave->approve_mdate)->format('H:i') . ' น.' }}</small> </p>
                                @else
                                <p class="text-muted">&nbsp;</p>
                                @endif
                            </li>
                            <li class="{{ $completed }}">
                                {!! $completedActive !!}
                                <h5 class="mt-0 mb-1">อนุมัติโดยบุคคล</h5>
                                @if ($leave->approve_hrdate != "")
                                <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($leave->approve_hrdate) }} <small class="text-muted">{{ \Carbon\Carbon::parse($leave->approve_hrdate)->format('H:i') . ' น.' }}</small> </p>
                                @else
                                <p class="text-muted">&nbsp;</p>
                                @endif
                            </li>
                            @if ($leave->approve_status == "C1")
                            <li class="{{ $cancel1 }}">
                                {!! $cancel1Active !!}
                                <h5 class="mt-0 mb-1 text-danger">ยกเลิกโดยหัวหน้า</h5>
                                @if ($leave->approve_ldate != "")
                                <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($leave->approve_ldate) }} <small class="text-muted">{{ \Carbon\Carbon::parse($leave->approve_ldate)->format('H:i') . ' น.' }}</small> </p>
                                @else
                                <p class="text-muted">&nbsp;</p>
                                @endif
                                <span class="text-muted">{{ $leave->cancel_remark }}</span>
                            </li>
                            @elseif ($leave->approve_status == "C2")
                            <li class="{{ $cancel2 }}">
                                {!! $cancel2Active !!}
                                <h5 class="mt-0 mb-1 text-danger">ยกเลิกโดยผู้จัดการ</h5>
                                @if ($leave->approve_mdate != "")
                                <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($leave->approve_mdate) }} <small class="text-muted">{{ \Carbon\Carbon::parse($leave->approve_mdate)->format('H:i') . ' น.' }}</small> </p>
                                @else
                                <p class="text-muted">&nbsp;</p>
                                @endif
                                <span class="text-muted">{{ $leave->cancel_remark }}</span>
                            </li>
                            @elseif ($leave->approve_status == "C3")
                            <li class="{{ $cancel3 }}">
                                {!! $cancel3Active !!}
                                <h5 class="mt-0 mb-1 text-danger">ยกเลิกโดยบุคคล</h5>
                                @if ($leave->approve_hrdate != "")
                                <p class="text-muted">{{ $thaiDateHelper->shortDateFormat($leave->approve_hrdate) }} <small class="text-muted">{{ \Carbon\Carbon::parse($leave->approve_hrdate)->format('H:i') . ' น.' }}</small> </p>
                                @else
                                <p class="text-muted">&nbsp;</p>
                                @endif
                                <span class="text-muted">{{ $leave->cancel_remark }}</span>
                            </li>
                            @endif
                        </ul>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-lg-9 mb-3">
            <div class="card ribbon-custom-box h-100">
                <div class="card-body">
                    <div class="ribbon-custom ribbon-custom-{{ $status["color"] }} ribbon-custom-top-right text-{{ $status["text"] }}"><span>{{ $status["name"] }}</span></div>
                    <div class="row">
                        <div class="col-auto">
                            <div class="d-flex align-items-center">
                                <img src="{{asset('assets/images/logo-kacee.png')}}" alt="logo" width="60" height="60">
                                <div class="mx-2 py-auto">
                                    <span>บริษัท อี .แอนด์. วี จำกัด</span><br>
                                    <span>259 ถนนเลียบคลองภาษีเจริญฝั่งใต้ แขวงหนองแขม เขตหนองแขม กรุงเทพฯ 10160</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <h5 class="text-center text-decoration-underline text-dark fw-bold">ใบบันทึกวันทำงาน</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <p class="text-end">วันที่ <span class="text-decoration-dotted text-dark">{{ \Carbon\Carbon::parse($leave->created_at)->format('d') }}</span> เดือน <span class="text-decoration-dotted text-dark">{{ \Carbon\Carbon::parse($leave->created_at)->locale('th_TH')->isoFormat('MMMM') }}</span> พ.ศ. <span class="text-decoration-dotted text-dark">{{ (\Carbon\Carbon::parse($leave->created_at)->format('Y') + 543) }}</span></p>
                        </div>
                    </div>
                    <div class="row pb-4">
                        <div class="col-sm-12">
                            <div class="rtv">
                                <div class="full-underline abs">
                                    <span class="full-dotted mb-2">
                                        @if ($emp->title == "นาย")
                                        <span class="bg-white">ข้าพเจ้า (นาย,<span class="text-decoration-line-through">นาง</span>,<span class="text-decoration-line-through">นางสาว</span>)</span>
                                        @elseif ($emp->title == "นาง")
                                        <span class="bg-white">ข้าพเจ้า (<span class="text-decoration-line-through">นาย</span>,นาง,<span class="text-decoration-line-through">นางสาว</span>)</span>
                                        @elseif ($emp->title == "นางสาว")
                                        <span class="bg-white">ข้าพเจ้า (<span class="text-decoration-line-through">นาย</span>,<span class="text-decoration-line-through">นาง</span>,นางสาว)</span>
                                        @else
                                        <span class="bg-white">ข้าพเจ้า (นาย,นาง,นางสาว)</span>
                                        @endif
                                        <span class="text-dark px-3">{{ $emp->name . ' ' . $emp->surname }}</span>
                                    </span>
                                    <span class="full-dotted mb-2">
                                        <span class="bg-white">ส่วน</span><span class="text-dark @if ($dept_arr["level1"]["name"]!='') px-3 @else px-5 @endif">{{ $dept_arr["level1"]["name"] }}</span>
                                        <span class="bg-white">ฝ่าย</span><span class="text-dark @if ($dept_arr["level2"]["name"]!='') px-3 @else px-5 @endif">{{ $dept_arr["level2"]["name"] }}</span>
                                        <span class="bg-white">แผนก</span><span class="text-dark @if ($dept_arr["level3"]["name"]!='') px-3 @else px-5 @endif">{{ $dept_arr["level3"]["name"] }}</span>
                                        <span class="bg-white">หน่วยงาน</span><span class="text-dark @if ($dept_arr["level4"]["name"]!='') px-3 @else px-5 @endif">{{ $dept_arr["level4"]["name"] }}</span>
                                    </span>
                                    <span class="full-dotted mb-2">
                                        <span class="bg-white">ตำแหน่ง</span><span class="text-dark @if ($emp->position_name!='') px-3 @else px-5 @endif">{{ $emp->position_name }}</span>
                                        <span class="bg-white">ระยะเวลาทำงาน</span><span class="text-dark px-3">{{ $worked_days["worked_text"] }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-sm-2">มีความประสงค์ขอ</div>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col-sm-3">
                                    <img src="{{asset('assets/images/checkbox-mark.png')}}" height="18">
                                    <span>แจ้งบันทึกวันทำงาน</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 pb-4">
                        <div class="col-sm-12">
                            <span>วันที่ลา</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($leave->work_date)->format('d') }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <span>เดือน</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($leave->work_date)->locale('th_TH')->isoFormat('MMMM') }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <span>พ.ศ.</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ (\Carbon\Carbon::parse($leave->work_date)->format('Y') + 543) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <br><br><span>เหตุผลที่บันทึกวันทำงาน</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ $leave->remark }}</span>&nbsp;&nbsp;&nbsp;</span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        @if (count($leaveLeader))
                        <div class="col-sm-12 col-md-6">
                            <div class="float-start">
                                <span>ลงชื่อ</span><span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-dark">@if (count($leaveLeader)) {{ $leaveLeader['name'] }} @endif</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span>ผู้บันทึกแทน</span>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="float-end">
                                <span>ลงชื่อ</span><span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-dark">@if (count($leaveEmp)) {{ $leaveEmp['name'] }} @endif</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span>ผู้บันทึก</span>
                            </div>
                        </div>
                        @else
                        <div class="col-sm-12">
                            <div class="float-end">
                                <span>ลงชื่อ</span><span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-dark">@if (count($leaveEmp)) {{ $leaveEmp['name'] }} @endif</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span>ผู้บันทึก</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-4 text-center">
                            <div class="rtv">
                                <div class="full-underline abs px-3">
                                    <span class="full-dotted">@if (count($approvedLeader)) {{ $approvedLeader['name'] }} / {{ $approvedLeader['date'] }} @endif</span>
                                </div>
                            </div>
                            <br><p class="fw-bold">หัวหน้า / ว.ด.ป.</p>
                        </div>
                        <div class="col-sm-4 text-center">
                            <div class="rtv">
                                <div class="full-underline abs px-3">
                                    <span class="full-dotted">@if (count($approvedManager)) {{ $approvedManager['name'] }} / {{ $approvedManager['date'] }} @endif</span>
                                </div>
                            </div>
                            <br><p class="fw-bold">ผู้จัดการ / ว.ด.ป.</p>
                        </div>
                        <div class="col-sm-4 text-center">
                            <div class="rtv">
                                <div class="full-underline abs px-3">
                                    <span class="full-dotted">@if (count($approvedHR)) {{ $approvedHR['name'] }} / {{ $approvedHR['date'] }} @endif</span>
                                </div>
                            </div>
                            <br><p class="fw-bold">ฝ่ายบุคคล / ว.ด.ป.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="clearfix pt-3">
                                <h6 class="text-muted text-decoration-underline fw-bold">หมายเหตุ</h6>
                                <small class="text-muted">
                                    1. การขาดงานของลูกจ้างจะถูกหักเงินเดือนตามอัตราค่าแรงรายวัน และ จะตัดเบี้ยขยันและจะไม่ได้รับโบนัสในช่วงเวลา 6 เดือน<br>
                                    2. การลาป่วยต้องมีใบรับรองแพทย์มายืนยันเสมอ เว้นแต่จะมีเหตุอันสมควรที่แจ้งให้บริษัททราบ มิเช่นนั้นจะถือว่าลูกจ้างขาดงาน<br>
                                    3. การลากิจลาได้ไม่เกิน 6 วัน ในระยะเวลา 1 ปียกเว้นพนักงานที่ทำงานยังไม่ครบ 180 วันและต้องลาล่วงหน้าอย่างน้อย 1 วันโดยจะถูกหักค่าแรงในวันนั้น
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 mb-1">
                        <div class="text-end d-print-none">
                            <button type="button" class="btn btn-secondary waves-effect waves-light me-2" onclick="history.back();"><i class="mdi mdi-keyboard-backspace me-1"></i> ย้อนกลับ</button>
                            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#printLeaveModal"><i class="mdi mdi-printer me-1"></i> พิมพ์</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3"></div>
        <div class="col-lg-9">
            <div class="border-0 border-bottom text-center">Action</div>
            <div class="border border-light p-2 mb-3">
                @foreach ($recordWorkingLog as $list)
                @php
                    $sname = '';
                    if ($list->surname != "") {$sname = ' '.$list->surname;}
                    $nname = '';
                    if ($list->nickname != "") {$nname = ' ('.$list->nickname.')';}
                    $log_username = $list->name . $sname . $nname;
                @endphp
                <div class="post-user-comment-box bg-white rounded my-1">
                    <div class="d-flex align-items-start">
                        <img class="me-2 avatar-sm rounded-circle" src="{{ url('assets/images/users/thumbnail/'.$list->image) }}" onerror="this.onerror=null;this.src='{{ url('assets/images/users/thumbnail/user-1.jpg') }}'" alt="image">
                        <div class="w-100">
                            <h5 class="mt-0">{{ $log_username }} <small class="text-muted">{{ \Carbon\Carbon::parse($list->updated_at)->locale('th_TH')->diffForHumans() }}</small></h5>
                            <i class="mdi mdi-share-outline me-1"></i>{{ $list->description }}<small class="text-muted ms-2">{{ \Carbon\Carbon::parse($list->updated_at)->thaidate('D j M Y, เวลา H:i น.') }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div id="printLeaveModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="printLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="printLeaveModalLabel">พิมพ์ใบบันทึกวันทำงาน</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <embed src="{{ url('leave/document-record-working/pdf', $leave->id) }}" frameborder="0" width="100%" height="600px">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<!-- third party js ends -->
@endsection