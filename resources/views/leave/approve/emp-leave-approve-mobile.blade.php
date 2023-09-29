@extends('layouts.master-layout', ['page_title' => "อนุมัติใบลางาน"])
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
                        <li class="breadcrumb-item active">อนุมัติใบลางาน</li>
                    </ol>
                </div>
                <h4 class="page-title">อนุมัติใบลางาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    @inject('thaiDateHelper', '\App\Services\ThaiDateHelperService')
    <div class="row">
        <div class="col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="header-title mb-3">สถานะใบลางาน</h4>

                    @if ($periodSalary)
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-4">
                                <h5 class="mt-0">งวดค่าแรง:</h5>
                                <p>{{ $thaiDateHelper->shortDateFormat($periodSalary->start) }} ถึง {{ $thaiDateHelper->shortDateFormat($periodSalary->end) }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

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
                                if ($leave->leave_status == "P") {
                                    $pending = "";
                                    $pendingActive = '<span class="active-dot dot"></span>';
                                } else if ($leave->leave_status == "A1") {
                                    $pending = "completed";
                                    $approved1Active = '<span class="active-dot dot"></span>';
                                } else if ($leave->leave_status == "A2") {
                                    $pending = "completed";
                                    $approved1 = "completed";
                                    $approved2Active = '<span class="active-dot dot"></span>';
                                } else if ($leave->leave_status == "S") {
                                    $pending = "completed";
                                    $approved1 = "completed";
                                    $approved2 = "completed";
                                    $completedActive = '<span class="active-dot dot"></span>';
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
                        </ul>

                        @if ($leave->leave_attach != "")
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <h5 class="mt-3">ไฟล์แนบ:</h5>
                                    <a href="{{ url('/leave/attach/'.$leave->leave_attach) }}" target="_blank" class="text-primary"><i class="mdi mdi-attachment me-1"></i> {{ $leave->leave_attach }}</a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="text-center mt-4">
                            <a href="#detail" class="btn btn-primary">ข้อมูลการลางาน</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-lg-9 mb-3">
            <div class="card h-100" id="detail">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="d-flex align-items-center">
                                <img src="{{asset('assets/images/logo-kacee.png')}}" alt="logo" width="60" height="70">
                                <div class="mx-2 py-auto">
                                    <span>บริษัท อี .แอนด์. วี จำกัด</span><br>
                                    <span>259 ถนนเลียบคลองภาษีเจริญฝั่งใต้ แขวงหนองแขม เขตหนองแขม กรุงเทพฯ 10160</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <h5 class="text-center text-decoration-underline text-dark fw-bold">ใบลา</h5>
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
                                        <span class="text-dark px-1">{{ $emp->name . ' ' . $emp->surname }}</span>
                                    </span>
                                    <span class="full-dotted mb-2">
                                        <span class="bg-white">ส่วน</span><span class="text-dark px-1">{{ $dept_arr["level1"]["name"] }}</span>
                                    </span>
                                    <span class="full-dotted mb-2">
                                        <span class="bg-white">ฝ่าย</span><span class="text-dark px-1">{{ $dept_arr["level2"]["name"] }}</span>
                                    </span>
                                    <span class="full-dotted mb-2">
                                        <span class="bg-white">แผนก</span><span class="text-dark px-1">{{ $dept_arr["level3"]["name"] }}</span>
                                    </span>
                                    <span class="full-dotted mb-2">
                                        <span class="bg-white">หน่วยงาน</span><span class="text-dark px-1">{{ $dept_arr["level4"]["name"] }}</span>
                                    </span>
                                    <span class="full-dotted mb-2">
                                        <span class="bg-white">ตำแหน่ง</span><span class="text-dark px-1">{{ $emp->position_name }}</span>
                                    </span>
                                    <span class="full-dotted mb-2">
                                        <span class="bg-white">ระยะเวลาทำงาน</span><span class="text-dark px-1">{{ $worked_days["worked_text"] }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-2" style="margin-top: 150px;">
                        <div class="col-sm-2">มีความประสงค์ขอ</div>
                        <div class="col-sm-10">
                            <div class="row">
                            @foreach ($leaveType as $list)
                                <div class="col-sm-4">
                                    @if ($leave->leave_type_id == $list->leave_type_id)
                                        <img src="{{asset('assets/images/checkbox-mark.png')}}" height="18">
                                    @else
                                        <img src="{{asset('assets/images/checkbox.png')}}" height="18">
                                    @endif
                                    <span>{{ $list->leave_type_name }}</span></div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 pb-4">
                        <div class="col-sm-12">
                            @if ($leave->leave_range == "full" || $leave->leave_range == "etc")
                            <span>วันที่</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($leave->leave_start_date)->format('d') }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <span>เดือน</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($leave->leave_start_date)->locale('th_TH')->isoFormat('MMMM') }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <span>พ.ศ.</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ (\Carbon\Carbon::parse($leave->leave_start_date)->format('Y') + 543) }}</span>&nbsp;&nbsp;&nbsp;</span>
                            @if ($leave->leave_range == "etc")
                            <br><span>เวลา</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($leave->leave_start_time)->format('H:i') }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <span>ถึง เวลา</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($leave->leave_end_time)->format('H:i') }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <br><span>รวม</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">@if($leaveMinutes['h']>0) {{ $leaveMinutes['h'].' ชม. ' }} @endif @if($leaveMinutes['m']>0) {{ $leaveMinutes['m'].' น.' }} @endif</span>&nbsp;&nbsp;&nbsp;</span>
                            @endif
                            @if ($rwRef)
                            <br><span>แทนวันที่</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($rwRef->work_date)->format('d') }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <span>เดือน</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($rwRef->work_date)->locale('th_TH')->isoFormat('MMMM') }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <span>พ.ศ.</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ (\Carbon\Carbon::parse($rwRef->work_date)->format('Y') + 543) }}</span>&nbsp;&nbsp;&nbsp;</span>
                            @endif
                            <br><br><span>เหตุผลที่ขอหยุดงาน</span>
                            <br><span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ $leave->leave_reason }}</span>&nbsp;&nbsp;&nbsp;</span>
                            @else
                            <span>ตั้งแต่วันที่</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($leave->leave_start_date)->format('d') }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <span>เดือน</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($leave->leave_start_date)->locale('th_TH')->isoFormat('MMMM') }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <span>พ.ศ.</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ (\Carbon\Carbon::parse($leave->leave_start_date)->format('Y') + 543) }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <br><span class="bg-white">ถึง วันที่</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($leave->leave_end_date)->format('d') }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <span>เดือน</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($leave->leave_end_date)->locale('th_TH')->isoFormat('MMMM') }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <span>พ.ศ.</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ (\Carbon\Carbon::parse($leave->leave_end_date)->format('Y') + 543) }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <br><span>รวม</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ $leave->leave_day }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <span>วัน</span>
                            @if ($rwRef)
                            <br><span>แทนวันที่</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($rwRef->work_date)->format('d') }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <span>เดือน</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ \Carbon\Carbon::parse($rwRef->work_date)->locale('th_TH')->isoFormat('MMMM') }}</span>&nbsp;&nbsp;&nbsp;</span>
                            <span>พ.ศ.</span>
                            <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;<span class="text-dark">{{ (\Carbon\Carbon::parse($rwRef->work_date)->format('Y') + 543) }}</span>&nbsp;&nbsp;&nbsp;</span>
                            @endif
                            <div class="rtv mt-2">
                                <div class="full-underline abs">
                                    <span class="full-dotted mb-2"><span class="bg-white">เหตุผลที่ขอหยุดงาน</span><span class="text-dark px-3">{{ $leave->leave_reason }}</span></span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @if (count($leaveLeader))
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <div class="text-center">
                                <span>ลงชื่อ</span><span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-dark">@if (count($leaveLeader)) {{ $leaveLeader['name'] }} @endif</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span>ผู้บันทึก</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <div class="text-center">
                                <span>ลงชื่อ</span><span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-dark">@if (count($leaveEmp)) {{ $leaveEmp['name'] }} @endif</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span>ผู้ลา</span>
                            </div>
                        </div>
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
                    <div class="mt-4">
                        <a href="{{ url('leave/approve/dashboard') }}" class="btn btn-secondary me-1"><i class="mdi mdi-keyboard-backspace me-1"></i> ย้อนกลับ</a>
                        <div class="float-end">
                            {{-- <a href="{{ url('leave/approve/emp-leave-edit', $leave->leave_id) }}" class="btn btn-warning me-1"><i class="mdi mdi-lead-pencil me-1"></i> แก้ไขใบลา</a> --}}
                            <button type="button" class="btn btn-primary waves-effect waves-light me-2" onclick="approveConfirmation('{{ $leave->leave_id }}');"><i class="mdi mdi-check-bold me-1"></i> อนุมัติ</button>
                            <button type="button" class="btn btn-danger waves-effect waves-light" onclick="cancelConfirmation('{{ $leave->leave_id }}');"><i class="mdi mdi-close-circle-outline me-1"></i> ไม่อนุมัติ</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="border-0 border-bottom text-center">Action</div>
            <div class="border border-light p-2 mb-3">
                @foreach ($leaveLog as $list)
                <div class="post-user-comment-box bg-white rounded my-1">
                    <div class="d-flex align-items-start">
                        <img class="me-2 avatar-sm rounded-circle" src="{{ url('assets/images/users/thumbnail/'.$list->image) }}" onerror="this.onerror=null;this.src='{{ url('assets/images/users/thumbnail/user-1.jpg') }}'" alt="image">
                        <div class="w-100">
                            <h5 class="mt-0">{{ $list->name . ' ' . $list->surname }} <small class="text-muted">{{ \Carbon\Carbon::parse($list->updated_at)->locale('th_TH')->diffForHumans() }}</small></h5>
                            <i class="mdi mdi-share-outline me-1"></i>{{ $list->description }}<small class="text-muted ms-2">{{ \Carbon\Carbon::parse($list->updated_at)->thaidate('D j M Y, เวลา H:i น.') }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    function approveConfirmation(id) {
        Swal.fire({
            icon: "warning",
            title: "คุณต้องการอนุมัติใบลางาน ใช่ไหม?",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการอนุมัติ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch('/leave/approve/emp-leave-approved', {
                        method: 'POST',
                        headers: {
                            'Content-type': 'application/json; charset=UTF-8',
                            'X-CSRF-TOKEN': '{{csrf_token()}}',
                        },
                        body: JSON.stringify({'leave_id': id}),
                    })
                    .then(function(response){
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .then(function(data){
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
                    title: "อนุมัติใบลางานเรียบร้อย!",
                });
                setTimeout(() => {
                    location.href = "{{ url('leave/approve/dashboard') }}";
                }, 2000);
            }
        });
    }
    function cancelConfirmation(id) {
        Swal.fire({
            icon: "warning",
            title: "คุณต้องการยกเลิกใบลางาน ใช่ไหม?",
            html: '<p>โปรดใส่เหตุผล</p><input type="text" class="form-control" id="cancel_remark" name="cancel_remark" required>',
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                var cancel_remark = $("#cancel_remark").val();
                if (cancel_remark == "") {
                    Swal.showValidationMessage(`โปรดใส่เหตุผล`);
                    return false;
                }
                return fetch('/leave/approve/emp-leave-cancel', {
                        method: 'POST',
                        headers: {
                            'Content-type': 'application/json; charset=UTF-8',
                            'X-CSRF-TOKEN': '{{csrf_token()}}',
                        },
                        body: JSON.stringify({
                            leave_id: id,
                            cancel_remark: cancel_remark,
                        }),
                    })
                    .then(function(response){
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .then(function(data){
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
                    location.href = "{{ url('leave/approve/dashboard') }}";
                }, 2000);
            }
        });
    }
</script>
@endsection