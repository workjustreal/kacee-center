<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Barcode</title>
    <link href="{{URL::asset('assets/css/bootstrap.min.css')}} " rel="stylesheet" type="text/css"/>
    <style type="text/css">
        @media print {
            @page {
                size: A4; /* DIN A4 standard, Europe */
                margin: 10px;
                padding: 10px;
            }
            .box-label {
                border: none !important;
            }
            button {
                display: none !important;
            }
        }
        @page {
            size: auto;
            margin: 10px;
            padding: 10px;
        }
        .box-label {
            width: 210mm;
            /* height: 297mm; */
            margin: auto;
            padding: 10px;
            border: 1px solid #CCC;
        }
        .barcode {
            width: 140px;
            margin: auto;
            padding: 0;
            padding-top: 10px;
            /* margin-bottom: 8px; */
            text-align: center;
            /* border: 1px solid #000; */
        }
        .barcode > b.sku {
            font-size: 12px;
            color: #000;
            font-weight: bold;
            text-shadow:none;
        }
        table, th, td {
            page-break-before: always;
            border: 1px solid black;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }
        tr{
            page-break-inside: avoid;
            page-break-after: auto;
        }
    </style>
</head>

<body>
    <div class="box-label">
        @if($barcode)
        @php
            $numOfCols = 5;
            $rowCount = 0;
            $arrayCount = count($barcode);
        @endphp
        <table style="width:100%">
            <tr>
            @foreach ($barcode as $line)
                <td>
                    <div class="barcode">
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($line["barcode"], 'C128',1,45,array(1,1,1), true) }}" alt="barcode" />
                    <b class="sku">{{ $line["sku"] }}</b>
                    </div>
                </td>
                @php
                    $rowCount++;
                    if($rowCount % $numOfCols == 0 && $rowCount < $arrayCount) {
                        echo '</tr><tr>';
                    }
                @endphp
            @endforeach
        </table>
        @endif
    </div>
</body>

</html>