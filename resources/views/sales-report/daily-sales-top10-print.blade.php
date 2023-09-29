<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $data["title"] }}</title>
    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }} " rel="stylesheet" type="text/css" />
    <style>
        @media print {
            @page {size: A4; }
        }
        body {
            background-color: #ffffff;
        }
        table {
            font-size: 12px;
        }
        table > thead > tr {
            line-height: 6px;
        }
        table > tbody > tr {
            line-height: 6px;
        }
    </style>
</head>
<body class="px-2">
    <h4 class="text-center">{{ $data["header"] }}</h4>
    <table class="table table-sm table-bordered text-nowrap text-dark">
        <thead>
            <tr>
                <th>อันดับ</th>
                <th>ชื่อร้านค้า</th>
                <th>รหัสร้านค้า</th>
                <th class="text-end">จำนวน</th>
                <th class="text-end">ยอดเงิน (บาท)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data["rows"] as $v)
                <tr>
                    <th>{!! $v["no"] !!}</th>
                    <th>{!! $v["cusnam"] !!}</th>
                    <th>{!! $v["cuscod"] !!}</th>
                    <th class="text-end">{!! $v["qty_total"] !!}</th>
                    <th class="text-end">{!! $v["price_total"] !!}</th>
                </tr>
                @foreach ($v["headers"] as $h)
                    <tr>
                        <th class="text-end"></th>
                        <th>{!! $h["doc_num"] !!} / ผู้รับออเดอร์ - {!! $h["shortnam"] !!}</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    @foreach ($h["items"] as $item)
                        <tr>
                            <td></td>
                            <td>รหัสสินค้า: {!! $item["stkcod"] !!}</td>
                            <td>{!! $item["stkdes"] !!}</td>
                            <td class="text-end">{!! $item["qty_total"] !!}</td>
                            <td class="text-end">{!! $item["price_total"] !!}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
    <script>
        window.onload = function() {
            window.onafterprint = window.close;
            window.print();
        };
    </script>
</body>
</html>
