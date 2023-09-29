@extends('layouts.pdf-layout', ['page_title' => 'ฟอร์มอนุมัติการลงสินค้าให้ลูกค้าKC'])
<style>
    * {
        /* box-sizing: border-box; */
    }

    html {
        margin-top: 10mm !important;
    }


    /* Create two equal columns that floats next to each other */
    .column {
        /* float: left;
        width: 50%; */
        /* padding: 20px;
        padding-top: 12px;
        padding-bottom: 1px; */
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
        /* content: "";
        display: table;
        clear: both; */
    }

    .row {
        /* margin-top: 5mm; */
        /* margin-bottom: 2mm; */
        margin-bottom: 10mm;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid black;
        padding: 0 5 0 5;
    }

    thead,
    tbody {
        text-transform: uppercase;
        letter-spacing: 3%;
    }

    td {
        /* margin-left: 10ex; */
        padding: 0 10 0 10;
    }

    u {
        border-bottom: 1px dotted #000;
        text-decoration: none;
    }

    .t1 {
        width: 50mm;
    }
</style>

@section('content')
    @inject('thaiDateHelper', '\App\Services\ThaiDateHelperService')
    <div class="row">
        @php
            $base_url = $_SERVER['DOCUMENT_ROOT'];
            // print_r($_logs);
        @endphp
        {{-- @foreach ($_logs as $log)
            {{$log->emp_id}}
        @endforeach --}}

        @foreach ($_datas as $item)
            @php
                $line = $loop->index + 1;
                $css = $line % 2 === 0 ? 'float: right;' : 'float: left;';
                $br = $line > 1 && $line % 2 === 0 ? true : false;
            @endphp
            <div class="column" style="width: 50%; {{ $css }}">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-end p-1 m-0" colspan="2" style="font-size: 4mm;color:darkgray">รหัสใบแจ้ง {{ $item->gen_id }}</th>
                        </tr>
                        <tr>
                            <th class="m-0" colspan="2">ฝ่ายขาย & ฝ่ายขนส่ง</th>
                        </tr>
                        <tr>
                            <th colspan="2">แบบฟอร์ม การอนุมัติให้ลงสินค้าให้ลูกค้า KACEE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <small class="text-muted text-decoration-underline fw-bold">รหัสลูกค้า : </small>
                                <u><small style="width: 102mm"
                                        class="px-3">{{ $item->customer_code . ' ( ' . $item->customer_name . ' )' }}</small></u>
                            </td>
                        </tr>

                        <tr>
                            <td class="t1">
                                <small class="text-muted text-decoration-underline fw-bold">เลขที่ IV : </small>
                                <u><small style="width: 28mm" class="px-3">{{ $item->invoice }}</small></u>
                            </td>
                            <td>
                                @php
                                    $_pay = number_format($item->pay);
                                @endphp
                                <small class="text-muted text-decoration-underline fw-bold">ยอดเงิน : </small>
                                <u><small style="width: 47mm" class="px-3">{{ $_pay }} บาท</small></u>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <small class="text-muted text-decoration-underline fw-bold">หมายเหตุ : </small>
                                <u><small style="width: 108mm"
                                        class="px-2">{{ $item->comment ? $item->comment : '-' }}</small></u>
                            </td>
                        </tr>

                        <tr>
                            @php $emp = Auth::User()->findEmployee( $item->emp_id ) @endphp
                            {{-- @php $apv = Auth::User()->findEmployee( $item->approve_id ) @endphp --}}
                            <td colspan="2">
                                <small class="text-muted text-decoration-underline fw-bold">ผู้ลงบันทึก : </small>
                                <u><small style="width: 107mm" class="px-2">
                                        @php
                                            if ($emp->nickname) {
                                                $emp_name = $emp->name . ' ' . $emp->surname . ' ( ' . $emp->nickname . ' )';
                                            } else {
                                                $emp_name = $emp->name . ' ' . $emp->surname;
                                            }
                                            
                                        @endphp
                                        {{ $emp_name. ' / ' . $thaiDateHelper->shortDateFormat($item->created_at)  }}
                                    </small></u>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <small class="text-muted text-decoration-underline fw-bold">ผู้พิมพ์ใบ : </small>
                                <u><small style="width: 109mm" class="px-2">
                                    @php
                                        $logEmp = Auth::User()->findEmployee( $item->log_emp_id );
                                        if ($logEmp->nickname) {
                                            $emp_names = $logEmp->name . ' ' . $logEmp->surname . ' ( ' . $logEmp->nickname . ' )';
                                        } else {
                                            $emp_names = $logEmp->name . ' ' . $logEmp->surname;
                                        }
                                        
                                    @endphp
                                    {{ $emp_names. ' / ' . $thaiDateHelper->shortDateFormat($item->log_created_at)  }}
                                </small></u>

                                <small class="text-muted fw-bold px-3">พิมพ์ครั้งที่ : </small>
                                <small class="">{{$item->log_print}}</small>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <div style="margin-bottom: 30px;"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if ($br)
    </div>
    <div class="row">
        @endif
        @endforeach
    </div>
@endsection
