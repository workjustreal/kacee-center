@extends('layouts.master-layout', ['page_title' => "พิมพ์ใบปะหน้าพัสดุ"])
@section('css')
<link href="{{ asset('assets/css/print.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                        <li class="breadcrumb-item active">ใบปะหน้าพัสดุ</li>
                    </ol>
                </div>
                <h4 class="page-title">พิมพ์ใบปะหน้าพัสดุ</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6 col-xl-4">
                            <div class="mb-3 text-center">
                                <input type="text" class="form-control text-center"
                                    placeholder="หมายเลข SO (SALE ORDER)" name="search" id="search" autocomplete="off"
                                    style="text-transform: uppercase">
                            </div>
                            <div class="text-center">
                                <button type="submit" id="btn-search" name="btn-search" class="btn btn-dark w-100">ค้นหา
                                    และ พิมพ์</button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row justify-content-center">
                        <div class="col-md-12 col-lg-12 col-xl-12">
                            <div id="div_msg" class="text-center border" style="height: 400px;">
                                <br><br><br><br><br><br><br>
                                <h1 id="text_msg"><span class="text-muted">ใบปะหน้าพัสดุ</span></h1>
                            </div>
                            <div id="label_area" class="text-center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/print.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
            setTimeout(() => {
                $('#search').focus();
            }, 500);
            $('#search').keypress(function(event) {
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if (keycode == '13') {
                    search();
                }
            });
            $('#search').click(function(event) {
                $(this).select();
            });
            $('#btn-search').click(function(event) {
                search();
            });
        });
        document.onkeydown = function (t) {
            if(t.which == 9){
                return false;
            }
        }
        function searchAgain(print) {
            search(print);
        }
        function search(print = "") {
            var search = $("#search").val();
            var div_msg = $("#div_msg");
            var text_msg = $("#text_msg");
            document.getElementById("label_area").innerHTML = '';
            div_msg.show();
            if (search == "") {
                text_msg.html('<span class="text-danger">ตรวจสอบหมายเลขคำสั่งซื้อ</span>');
                return false;
            } else {
                text_msg.html('<div class="spinner-grow avatar-md text-secondary" role="status"></div><br><span class="text-muted">กำลังค้นหา...</span>');
            }
            $.ajax({
                url: "{{ Route('shipping.search') }}",
                method: 'GET',
                data: {
                    search: search,
                    print: print
                },
                dataType: 'json',
                success: function(res) {
                    // console.log(res);
                    if (res.success == true) {
                        // div_msg.hide();
                        // var objFra = document.createElement('iframe'); // Create an IFrame.
                        // objFra.id = 'iframe';
                        // // objFra.style.visibility = "hidden"; // Hide the frame.
                        // objFra.src = 'data:application/pdf;base64,'+res.file; // Set source not done .pdf.
                        // objFra.onload = function(){
                        //     objFra.style.width = '400px';
                        //     objFra.style.height = '600px';
                        //     objFra.contentWindow.focus(); // Set focus.
                        //     $('#search').focus();
                        //     $('#search').select();
                        // };
                        // document.getElementById("label_area").appendChild(objFra); // Add the frame to the web page.

                        // var btnPrint = document.createElement('button');
                        // btnPrint.innerText = "Print PDF";
                        // btnPrint.style.display = "none";
                        // btnPrint.click = printJS({printable: res.file, type: 'pdf', base64: true});
                        // document.getElementById("label_area").appendChild(btnPrint);


                        // div_msg.hide();
                        var objFra = document.createElement('iframe'); // Create an IFrame.
                        objFra.id = 'iframe';
                        objFra.style.visibility = "hidden"; // Hide the frame.
                        objFra.src = res.file; // Set source not done .pdf.
                        // if (res.platform_id == 2) {
                        //     objFra.srcdoc = res.file; // Set source not done html. Lazada
                        // } else {
                        //     objFra.src = res.file; // Set source not done .pdf.
                        // }
                        objFra.onload = function(){
                            objFra.style.width = '400px';
                            objFra.style.height = '600px';
                            objFra.contentWindow.focus(); // Set focus.
                            $('#search').focus();
                            $('#search').select();
                        };
                        document.getElementById("label_area").appendChild(objFra); // Add the frame to the web page.
                        setTimeout(() => {
                            // div_msg.show();
                            text_msg.html('<span class="text-success">เสร็จสิ้น</span>');
                            document.getElementById("iframe").contentWindow.focus();
                            document.getElementById("iframe").contentWindow.print();
                        }, 500);
                    } else {
                        div_msg.show();
                        if ('print_count' in res) {
                            Swal.fire({
                                title: "คุณต้องการพิมพ์อีกครั้ง ใช่ไหม?",
                                text: res.message,
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "ยืนยัน!",
                                cancelButtonText: "ยกเลิก",
                            }).then((willPrint) => {
                                if (willPrint.isConfirmed) {
                                    searchAgain("print_again");
                                }
                            });
                        }
                        text_msg.html(res.message);
                        $('#search').focus();
                        $('#search').select();
                    }
                }
            });
        }
</script>
@endsection