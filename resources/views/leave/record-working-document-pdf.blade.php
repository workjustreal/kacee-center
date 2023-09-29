@extends('layouts.pdf-layout', ['page_title' => "ใบบันทึกวันทำงาน"])
@section('content')
@php
    $base_url = $_SERVER['DOCUMENT_ROOT'];
@endphp
<table class="table-leave" width="100%">
    <tbody>
        <tr>
            <td style="width: 60px;"><img src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/logo-kacee.png')) }}" alt="logo" width="60" height="60"></td>
            <td>
                <div style="padding-left: 4px;line-height: 12px;">
                    <span>บริษัท อี .แอนด์. วี จำกัด</span><br>
                    <span>259 ถนนเลียบคลองภาษีเจริญฝั่งใต้ แขวงหนองแขม เขตหนองแขม กรุงเทพฯ 10160</span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="text-center"><b><u>ใบบันทึกวันทำงาน</u></b></td>
        </tr>
    </tbody>
</table>
<div class="text-end">
    <span>วันที่ <span class="text-decoration-dotted">{{ \Carbon\Carbon::parse($leave->created_at)->format('d') }}</span> เดือน <span class="text-decoration-dotted">{{ \Carbon\Carbon::parse($leave->created_at)->locale('th_TH')->isoFormat('MMMM') }}</span> พ.ศ. <span class="text-decoration-dotted">{{ (\Carbon\Carbon::parse($leave->created_at)->format('Y') + 543) }}</span></span>
</div>
<div>
    <div class="rtv">
        <div class="full-underline abs">
            <span class="full-dotted">
                @if ($emp->title == "นาย")
                <span class="bg-white">ข้าพเจ้า (นาย,<del>นาง</del>,<del>นางสาว</del>)</span>
                @elseif ($emp->title == "นาง")
                <span class="bg-white">ข้าพเจ้า (<del>นาย</del>,นาง,<del>นางสาว</del>)</span>
                @elseif ($emp->title == "นางสาว")
                <span class="bg-white">ข้าพเจ้า (<del>นาย</del>,<del>นาง</del>,นางสาว)</span>
                @else
                <span class="bg-white">ข้าพเจ้า (นาย,นาง,นางสาว)</span>
                @endif
                <span class="px-3">{{ $emp->name . ' ' . $emp->surname }}</span>
            </span>
            <span class="full-dotted">
                <span class="bg-white">ส่วน</span>
                @if ($dept_arr["level1"]["name"]!='')
                <span class="px-3">{{ $dept_arr["level1"]["name"] }}</span>
                @else
                <span class="px-5">&nbsp;</span>
                @endif
                <span class="bg-white">ฝ่าย</span>
                @if ($dept_arr["level2"]["name"]!='')
                <span class="px-3">{{ $dept_arr["level2"]["name"] }}</span>
                @else
                <span class="px-5">&nbsp;</span>
                @endif
                <span class="bg-white">แผนก</span>
                @if ($dept_arr["level3"]["name"]!='')
                <span class="px-3">{{ $dept_arr["level3"]["name"] }}</span>
                @else
                <span class="px-5">&nbsp;</span>
                @endif
                <span class="bg-white">หน่วยงาน</span>
                @if ($dept_arr["level4"]["name"]!='')
                <span class="px-3">{{ $dept_arr["level4"]["name"] }}</span>
                @else
                <span class="px-5">&nbsp;</span>
                @endif
            </span>
            <span class="full-dotted">
                <span class="bg-white">ตำแหน่ง</span>
                @if ($emp->position_name!='')
                <span class="px-3">{{ $emp->position_name }}</span>
                @else
                <span class="px-5">&nbsp;</span>
                @endif
                <span class="bg-white">ระยะเวลาทำงาน</span><span class="px-3">{{ $worked_days["worked_text"] }}</span>
            </span>
        </div>
    </div>
