<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>เอกสารการจัดส่ง (สำหรับขนส่ง)</title>
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/bootstrap-5.0.2/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        @font-face {
            font-family: "Kanit";
            font-style: normal;
            font-weight: 300;
            src: url("/assets/fonts/kanit-300.eot"); /* IE9 Compat Modes */
            src: local(""),
                url("/assets/fonts/kanit-300.eot?#iefix") format("embedded-opentype"),
                /* IE6-IE8 */ url("/assets/fonts/kanit-300.woff2") format("woff2"),
                /* Super Modern Browsers */ url("/assets/fonts/kanit-300.woff")
                    format("woff"),
                /* Modern Browsers */ url("/assets/fonts/kanit-300.ttf")
                    format("truetype"),
                /* Safari, Android, iOS */ url("/assets/fonts/kanit-300.svg#Kanit")
                    format("svg"); /* Legacy iOS */
        }
        body {
            margin: 0;
            font-family: Kanit, sans-serif;
            font-size: 0.6875rem;
            font-weight: 400;
            line-height: 1.5;
            color: #333b46;
            background-color: #f5f6f8;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }
        .thead-color {
            color: #333b46;
            background-color: #e2e3e5;
        }
        .header {
            position: running(HeaderRunning);
        }

        @page {
            size: A4;
            margin: 30mm 15mm 18mm 15mm;

            @bottom-right {
                content: counter(page) "/"counter(pages);
            }

            @top-center {
                padding-top: 15mm;
                content: element(HeaderRunning);
            }
        }
    </style>
</head>

<body onafterprint="$('.btn-print').show()">
    <div class="wrapper">
        <div class="header">
            <div class="btn-print" style="position: absolute;top: 0;right: 0;">
                <a href="javascript:window.close()" class="btn btn-secondary waves-effect waves-light"><i class="mdi mdi-close me-1"></i> ปิด</a>
                <a href="javascript:window.print()" onclick="$('.btn-print').hide()" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-printer me-1"></i> พิมพ์</a>
            </div>
            <span class="text-center" style="font-size: 16px;">เอกสารการจัดส่ง</span>
            <div class="text-center" style="margin-top: 0px;">
                <span class="text-center">เลขเอกสาร <b>{{ $header->running }}</b></span>&nbsp;&nbsp;
                <span class="text-center">ทะเบียนรถ <b>{{ $header->vehicle_registration }}</b></span>&nbsp;&nbsp;
                @if ($header->ship_com != 1)
                <span class="text-center">ขนส่ง <b>{{ $header->ship_com_name }}</b></span>&nbsp;&nbsp;
                @endif
                <span class="text-center">วันที่เช็คเอาท์
                    <b>{{ \Carbon\Carbon::parse($header->checkout_date)->format('d/m/Y') }}</b></span>
            </div>
            <table class="table table-sm" style="margin-top: 6px;">
                <thead class="thead-color text-start">
                    <tr>
                        <th width="40">ลำดับ</th>
                        <th width="110">หมายเลขขนส่ง</th>
                        <th width="60">แพ็คเกจ</th>
                        <th width="60">ร้านค้า</th>
                        <th>เวลา</th>
                        <th width="40">ลำดับ</th>
                        <th width="110">หมายเลขขนส่ง</th>
                        <th width="60">แพ็คเกจ</th>
                        <th width="60">ร้านค้า</th>
                        <th>เวลา</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="content table-responsive">
            <table class="table table-sm" style="margin-top: 2px;">
                <tbody>
                    @for ($i=0; $i<count($detail); $i++)
                    <tr>
                        <td width="40">{{ number_format($detail[$i][1]["line"]) }}</td>
                        <td width="110">{{ $detail[$i][1]["trackingnumber"] }}</td>
                        <td width="60">{{ $detail[$i][1]["packaging_total"] }}</td>
                        <td width="60">{{ $detail[$i][1]["eplatform_name"] }}</td>
                        <td>{{ \Carbon\Carbon::parse($detail[$i][1]["updated_at"])->format('H:i:s') }}</td>
                        @if (isset($detail[$i][2]["line"]))
                        <td width="40">{{ number_format($detail[$i][2]["line"]) }}</td>
                        <td width="110">{{ $detail[$i][2]["trackingnumber"] }}</td>
                        <td width="60">{{ $detail[$i][2]["packaging_total"] }}</td>
                        <td width="60">{{ $detail[$i][2]["eplatform_name"] }}</td>
                        <td>{{ \Carbon\Carbon::parse($detail[$i][2]["updated_at"])->format('H:i:s') }}</td>
                        @else
                        <td width="40"></td>
                        <td width="110"></td>
                        <td width="60"></td>
                        <td width="60"></td>
                        <td></td>
                        @endif
                    </tr>
                    @endfor
                    @if ($dataSumTotal->tracking_total <= 0)
                    <tr>
                        <td colspan="10" class="text-center">ไม่พบข้อมูล</td>
                    </tr>
                    @else
                    <tr>
                        <th colspan="10" class="text-center"></th>
                    </tr>
                    <tr class="thead-color">
                        <th colspan="10" class="text-center">สรุป</th>
                    </tr>
                    <tr class="thead-color">
                        <th colspan="2">ร้านค้า</th>
                        <th colspan="2">หมายเลขขนส่ง</th>
                        <th colspan="2">แพ็คเกจ</th>
                        <th colspan="4"></th>
                    </tr>
                    @foreach ($dataSumShop as $list)
                    <tr>
                        <td colspan="2">{{ $list->eplatform_name }}</td>
                        <td colspan="2">{{ number_format($list->tracking_total) }}</td>
                        <td colspan="2">{{ number_format($list->packaging_total) }}</td>
                        <td colspan="4"></td>
                    </tr>
                    @endforeach
                    <tr>
                        <th colspan="2">รวมทั้งหมด</th>
                        <th colspan="2">{{ number_format($dataSumTotal->tracking_total) }}</th>
                        <th colspan="2">{{ number_format($dataSumTotal->packaging_total) }}</th>
                        <th colspan="4"></th>
                    </tr>
                    <tr class="border-white">
                        <td colspan="10">
                            <div class="d-flex justify-content-between mx-5 mt-2">
                                <div class="text-center">
                                    <p>ผู้รับของ</p>
                                    @if($header->signature!='')
                                    <img id="sig-image" width="130" height="50" src="{{ URL::asset('assets/images/signature').'/'.$header->signature }}" alt="Your signature will go here!"/>
                                    @else
                                    <p>( ....................................................................... )</p>
                                    @endif
                                    @if($header->remark!='')
                                    <p>{{ $header->remark }}</p>
                                    @endif
                                </div>
                                <div class="text-center">
                                    <p>ผู้ส่งของ</p>
                                    <p>( {{ $header->fname . ' ' . $header->lname }} )</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/paged.polyfill.js') }}"></script>
    <script type="text/javascript">
        window.onload = function(e) {
            // window.setTimeout(() => {
            //     window.onafterprint = window.close;
            //     window.print();
            // }, 800);
        };
    </script>
</body>
