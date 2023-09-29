<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>QRCODE PRINTOUT</title>
    <link href="{{ URL::asset('assets/css/printout.css') }} " rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="container page-32x16 border border-dark">
        @foreach ($qrcode as $qrcode)
            @if ($loop->index % 3 == 0)
                <div style="page-break-after: always;">
                    <div class="row">
            @endif
            @if ($loop->index % 3 == 0)
                <div class="column sticker32x16 s-left">
                @elseif ($loop->index % 3 == 2)
                    <div class="column sticker32x16 s-right">
                    @else
                        <div class="column sticker32x16 s-center">
            @endif
            <table class="table-qrcode">
                <tr style="line-height: 10px;">
                    <td class="product-qrcode">
                        @php
                            echo '<div>' . DNS2D::getBarcodeSVG($qrcode['barcode'], 'QRCODE', 2, 2) . '</div>';
                        @endphp
                    </td>
                </tr>
                <tr>
                    <td class="product-font">{{ $qrcode['sku'] }}</td>
                </tr>
            </table>
            </div>
            @if ($loop->index % 3 == 2)
                </div>
                </div>
            @endif
        @endforeach
    </div>
    <script type="text/javascript">
        window.onafterprint = window.close;
        window.print();
    </script>
</body>

</html>
