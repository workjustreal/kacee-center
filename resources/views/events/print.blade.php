<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>วันหยุดประจำปี</title>
    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }} " rel="stylesheet" type="text/css" />
</head>

<body>
    <h3 class="text-center">วันหยุดประจำปี</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">ลำดับ</th>
                <th scope="col">หัวข้อ</th>
                <th scope="col">เริ่ม</th>
                <th scope="col">สิ้นสุด</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($holiday as $list)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $list->title }}</td>
                    <td>{{ \Carbon\Carbon::parse($list->start)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($list->end)->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <script>
        window.onload = function() {
            window.onafterprint = window.close;
            window.print()
        };
    </script>
</body>
</html>