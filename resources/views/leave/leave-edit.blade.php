@extends('layouts.master-layout', ['page_title' => 'แก้ไขใบลา'])
@section('css')
    <link href="{{ asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/clockpicker/clockpicker.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        input[type=file]::file-selector-button {
            background: #e4e0fa !important;
            color: #7536f9 !important;
            border-style: none !important;
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
                            <li class="breadcrumb-item active">แก้ไขใบลา</li>
                        </ol>
                    </div>
                    <h4 class="page-title">แก้ไขใบลา</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-box">
                            <form action="{{ route('leave.update') }}" class="wow fadeInLeft" method="POST"
                            enctype="multipart/form-data" onsubmit="return SubmitForm(this);">
                            {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-5">
                                        <h4 class="text-info">แก้ไขใบลา</h4>
                                    </div>
                                    <div class="col-7">
                                        <h4 class="text-primary float-end">วันที่: {{ thaidate('d/m/Y') }}</h4>
                                    </div>
                                </div>
                                <div class="p-lg-3">
                                    <div class="row mb-1">
                                        <div class="d-flex align-items-start mb-2 mt-1">
                                            <img class="d-flex me-2 rounded-circle" src="{{url('assets/images/users/thumbnail/'.$user->image)}}" onerror="this.onerror=null;this.src='{{url('assets/images/users/thumbnail/user-1.jpg')}}';" alt="placeholder image" width="32" height="32">
                                            <div class="w-100">
                                                <h6 class="m-0 font-14">{{ $user->name }} {{ $user->surname }} ({{ $user->emp_id }})</h6>
                                                @if ($dept_level)
                                                    @foreach ($dept_level as $list)
                                                        @if ($list->level == 0 && $list->detail->dept_id != "")
                                                        <small class="text-muted">บริษัท: {{ $list->detail->dept_name }}</small>&nbsp;
                                                        @endif
                                                        @if ($list->level == 1 && $list->detail->dept_id != "")
                                                        <small class="text-muted">ส่วน: {{ $list->detail->dept_name }}</small>&nbsp;
                                                        @endif
                                                        @if ($list->level == 2 && $list->detail->dept_id != "")
                                                        <small class="text-muted">ฝ่าย: {{ $list->detail->dept_name }}</small>&nbsp;
                                                        @endif
                                                        @if ($list->level == 3 && $list->detail->dept_id != "")
                                                        <small class="text-muted">แผนก: {{ $list->detail->dept_name }}</small>&nbsp;
                                                        @endif
                                                        @if ($list->level == 4 && $list->detail->dept_id != "")
                                                        <small class="text-muted">หน่วยงาน: {{ $list->detail->dept_name }}</small>&nbsp;
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <small class="text-muted">ตำแหน่ง: @if ($user->position_id!=0) {{ $user->position_name }} @else - @endif</small>&nbsp;
                                                <h6 class="float-end font-13">ระยะเวลาทำงาน: {{ $work_date }}</h6>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 text-primary">
                                            งวดค่าแรงปัจจุบัน: {{ ($period->year + 543) . "/" . str_pad($period->month, 2, "0", STR_PAD_LEFT) . ", " . substr($period->start, -2) . " - " . substr($period->end, -2) }}
                                        </div>
                                    </div>
                                    <div class="alert alert-warning" id="leave_type_detail" role="alert">{{ $leave_type_detail }}</div>
                                    <div class="mb-3" id="formfile" @if ($leave->leave_type_id!=2) hidden @endif>
                                        <label for="attach_file" class="form-label">แนบใบรับรองแพทย์ (image/*,.pdf,.doc,.docx)</label>
                                        <input class="form-control" type="file" id="attach_file" name="attach_file" accept="image/*,.pdf,.doc,.docx">
                                        <input class="form-control" type="hidden" id="attach_file_old" name="attach_file_old" value="{{ $leave->leave_attach }}">
                                        @if ($leave->leave_attach != "")
                                        <div id="link_attach_file_old">
                                            <a href="{{ url('/leave/attach/'.$leave->leave_attach) }}" target="_blank" class="text-primary @if($leave->leave_attach=="") d-none @endif"><span class="badge badge-soft-primary rounded-pill fs-6 mt-1"><i class="mdi mdi-attachment me-1"></i> {{ $leave->leave_attach }}</span></a><span class="badge badge-soft-danger ms-1" role="button" title="ลบไฟล์" onclick="$('#attach_file_old').val('');$('#link_attach_file_old').html('');"><i class="mdi mdi-trash-can"></i></span>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5 col-12">
                                            <select class="form-select mb-3" id="leave_type" name="leave_type" required>
                                                <option value="" selected>เลือกประเภทการลา</option>
                                                @foreach ($leave_type as $list)
                                                    @if ($list->leave_type_id == "9") // ลาคลอด
                                                        @if ($user->gender == "F")
                                                        <option value="{{ $list->leave_type_id }}" title="{{ $list->leave_type_detail }}"
                                                            @if ($list->leave_type_id==$leave->leave_type_id) selected @endif>{{ $list->leave_type_name }}</option>
                                                        @endif
                                                    @elseif ($list->leave_type_id == "8" || $list->leave_type_id == "10") // ลาบวช, ทหาร
                                                        @if ($user->gender == "M")
                                                        <option value="{{ $list->leave_type_id }}" title="{{ $list->leave_type_detail }}"
                                                            @if ($list->leave_type_id==$leave->leave_type_id) selected @endif>{{ $list->leave_type_name }}</option>
                                                        @endif
                                                    @else
                                                        <option value="{{ $list->leave_type_id }}" title="{{ $list->leave_type_detail }}"
                                                            @if ($list->leave_type_id==$leave->leave_type_id) selected @endif>{{ $list->leave_type_name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-7 col-12">
                                            <div class="input-group mb-3">
                                                @if((new \Jenssegers\Agent\Agent())->isDesktop() || (new \Jenssegers\Agent\Agent())->isTablet())
                                                <span class="input-group-text text-info bg-soft-info border-info">เหตุผลที่ขอหยุดงาน</span>
                                                @endif
                                                <input type="text" class="form-control" id="leave_reason" name="leave_reason" value="{{ $leave->leave_reason }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div id="record_working_detail_use">
                                        @if ($record_working)
                                        <div class="alert alert-info" role="alert">
                                            <h5 class="text-decoration-underline">วันหยุดที่ใช้งานอยู่กับใบลานี้</h5>
                                            <span>{{ \Carbon\Carbon::parse($record_working->work_date)->locale('th_TH')->isoFormat('dddd , LL') }}</span><br>
                                        </div>
                                        @endif
                                    </div>
                                    <div id="record_working_detail">
                                        <div class="alert alert-pink" role="alert">
                                            <h5 class="text-decoration-underline">วันหยุดที่สามารถใช้งานได้</h5>
                                            @php
                                                $exp_record_working = explode(",", $record_working_arr, -1);
                                            @endphp
                                            @for ($i=0; $i<count($exp_record_working); $i++)
                                            <span>{{ \Carbon\Carbon::parse($exp_record_working[$i])->locale('th_TH')->isoFormat('dddd , LL') }}</span><br>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="row-date">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="form-check form-check-inline range_full">
                                                <input class="form-check-input" type="radio" name="leave_range"
                                                    id="full" value="full" checked @if ($leave->leave_range=='full') checked @endif required>
                                                <label class="form-check-label" for="full">ลาเต็มวัน</label>
                                            </div>
                                            <div class="form-check form-check-inline range_many" @if ($leave->leave_type=='6') hidden @endif>
                                                <input class="form-check-input" type="radio"
                                                    name="leave_range" id="many" value="many" @if ($leave->leave_range=='many') checked @endif>
                                                <label class="form-check-label" for="many">ลามากกว่า 1 วัน</label>
                                            </div>
                                            <div class="form-check form-check-inline range_etc" @if ($leave->leave_type=='6') hidden @endif>
                                                <input class="form-check-input" type="radio"
                                                    name="leave_range" id="etc" value="etc" @if ($leave->leave_range=='etc') checked @endif>
                                                <label class="form-check-label" for="etc">ลาเป็นชั่วโมง</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5 col-12">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text text-info bg-soft-info border-info" id="text_start">วันที่</span>
                                                <input type="text" class="form-control leave-datepicker" id="date_start" name="date_start"
                                                    placeholder="กรอกวันที่" value="{{ \Carbon\Carbon::parse($leave->leave_start_date)->format('d/m/Y') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-7 col-md-7 col-12">
                                            <div class="row">
                                                <div class="col-lg-9 col-md-7 col-12">
                                                    <div class="input-group mb-3 sum_day" @if ($leave->leave_range!='many') hidden @endif>
                                                        <span class="input-group-text text-info bg-soft-info border-info">ถึงวันที่</span>
                                                        <input type="text" class="form-control leave-datepicker"
                                                            id="date_end" name="date_end" placeholder="กรอกวันที่" value="{{ \Carbon\Carbon::parse($leave->leave_end_date)->format('d/m/Y') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-5 col-12">
                                                    <div class="input-group mb-3 sum_day" @if ($leave->leave_range!='many') hidden @endif>
                                                        <input type="number" class="form-control" id="sum_day" name="sum_day" value="{{ $sum_time['d'] }}" autocomplete="off" min="0" max="999" onblur="if(this.value<=0){this.value=0}" onkeypress="return onlyNumberKey(event)">
                                                        <span class="input-group-text text-primary bg-soft-primary border-primary">วัน</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-6 col-12">
                                            <div class="input-group mb-3 hour" @if ($leave->leave_range!='etc') hidden @endif>
                                                <span class="input-group-text text-info bg-soft-info border-info">เวลาเริ่ม</span>
                                                <input type="text" id="time_start" name="time_start" class="form-control hour 24hours-timepicker" value="{{ \Carbon\Carbon::parse($leave->leave_start_time)->format('H:i') }}" placeholder="00:00">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-12">
                                            <div class="input-group mb-3 hour" @if ($leave->leave_range!='etc') hidden @endif>
                                                <span class="input-group-text text-info bg-soft-info border-info">ถึง</span>
                                                <input type="text" id="time_end" name="time_end" class="form-control 24hours-timepicker" value="{{ \Carbon\Carbon::parse($leave->leave_end_time)->format('H:i') }}" placeholder="00:00">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-12">
                                            <div class="input-group mb-3 hour" @if ($leave->leave_range!='etc') hidden @endif>
                                                <select class="form-select" id="sum_hour" name="sum_hour">
                                                    @for ($i=0; $i<=7; $i++)
                                                    <option value="{{ $i }}" @if ($i==$sum_time['h']) selected @endif>{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <span class="input-group-text text-primary bg-soft-primary border-primary" for="sum_hour">ชั่วโมง</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-12">
                                            <div class="input-group mb-3 hour" @if ($leave->leave_range!='etc') hidden @endif>
                                                <select class="form-select" id="sum_minute" name="sum_minute">
                                                    <option value="0" @if ($sum_time['m']==0) selected @endif>0</option>
                                                    <option value="30" @if ($sum_time['m']==30) selected @endif>30</option>
                                                </select>
                                                <span class="input-group-text text-primary bg-soft-primary border-primary" for="sum_minute">นาที</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge badge-soft-danger rounded-pill fs-6" id="msg_time"></span>
                                    </div>
                                    <div class="alert alert-dark mt-3" role="alert">
                                        <h5 class="text-decoration-underline">หมายเหตุ</h5>
                                        1.การขาดงานของลูกจ้างจะถูกหักเงินเดือนตามอัตราค่าแรงรายวัน และ
                                        จะตัดเบี้ยขยันและจะไม่ได้โบนัสในช่วงเวลา 6 เดือน <br>
                                        2.การลาป่วยต้องมีใบรับรองแพทย์มายืนยันเสมอ เว้นแต่จะมีเหตุอันสมควรที่จะแจ้งให้บริษัททราบ
                                        มิเช่นนั้นจะถือว่าลูกจ้างขาดงาน <br>
                                        3.การลากิจได้ไม่เกิน 6 วัน ในระยะเวลา 1 ปียกเว้นพนักงานที่ทำงานยังไม่ครบ 180
                                        วันและต้องลาล่วงหน้าอย่างน้อย 1 วันโดยจะถูกหักค่าแรงในวันนั้น
                                    </div>
                                    <input type="hidden" class="form-control" id="holiday" name="holiday" value="{{ $hol_arr }}">
                                    <input type="hidden" class="form-control" id="record_working" name="record_working" value="{{ $record_working_arr }}">
                                    <input type="hidden" class="form-control" id="leave_id" name="leave_id" value="{{ $leave->leave_id }}">
                                    <input type="hidden" class="form-control" id="leave_mode" name="leave_mode" value="{{ $leave->leave_mode }}">
                                    <input type="hidden" class="form-control" id="period_start" name="period_start" value="{{ $period->start }}">
                                    <input type="hidden" class="form-control" id="period_end" name="period_end" value="{{ $period->end }}">
                                    <input type="hidden" class="form-control" id="period_last" name="period_last" value="{{ $period->last }}">
                                    <input type="hidden" class="form-control" id="pre_period_start" name="pre_period_start" value="{{ $pre_period->start }}">
                                    <input type="hidden" class="form-control" id="pre_period_end" name="pre_period_end" value="{{ $pre_period->end }}">
                                    <input type="hidden" class="form-control" id="pre_period_last" name="pre_period_last" value="{{ $pre_period->last }}">
                                    <input type="hidden" class="form-control" id="action" name="action" value="update">
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ url('leave/dashboard') }}" class="btn btn-soft-secondary waves-effect waves-light"><i class="mdi mdi-keyboard-backspace me-1"></i> ย้อนกลับ</a>
                                            <button type="submit" class="btn btn-soft-primary waves-effect waves-light"><i class="mdi mdi-content-save me-1"></i>อัปเดตใบลา</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/clockpicker/clockpicker.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-leave.init.js') }}"></script>
    <!-- third party js ends -->
@endsection