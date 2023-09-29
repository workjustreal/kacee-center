@extends('layouts.master-layout', ['page_title' => "เพิ่มระบบงาน"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
@endsection
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">ทดสอบ</li>
                    </ol>
                </div>
                <h4 class="page-title">ทดสอบกล้อง</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <div id="qr-reader" style="width:100%; max-width: 400px;"></div>
                        <div id="qr-reader-results"></div>
                        <div id="reader" style="width:100%; max-width: 400px;"></div>
                        <button type="button" id="btnStart" onclick="startScan()">Start</button>
                        <button type="button" id="btnStop" onclick="stopScan()">Stop</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('script')
    <!-- third party js -->
    <script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
    <script src="{{asset('assets/js/html5-qrcode.min.js')}}"></script>
    <!-- third party js ends -->
    <script type="text/javascript">
        $(document).ready(function(){
        });
        var resultContainer = document.getElementById('qr-reader-results');
        var lastResult, countResults = 0;
        function onScanSuccess(decodedText, decodedResult) {
            if (decodedText !== lastResult) {
                ++countResults;
                lastResult = decodedText;
                // Handle on success condition with the decoded message.
                console.log(`Scan result ${decodedText}`, decodedResult);
                resultContainer.innerHTML = lastResult;
                html5QrcodeScanner.pause(true);
                setTimeout(function(){
                    html5QrcodeScanner.resume();
                },1200);
            }
        }
        let config = {
            fps: 10,
            qrbox: {width: 220, height: 220},
            experimentalFeatures: {useBarCodeDetectorIfSupported: true},
            rememberLastUsedCamera: true,
            // Only support camera scan type.
            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
        };
        let html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", config);

        function startScan() {
            html5QrcodeScanner.render(onScanSuccess);
        }

        function stopScan() {
            html5QrcodeScanner.clear();
        }
    </script>
@endsection