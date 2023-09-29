@extends('layouts.pdf-layout', ['page_title' => 'ใบเบิกอุปกรณ์แจ้งซ่อม'])
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

    table.border1 {
        border-collapse: collapse;
        border-radius: 10px;
    }

    table.border1 thead,
    table.border1 th {
        border: 1.5px solid black;
    }

    table.border1 td {
        border: 1px solid rgb(31, 31, 31);
    }

    table.border1 tr {
        line-height: 17px;
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
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($base_url . '/assets/images/logo-kacee.png')) }}"
                        alt="logo" width="60" height="60" />
                </td>
                <td>
                    <div style="padding-left: 4px;line-height: 12px;">
                        <span>บริษัท อี .แอนด์. วี จำกัด</span>
                        <br>
                        <span>259 ถนนเลียบคลองภาษีเจริญฝั่งใต้ แขวงหนองแขม เขตหนองแขม กรุงเทพฯ 10160</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-center p-1" style="font-size: 5mm;"><b><u>ใบขอสั่งซื้อ (PURCHASE
                            REQUEST)</u></b></td>
            </tr>

            <tr>
                <td colspan="2"><span>{{ $data_dept->dept_name ? $data_dept->dept_name : '' }}</span></td>
                <td style="text-align: right">
                    @if ($data[0]->created_at)
                        <span>
                            วันที่
                            <span
                                class="text-decoration-dotted">{{ \Carbon\Carbon::parse($data[0]->created_at)->format('d') }}</span>
                            เดือน
                            <span
                                class="text-decoration-dotted">{{ \Carbon\Carbon::parse($data[0]->created_at)->locale('th_TH')->isoFormat('MMMM') }}</span>
                            พ.ศ.
                            <span
                                class="text-decoration-dotted">{{ \Carbon\Carbon::parse($data[0]->created_at)->format('Y') + 543 }}</span>
                        </span>
                    @else
                        <span>
                            วันที่
                            <span class="text-decoration-dotted">&nbsp;&nbsp;</span>
                            เดือน
                            <span
                                class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            พ.ศ.
                            <span
                                class="text-decoration-dotted">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </span>
                    @endif

                </td>
            </tr>
        </tbody>
    </table>

    <table class="mt-2 border1" width="100%">
        <thead class="text-center">
            <th width="5%">No.</th>
            <th width="45%">รายการ</th>
            <th width="8%">จำนวน</th>
            <th width="10%">ราคา</th>
            <th width="12%">รวม</th>
            <th width="20%">หมายเหตุ</th>
        </thead>
        <tbody class="">
            @if (count($data) > 0)
                @foreach ($data as $item)
                    <tr>
                        <td class="text-center">{{ $loop->index + 1 }}</td>
                        <td class="px-1">{{ $item->products_name }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-center">{{ number_format($item->prices) }}</td>
                        <td class="text-center">{{ number_format($item->total_prices) }}</td>
                        <td class="px-1">{{ $item->comment }}</td>
                    </tr>
                @endforeach

                @php $dataCount = count($data); @endphp

                @if ($dataCount < 25)
                    @for ($i = $dataCount; $i < 25; $i++)
                        <tr>
                            <td class="text-center">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td class="text-center">&nbsp;</td>
                            <td class="text-center">&nbsp;</td>
                            <td class="text-center">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                @endif
            @else
                @for ($i = 1; $i < 25; $i++)
                    <tr>
                        <td class="text-center">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <div class="row" style="margin-top: 20mm">
        <div class="column-manage text-center">
            <div class="rtv">
                <div class="full-underline abs px-auto">
                    <span class="full-dotted">
                        @if ($data[0]->emp_id)
                            @php $emp = Auth::User()->findEmployee($data[0]->emp_id); @endphp
                            {{ $emp->name . ' ' . $emp->surname }} /
                            {{ $thaiDateHelper->shortDateFormat($data[0]->created_at) }}
                        @endif
                    </span>
                </div>
            </div>
            <div>
                <p class="fw-bold mt-3">ผู้สั่งซื้อ / ว.ด.ป.</p>
            </div>
        </div>
        <div class="column-manage text-center"></div>
        
        <div class="column-manage text-center">
            <div class="rtv">
                <div class="full-underline abs px-auto">
                    <span class="full-dotted"> </span>
                </div>
            </div>
            <p class="fw-bold mt-3">ผู้อนุมัติ / ว.ด.ป.</p>
        </div>
    </div>
@endsection
