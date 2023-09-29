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
            @page {size: A4 landscape; }
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
    <h4 class="text-center">{!! $data["header"]["title"] !!}</h4>
    <table class="table table-sm table-bordered text-nowrap text-dark">
        <thead class="border border-dark">
            <tr>
                <th rowspan="2" class="align-middle text-center">กลุ่มสินค้า</th>
                <th rowspan="2" class="align-middle text-center">ยอดรวมทั้งหมด</th>
                @php $cols = []; @endphp
                @foreach ($data["thead"]["header"] as $value)
                @php $cols[] = $value["colspan"]; @endphp
                <th @if($value["colspan"] > 1) colspan="{{ $value["colspan"] }}" @endif class="text-center">{{ $value["display_name"] }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($data["thead"]["subheader"] as $value)
                <th class="text-center">{{ $value["display_name"] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data["rows"] as $v)
            <tr style="@if(($loop->index+1) == count($data["rows"])) border-bottom: 1px solid black; @endif @if(($loop->index+1) == count($data["rows"]) || ($loop->index+2) == count($data["rows"])) font-weight: bold; @endif">
                <td class="border-bottom-0 border-dark">{!! $v["category"] !!}</td>
                <td class="text-center border-bottom-0 border-dark">{!! $v["summary"] !!}</td>
                @php $i = 0;$c = 0; @endphp
                @foreach ($data["rowlist"] as $list)
                    @php
                    $is_border = false;
                    if (($i+1) == $cols[$c]) {
                        $is_border = true;
                        $i=0;
                        $c++;
                    } else {
                        $i++;
                    }
                    @endphp
                    <td class="text-center @if($is_border) border-0 border-end border-dark @endif">{!! $v[$list["name"]] !!}</td>
                @endforeach
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