</div>
<div style="margin-top: 90px;">
    <div style="float: left;width: 15%;height: 50px;">มีความประสงค์ขอ</div>
    <div style="width: 85%;margin-top: 12px;">
        <div class="row">
            <div class="column-three">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/checkbox-mark.png')) }}" height="14">
                <span>แจ้งบันทึกวันทำงาน</span>
            </div>
        </div>
    </div>
</div>
<div>
    <span>วันที่ลา</span>
    <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>{{ \Carbon\Carbon::parse($leave->work_date)->format('d') }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    <span>เดือน</span>
    <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>{{ \Carbon\Carbon::parse($leave->work_date)->locale('th_TH')->isoFormat('MMMM') }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    <span>พ.ศ.</span>
    <span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>{{ (\Carbon\Carbon::parse($leave->work_date)->format('Y') + 543) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    <div class="rtv">
        <div class="full-underline abs">
            <span class="full-dotted mb-2"><span class="bg-white">เหตุผลที่บันทึกวันทำงาน</span><span class="text-dark px-3">{{ $leave->remark }}</span></span>
        </div>
    </div>
</div>
@if (count($leaveLeader))
<div class="row mt-4">
    <div class="column-two">
        <span>ลงชื่อ</span><span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>@if (count($leaveLeader)) {{ $leaveLeader['name'] }} @endif</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span>ผู้บันทึกแทน</span>
    </div>
    <div class="column-two text-end">
        <span>ลงชื่อ</span><span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>@if (count($leaveEmp)) {{ $leaveEmp['name'] }} @endif</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span>ผู้บันทึก</span>
    </div>
</div>
@else
<div class="text-end mt-4">
    <span>ลงชื่อ</span><span class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>@if (count($leaveEmp)) {{ $leaveEmp['name'] }} @endif</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span>ผู้บันทึก</span>
</div>
@endif
<div class="row mt-3">
    <div class="column-approve text-center">
        <div class="rtv">
            <div class="full-underline abs px-auto ms-2" style="width: 230px;">
                <span class="full-dotted">@if (count($approvedLeader)) {{ $approvedLeader['name'] }} / {{ $approvedLeader['date'] }} @endif</span>
            </div>
        </div>
        <p class="fw-bold">หัวหน้า / ว.ด.ป.</p>
    </div>
    <div class="column-approve text-center">
        <div class="rtv">
            <div class="full-underline abs px-auto ms-2" style="width: 230px;">
                <span class="full-dotted">@if (count($approvedManager)) {{ $approvedManager['name'] }} / {{ $approvedManager['date'] }} @endif</span>
            </div>
        </div>
        <p class="fw-bold">ผู้จัดการ / ว.ด.ป.</p>
    </div>
    <div class="column-approve text-center">
        <div class="rtv">
            <div class="full-underline abs px-auto ms-2" style="width: 230px;">
                <span class="full-dotted">@if (count($approvedHR)) {{ $approvedHR['name'] }} / {{ $approvedHR['date'] }} @endif</span>
            </div>
        </div>
        <p class="fw-bold">ฝ่ายบุคคล / ว.ด.ป.</p>
    </div>
</div>
<div style="line-height: 10px;">
    <small class="text-muted text-decoration-underline fw-bold"><u>หมายเหตุ</u></small><br>
    <small>
        1. การขาดงานของลูกจ้างจะถูกหักเงินเดือนตามอัตราค่าแรงรายวัน และ จะตัดเบี้ยขยันและจะไม่ได้รับโบนัสในช่วงเวลา 6 เดือน<br>
        2. การลาป่วยต้องมีใบรับรองแพทย์มายืนยันเสมอ เว้นแต่จะมีเหตุอันสมควรที่แจ้งให้บริษัททราบ มิเช่นนั้นจะถือว่าลูกจ้างขาดงาน<br>
        3. การลากิจลาได้ไม่เกิน 6 วัน ในระยะเวลา 1 ปียกเว้นพนักงานที่ทำงานยังไม่ครบ 180 วันและต้องลาล่วงหน้าอย่างน้อย 1 วันโดยจะถูกหักค่าแรงในวันนั้น
    </small>
</div>
@endsection