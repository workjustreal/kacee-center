@extends('layouts.pdf-layout', ['page_title' => 'ใบแจ้งซ่อม'])
<style>
    * {
        box-sizing: border-box;
    }

    /* Create two equal columns that floats next to each other */
    .column {
        float: left;
        width: 45%;
        padding: 20px;
        padding-top: 12px;
        padding-bottom: 1px;
    }

    .column-manage {
        float: left;
        width: 30%;
        padding: 12px;
        /* padding-top: 12px;
        padding-bottom: 1px; */
    }

    /* Clear floats after the columns */
    .row:after {
        content: "";
        display: table;
        clear: both;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        
    }
    thead {
        /* background-color: #333; */
        /* color: white; */
        /* font-size: 0.875rem; */
        text-transform: uppercase;
        letter-spacing: 2%;
    }
    th, td {
        /* border: 1px solid black; */
        /* border-bottom: 1px solid #ddd; */
        padding: 6px;
    }
</style>

@section('content')
    @php
        $base_url = $_SERVER['DOCUMENT_ROOT'];
    @endphp
    @inject('thaiDateHelper', '\App\Services\ThaiDateHelperService')

    

    <table class="table-leave" width="100%">

        <tbody>
            <tr>
                <td style="width: 60px;">
                    <img
                        src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/logo-kacee.png')) }}"
                        alt="logo" width="60" height="60" />
                </td>
                <td>
                    <div style="padding-left: 4px;line-height: 12px;">
                        <span>บริษัท อี .แอนด์. วี จำกัด</span>
                        
                        <br>
                        <span>259 ถนนเลียบคลองภาษีเจริญฝั่งใต้ แขวงหนองแขม เขตหนองแขม กรุงเทพฯ 10160</span>
                    </div>
                </td>
                <td class="text-end" style="color:rgb(131, 130, 130)"><span>รหัสใบแจ้ง {{ $repairs->order_id }}</span></td>
            </tr>
            <tr>
                <td colspan="3" class="text-center p-1" style="font-size: 6mm;"><b><u>ใบแจ้งซ่อม {{ $depts->dept_name }}</u></b></td>
            </tr>
        </tbody>
    </table>

    <div class="text-end">
        <span>วันที่ <span
                class="text-decoration-dotted">{{ \Carbon\Carbon::parse($repairs->created_at)->format('d') }}</span> เดือน
            <span
                class="text-decoration-dotted">{{ \Carbon\Carbon::parse($repairs->created_at)->locale('th_TH')->isoFormat('MMMM') }}</span>
            พ.ศ. <span
                class="text-decoration-dotted">{{ \Carbon\Carbon::parse($repairs->created_at)->format('Y') + 543 }}</span></span>
    </div>

    <div style="line-height: 5mm;">
        <span class="text-muted text-decoration-underline fw-bold"><u>ส่วนที่ 1</u> ข้อมูลผู้แจ้ง</span>
    </div>

    <div>
        <div class="rtv">
            <div class="full-underline abs">
                @php
                    $user = Auth::User()->findEmployee($repairs->user_id);
                @endphp
                <span class="full-dotted">
                    @if ($user->title == 'นาย')
                        <span class="bg-white">ชื่อ (นาย,<span class="text-decoration-line-through">นาง</span>,<span
                                class="text-decoration-line-through">นางสาว</span>)</span>
                    @elseif ($user->title == 'นาง')
                        <span class="bg-white">ชื่อ (<span class="text-decoration-line-through">นาย</span>,นาง,<span
                                class="text-decoration-line-through">นางสาว</span>)</span>
                    @elseif ($user->title == 'นางสาว')
                        <span class="bg-white">ชื่อ (<span class="text-decoration-line-through">นาย</span>,<span
                                class="text-decoration-line-through">นาง</span>,นางสาว)</span>
                    @else
                        <span class="bg-white">ชื่อ (นาย,นาง,นางสาว)</span>
                    @endif
                    <span class="text-dark px-5">{{ $user->name . ' ' . $user->surname }}</span>

                    <span class="bg-white">ชื่อเล่น </span>
                    <span
                        class="text-dark @if ($user->nickname) px-3 @else px-5 @endif">{{ $user->nickname }}</span>

                    <span class="bg-white">เบอร์โทรภายใน </span>
                    <span
                        class="text-dark @if ($user->tel) px-3 @else px-5 @endif">{{ $user->tel }}</span>

                </span>

                <span class="full-dotted">
                    <span class="bg-white">ฝ่าย / แผนก</span>
                    <span class="text-dark px-5">{{ $dept_parent->dept_name }}</span>
                </span>
            </div>
        </div>
    </div>

    @if ($repairs->order_dept == 'A03050200')
        <div style="margin-top: 55px;">
            <div class="rtv">
                <div class="full-underline abs">
                    <span class="full-dotted">

                        <span class="bg-white">ทะเบียนรถ : </span>
                        <span class="text-dark px-5">{{ $repairs->car_id }}</span>

                        <span class="bg-white">เลขไมล์ล่าสุด : </span>
                        <span class="text-dark @if ($repairs->car_mile) px-3 @else px-5 @endif">{{ $repairs->car_mile }}</span>

                        <span class="bg-white">ประเภทงานซ่อม : </span>
                        <span class="text-dark @if ($repairs->order_type) px-3 @else px-5 @endif">{{ $repairs->order_type }}</span>
                    </span>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <div style="margin-bottom: 7px;"><b>แจ้งปัญหา : </b></div>
            {{-- <div style="float: left;width: 15%;height: 80px;">แจ้งปัญหา : </div> --}}
            <div style="width: 95%;margin-top: 12px;margin-left: 15px;">
                <div class="row">
                    @if ($repair_type)
                        @php
                            $i = 0;
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
                                @php $i++; @endphp
                                <div class="column-three">
                                    @if (str_contains($repairs->order_tool, $list->name))
                                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/checkbox-mark.png')) }}"
                                            height="14">
                                    @else
                                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/checkbox.png')) }}"
                                            height="14">
                                    @endif
                                    <span>{{ $list->name }}</span>
                                </div>
                                @php
                                    if ($i == 4) {
                                        $i = 0;
                                        echo '<br style="line-height: 15px;">';
                                    }
                                @endphp
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    @else
        <div style="margin-top: 60px;">
            <div style="margin-bottom: 7px;"><b>แจ้งปัญหา : </b></div>
            {{-- <div style="float: left;width: 15%;height: 80px;">แจ้งปัญหา : </div> --}}
            <div style="width: 95%;margin-top: 12px;margin-left: 15px;">
                <div class="row">
                    @if ($repair_type)
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($repair_type as $list)
                            @if ($repairs->order_dept == $list->dept_id)
                                @php $i++; @endphp
                                <div class="column-three">
                                    @if ($repairs->order_type == $list->name)
                                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/checkbox-mark.png')) }}"
                                            height="14">
                                    @else
                                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/checkbox.png')) }}"
                                            height="14">
                                    @endif
                                    <span>{{ $list->name }}</span>
                                </div>
                                @php
                                    if ($i == 4) {
                                        $i = 0;
                                        echo '<br style="line-height: 15px;">';
                                    }
                                @endphp
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    @endif
 
    <div class="row" style="margin-top: 7px;">
        <div class="full-underline abs">
            @if ($repairs->order_dept !== 'A03050200')
                <span class="full-dotted mb-1">
                    <span class="bg-white">อุปกรณ์ที่แจ้ง : </span>
                    <span class="text-dark @if ($repairs->order_tool) px-3 @else px-5 @endif">
                        {{ $repairs->order_tool ? $repairs->order_tool : '-' }}
                        &nbsp;&nbsp;&nbsp;&nbsp;
                    </span>

                    <span class="bg-white">สถานที่ซ่อม : </span>
                    <span class="text-dark @if ($repairs->order_address) px-3 @else px-5 @endif">
                        {{ $repairs->order_address ? $repairs->order_address : '-' }}
                    </span>
                </span>
            @else
                <span class="full-dotted mb-1">
                    <span class="bg-white">สถานที่ซ่อม : </span>
                    <span class="text-dark @if ($repairs->order_address) px-3 @else px-5 @endif">
                        {{ $repairs->order_address ? $repairs->order_address : '-' }}
                    </span>
                </span>
            @endif
            <span class="full-dotted mb-1">
                <span class="bg-white">รายละเอียดปัญหา : </span>
                <span class="text-dark @if ($repairs->order_detail) px-3 @else px-5 @endif">
                    {{ $repairs->order_detail ? $repairs->order_detail : '-' }}
                </span>
            </span>
            <span class="full-dotted mb-1"></span>
        </div>
    </div>


    <div class="row" style="margin-top: 30mm">
        <div class="column text-center">
            <div class="rtv">
                <div class="full-underline abs px-auto">
                    <span class="full-dotted">
                        @if ($repairs->user_id)
                            {{ $user->name . ' ' . $user->surname }} /
                            {{ $thaiDateHelper->shortDateFormat($repairs->order_date) }}
                        @endif
                    </span>
                </div>
            </div>
            <div>
                <p class="fw-bold">ผู้แจ้งซ่อม / ว.ด.ป.</p>
            </div>
        </div> 
        <div class="column text-center">
            <div class="rtv">
                <div class="full-underline abs px-auto">
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
            <p class="fw-bold">ผู้รับแจ้ง / ว.ด.ป.</p>
        </div>
    </div>

    <hr class="style-three" />

    <div>
        <span class="text-muted text-decoration-underline fw-bold">
            <u>ส่วนที่ 2</u> สำหรับผู้ปฏิบัติงาน
        </span>
    </div>

    <div class="row">
        <div class="full-underline abs">
            <span class="full-dotted">
                <span class="bg-white">วันที่เริ่มซ่อม : </span>
                <span class="text-dark @if ($repairs->order_address) px-4 @else px-5 @endif">
                    {{ $repairs->start_date ? $thaiDateHelper->shortDateFormat($repairs->start_date) : '-' }}
                </span>

                <span class="bg-white">วันที่ซ่อมเสร็จ : </span>
                <span class="text-dark @if ($repairs->order_detail) px-4 @else px-5 @endif">
                    {{ $repairs->end_date ? $thaiDateHelper->shortDateFormat($repairs->end_date) : '-' }}
                </span>
            </span>
        </div>
    </div>

    <div class="row" style="margin-top: 30px">
        <div class="full-underline abs">
            <span class="full-dotted mb-1">
                <span class="bg-white">ผู้ปฏิบัติงาน : </span>
                <span class="text-dark @if ($tech_name) px-1 @else px-5 @endif">
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
                            <span class="px-1">
                                {{ $i++ . '. ' . $worker->title . ' ' . $worker->name . ' ' . $worker->surname . $work }}
                            </span>
                        @endforeach
                    @else
                        <span style="margin-bottom: 7px;">-</span>
                    @endif
                </span>
            </span>
            <span class="full-dotted mb-1"></span>
        </div>
    </div>

    <div class="row" style="margin-top: 70px;">
        <div class="full-underline abs">
            <span class="full-dotted mb-1">
                <span class="bg-white">รายละเอียด ( ผู้ดำเนินการ ) : </span>
                <span class="text-dark @if ($tech_name) px-2 @else px-5 @endif">
                    @if ($tech_detail)
                        @foreach ($tech_detail as $list)
                            <span style="margin-bottom: 7px;">
                            {{ '( '. $thaiDateHelper->shortDateFormat($list['start_date']). ' )'  }}
                            {{ $list['s_job'] . ' ' . $list['s_tool'] . ' ' . $list['detail'] }}
                            </span>
                        @endforeach
                    @else
                        <span style="margin-bottom: 7px;">-</span>
                    @endif
                </span>
            </span>
            <span class="full-dotted mb-1"></span>
            <span class="full-dotted mb-1"></span>
        </div>
    </div>

    <div class="row" style="margin-top: 105px;">
        <div class="full-underline abs">
            <span class="full-dotted mb-1">
                <span class="bg-white">ยอดรวมค่าใช้จ่าย : </span>
                <span class="text-dark @if ($repairs->price) px-3 @else px-5 @endif">
                    {{ $repairs->price ? $repairs->price : '-' }}
                    &nbsp;&nbsp;&nbsp;&nbsp;บาท
                </span>

                <span class="bg-white">รายละเอียดเพิ่มเติม ( ผู้ตรวจสอบงาน ) : </span>
                <span class="text-dark @if ($ap_detail) px-2 @else px-5 @endif">
                    @if ($ap_detail)
                        @foreach ($ap_detail as $lists)
                            <span style="margin-bottom: 7px;">{{ $lists['detail'] }}</span>
                        @endforeach
                    @else
                        <span style="margin-bottom: 7px;">-</span>
                    @endif
                </span>
            </span>
            
            <span class="full-dotted mb-1"></span>
            <span class="full-dotted mb-1"></span>
            <span class="full-dotted mb-1"></span>
        </div>
    </div>

    <div class="row" style="margin-top: 40mm">
        <div class="column-manage text-center">
            <div class="rtv">
                <div class="full-underline abs px-auto">
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
            <div>
                <p class="fw-bold">ผู้มอบหมายงาน / ว.ด.ป.</p>
            </div>
        </div>
        <div class="column-manage text-center">
            <div class="rtv">
                <div class="full-underline abs px-auto">
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
            <div>
                <p class="fw-bold">ผู้ตรวจสอบงาน / ว.ด.ป.</p>
            </div>
        </div>
        <div class="column-manage text-center">
            <div class="rtv">
                <div class="full-underline abs px-auto">
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
            <p class="fw-bold">ผู้ตรวจรับงาน / ว.ด.ป.</p>
        </div>
    </div>


@endsection
