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
        table > thead > tr.font-header {
            font-size: 14px;
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
    <table class="table table-sm table-bordered text-nowrap text-dark">
        <thead>
            <tr class="font-header text-success">
                <th colspan="4">{!! $data["header"]["title"] !!}</th>
                <th class="text-end">{!! $data["header"]["qty_total"] !!}</th>
                <th class="text-end">{!! $data["header"]["price_total"] !!}</th>
            </tr>
            <tr>
                <th>หมวดหมู่รายวัน</th>
                <th>รหัสสินค้า</th>
                <th>ชื่อสินค้า</th>
                <th>หน่วย</th>
                <th class="text-end">จำนวน</th>
                <th class="text-end">ยอดขาย(บาท)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data["rows"] as $v)
            <tr>
                <td>{!! $v["daily_category"] !!}</td>
                <td>{!! $v["stkcod"] !!}</td>
                <td>{!! $v["stkdes"] !!}</td>
                <td>{!! $v["unit"] !!}</td>
                <td class="text-end">{!! $v["qty"] !!}</td>
                <td class="text-end">{!! $v["price"] !!}</td>
            </tr>
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
