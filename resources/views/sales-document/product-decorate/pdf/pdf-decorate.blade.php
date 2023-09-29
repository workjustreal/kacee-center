@extends('layouts.pdf-layout', ['page_title' => 'แบบฟอร์มขอสินค้าตกแต่งหน้าร้าน/สินค้าตัวอย่าง/แสตนโชว์'])
<style>
    .table {
        width: 100%;
        border-collapse: collapse;
        padding: 5 30 0 30;
    }

    .line_un {
        border-bottom: 1px dotted #000;
        text-decoration: none;
        /* ตรงนี้ทำให้บรรทัดมันห่าง */
    }

    .head_name {
        background-color: white;
        padding-bottom: 5px;
    }

    .line_un_app {
        width: 300px;
        border-bottom: 1px dotted #000;
        text-decoration: none;
        text-align: center;
        margin: auto;
        width: 50%;
        padding: 10px;
    }

    .topright {
        /* position: absolute; */
        position: fixed;
        top: 0px;
        right: 16px;
        font-size: 18px;
    }
</style>

@section('content')
    @inject('thaiDateHelper', '\App\Services\ThaiDateHelperService')
    <div class="row">
        @php
            $base_url = $_SERVER['DOCUMENT_ROOT'];
        @endphp

        @foreach ($data as $item)
            @php
                $log_list = json_decode($item->log_list);
            @endphp
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="2">
                            <div class="topright">{{ $item->doc_id }}</div>
                            <h3>แบบฟอร์มขอสินค้าตกแต่งหน้าร้าน/สินค้าตัวอย่าง/แสตนโชว์</h3>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php $emp = Auth::User()->findEmployee( $item->emp_id ) @endphp
                    @php
                        if ($emp->nickname) {
                            $emp_name = $emp->name . ' ' . $emp->surname . ' ( ' . $emp->nickname . ' )';
                        } else {
                            $emp_name = $emp->name . ' ' . $emp->surname;
                        }
                    @endphp
                    <tr>
                        <td>
                            <h4 style="margin-top: -25px;" class="line_un"><b class="head_name">เจ้าหน้าที่รับเรื่อง :</b><span
                                    class="px-3">{{ $emp_name }}</span>
                            </h4>
                        </td>
                        <td>
                            <h4 style="margin-top: -25px;" class="line_un"><b class="head_name">วันที่บันทึก
                                    :</b><span
                                    class="px-3">{{ $thaiDateHelper->shortDateFormat($item->created_at) }}</span>
                            </h4>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4 style="margin-top: -25px;" class="line_un"><b class="head_name">ชื่อร้านค้า :</b><span
                                    class="px-3">{{ $item->customer_name }}</span> </h4>
                        </td>
                        <td>
                            <h4 style="margin-top: -25px;" class="line_un"><b class="head_name">รหัสลูกค้า :</b><span
                                    class="px-3">{{ $item->customer_code }}</span> </h4>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4 style="margin-top: -20px" class="line_un"><b class="head_name">สถานะลูกค้า :</b><span
                                    class="px-3">{{ $item->customer_status }}</span> </h4>
                        </td>
                        <td>
                            @php
                                $_pay = number_format($item->limit);
                            @endphp
                            <h4 style="margin-top: -20px" class="line_un"><b class="head_name">วงเงินอนุมัติ :</b><span
                                    class="px-3">{{ $_pay }}</span> </h4>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table style="margin-top: -25px;">
                                <tr>
                                    <td>
                                        <div>
                                            @if ($item->request == 'ขอเพื่อสนับสนุนการขายโดยเสนอส่วนลด/ราคาพิเศษ')
                                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/checkbox-mark.png')) }}"
                                                    height="10" class="px-2">
                                            @else
                                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/checkbox.png')) }}"
                                                    height="10" class="px-2">
                                            @endif
                                            <span>ขอเพื่อสนับสนุนการขายโดยเสนอส่วนลด/ราคาพิเศษ</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="margin-top: -13px;">
                                            @if ($item->request == 'ขอเพื่อสนับสนุนการขายโดยไม่คิดค่าใช้จ่าย')
                                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/checkbox-mark.png')) }}"
                                                    height="10" class="px-2">
                                            @else
                                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/checkbox.png')) }}"
                                                    height="10" class="px-2">
                                            @endif
                                            <span>ขอเพื่อสนับสนุนการขายโดยไม่คิดค่าใช้จ่าย(ฟรี)</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="margin-top: -13px;">
                                            @if ($item->request == 'other')
                                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/checkbox-mark.png')) }}"
                                                    height="10" class="px-2">
                                            @else
                                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/checkbox.png')) }}"
                                                    height="10" class="px-2">
                                            @endif
                                            <span class="line_un"><span class="head_name">อื่นๆ </span>
                                                <span class="px-3">
                                                    {{ $item->request == 'other' ? $item->note : '' }}
                                                </span>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="margin-top: -30px;">
                                <h4 style="margin-bottom: -40px;"><b class="head_name">รายละเอียดสินค้า : </b></h4><br>
                                <span style="line-height: 80%;">{!! $item->description !!}</span>
                                <p style="margin-bottom: -5px; margin-top: -5px;"><b class="head_name">
                                        หมายเหตุเพิ่มเติม : </b>
                                    <span>{{ $item->more }}</span>
                                </p>
                                @foreach ($log_list as $again)
                                    @if ($again->description == 'แก้ไขเพื่อขออีกครั้ง')
                                        /<span class="px-2">(แก้ไข) {{ $again->comment }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    {{-- <tr>
                        @php $emp = Auth::User()->findEmployee( $item->emp_id ) @endphp
                        <td colspan="2">
                            @php
                                if ($emp->nickname) {
                                    $emp_name = $emp->name . ' ' . $emp->surname . ' ( ' . $emp->nickname . ' )';
                                } else {
                                    $emp_name = $emp->name . ' ' . $emp->surname;
                                }
                            @endphp
                            </small></u>
                            <center>
                                <div class="line_un_app">
                                    {{ $emp_name . ' / ' . $thaiDateHelper->shortDateFormat($item->created_at) }}
                                </div>
                                <h4 style="margin-top: -4px">เจ้าหน้าที่รับเรื่อง</h4>
                            </center>
                        </td>
                    </tr> --}}
                    <tr>
                        <td colspan="2">
                            <div style="margin-top: -10px;">
                                <p class="line_un"><b class="head_name">ความคิดเห็นจากผู้บังคับบัญชา :</b>
                                    @php
                                        $a = 0;
                                    @endphp
                                    @foreach ($log_list as $list)
                                        @if ($list->description == 'ManagerApprove' || $list->description == 'Send Secretary')
                                            {{ $a > 0 ? '/' : '' }}
                                            @php
                                                $a++;
                                            @endphp
                                            <span class="px-2">(อนุมัติ) {{ $list->comment }}</span>
                                        @elseif($list->description == 'Manager DisApprove')
                                            {{ $a > 0 ? '/' : '' }}
                                            @php
                                                $a++;
                                            @endphp
                                            <span class="px-2">(ไม่อนุมัติ) {{ $list->comment }}</span>
                                        @endif
                                    @endforeach
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="margin-top: -35px;">
                                @php
                                    $mn_approve = Auth::User()->findEmployee($item->mn_approve);
                                    if ($mn_approve->nickname) {
                                        $emp_names = $mn_approve->name . ' ' . $mn_approve->surname . ' ( ' . $mn_approve->nickname . ' )';
                                    } else {
                                        $emp_names = $mn_approve->name . ' ' . $mn_approve->surname;
                                    }
                                @endphp
                                <center>
                                    <div class="line_un_app">
                                        {{ $emp_names . ' / ' . $thaiDateHelper->shortDateFormat($item->mn_approve_date) }}
                                    </div>
                                    <h4 style="margin-top: -4px">ผู้รับทราบ</h4>
                                </center>
                            </div>
                        </td>
                    </tr>
                    @if ($item->sec_approve != null)
                        <tr>
                            <td colspan="2">
                                <div style="margin-top: -35px;">
                                    <p class="line_un"><b class="head_name">ผลการพิจารณา :</b>
                                        @php
                                            $a = 0;
                                        @endphp
                                        @foreach ($log_list as $list)
                                            @if ($list->description == 'SecretaryApprove')
                                                {{ $a > 0 ? '/' : '' }}
                                                @php
                                                    $a++;
                                                @endphp
                                                <span class="px-2">(อนุมัติ) {{ $list->comment }}</span>
                                            @elseif($list->description == 'Secretary DisApprove')
                                                {{ $a > 0 ? '/' : '' }}
                                                @php
                                                    $a++;
                                                @endphp
                                                <span class="px-2">(ไม่อนุมัติ) {{ $list->comment }}</span>
                                            @endif
                                        @endforeach

                                        {{-- @foreach ($log_list as $list)
                                        @if ($list->description == 'SecretaryApprove')
                                            <span class="px-2">(อนุมัติ)
                                                {{ $list->comment }}</span>
                                        @elseif($list->description == 'Secretary DisApprove')
                                            <span class="px-2">(ไม่อนุมัติ)
                                                {{ $list->comment }}</span>
                                        @endif
                                    @endforeach --}}
                                    </p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div style="margin-top: -30px;">
                                    @php
                                        $sec_approve = Auth::User()->findEmployee($item->sec_approve);
                                        if ($sec_approve->nickname) {
                                            $emp_names = $sec_approve->name . ' ' . $sec_approve->surname . ' ( ' . $sec_approve->nickname . ' )';
                                        } else {
                                            $emp_names = $sec_approve->name . ' ' . $sec_approve->surname;
                                        }
                                    @endphp
                                    <center>
                                        <div class="line_un_app">
                                            {{ $emp_names . ' / ' . $thaiDateHelper->shortDateFormat($item->sec_approve_date) }}
                                        </div>
                                        <h4 style="margin-top: -4px">ผู้อนุมัติ</h4>
                                    </center>
                                </div>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="2">
                            <div style="margin-bottom: 20px;"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>
@endsection
