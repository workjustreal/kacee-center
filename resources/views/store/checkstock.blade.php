@extends('layouts.master-nopreloader-layout', ['page_title' => "เช็คสต๊อกสินค้า"])
@section('css')
<style>
    .tableFixHead {
        overflow-y: auto;
        height: 450px;
        background-color: #e6e6e6;
    }

    .tableFixHead table {
        border-collapse: collapse;
        width: 100%;
        background-color: #FFFFFF;
        margin-left: -1px !important;
    }

    .tableFixHead th,
    .tableFixHead td {
        padding: 8px 16px;
    }

    .tableFixHead thead {
        position: sticky;
        top: 0;
    }

    .h-divider {
        margin-top: 50px;
        margin-bottom: 10px;
        height: 1px;
        width: 100%;
        border-top: 1px solid #e6e6e6;
    }

    .input-xs {
        height: 25px;
        padding: 2px 5px;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 2px;
    }

    .btn-xs {
        height: 25px;
        padding: 2px 5px 2px 5px;
        font-size: 12px;
    }

    .tdPadding {
        padding: 3px !important;
    }

    .bgAlert {
        background-color: #ff0000;
    }

    .bgAlert2 {
        background-color: #FFC107;
    }

    .blinking {
        animation: blinkingBg 0.5s infinite;
    }

    .bg {
        min-height: 100%;
    }

    @keyframes blinkingBg {
        0% {
            background-color: #ff0000;
        }

        49% {
            background-color: transparent;
        }

        50% {
            background-color: transparent;
        }

        99% {
            background-color: transparent;
        }

        100% {
            background-color: #ff0000;
        }
    }
</style>
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Stock</a></li>
                        <li class="breadcrumb-item active">สต๊อกสินค้า</li>
                    </ol>
                </div>
                <h4 class="page-title">เช็คสต๊อกสินค้า</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-12 mb-2">
                            <div class="input-group">
                                <input type="text" class="form-control border border-primary" id="search" name="search"
                                    placeholder="QR Code" aria-label="QR Code" aria-describedby="btn-search"
                                    autocomplete="off" style="text-transform: uppercase;" onclick="$(this).select();" onfocus="$(this).css('background', 'yellow');" onblur="$(this).css('background', 'white');">
                                <button type="button" class="btn btn-outline-secondary border border-primary"
                                    id="btn-search" onclick="search();"><i class="fa fa-search"
                                        aria-hidden="true"></i></button>
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12 mb-2">
                            <div class="input-group">
                                <input type="number" class="form-control border border-primary" id="qty" name="qty"
                                    value="1" onclick="this.select()" onfocus="$(this).css('background', 'yellow');" onblur="$(this).css('background', 'white');" min="1" max="999">
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12 mb-2 align-self-center">
                            จำนวนรวม:&nbsp;<span id="total" class="fs-3 text-primary"></span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 mx-auto">
                            <center>
                                <div id="divTable" class="tableFixHead rounded">
                                    <table id="tableContent" class="table table-hover table-bordered">
                                        <thead class="bg-primary text-white border border-primary">
                                            <tr>
                                                <th style="width: 50px;" class="text-center">ลำดับ</th>
                                                <th style="width: 120px;" class="text-center">รหัสสินค้า
                                                </th>
                                                <th style="width: 170px;" class="text-center">ชื่อสินค้า
                                                </th>
                                                <th style="width: 140px;" class="text-center">บาร์โค้ด
                                                </th>
                                                <th style="width: 60px;" class="text-center">จำนวน</th>
                                                <th style="width: 100px;" class="text-center">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </center>
                            <br>
                            <label for="txt_remark">ระบุหมายเหตุ</label>
                            <input class="form-control" type="txt_remark" placeholder="ระบุหมายเหตุ" id="txt_remark"
                                name="txt_remark" value="{{ old('txt_remark') }}">
                            <br>
                            <div class="d-flex">
                                <form action="{{ Route('checkstock.reset') }}" method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary m-1">รีเซ็ต</button>
                                </form>
                                <form action="{{ Route('checkstock.save') }}" method="post">
                                    @csrf
                                    <input class="form-control" type="hidden" id="remark" name="remark"
                                        value="{{ old('remark') }}">
                                    <button type="submit" class="btn btn-primary m-1">บันทึก</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
            scanBarcode();
            loadData();
            $('#txt_remark').blur(function(event) {
                $('#remark').val(this.value);
            });
            setTimeout(() => {
                var success = '{{ session("message") }}';
                if (success == 'success') {
                    var file_name = '{{ session("file_name") }}';
                    var url = "{{ route('checkstock.download', ':file_name') }}";
                    url = url.replace(':file_name', file_name);
                    download(url, file_name);
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                }
            }, 800);
        });

        function scanBarcode() {
            var barcode = "";
            $("#search").keydown(function(e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (code == 13) { // Enter key hit
                    search();
                } else {
                    barcode = barcode + String.fromCharCode(code);
                }
            });
            $("#qty").keydown(function(e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (code == 13) {
                    $('#search').focus();
                }
            });
        }

        function scrollToPosition(id) {
            var elemDiv = document.getElementById('divTable');
            var elem = document.getElementById('index[' + id + ']');
            var sum = (getTopPos(elem) - getTopPos(elemDiv)) - 60;
            $('#divTable').animate({
                scrollTop: sum
            }, 100);
        }

        function scrollToBottom() {
            var height = $('#tableContent').height();
            $('#divTable').animate({
                scrollTop: height
            }, 100);
        }

        function getTopPos(el) {
            for (var topPos = 0; el != null; topPos += el.offsetTop, el = el.offsetParent);
            return topPos;
        }

        function loadData() {
            $.ajax({
                url: "{{ route('checkstock.loaddata') }}",
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    if (response.status == 1) {
                        $("tbody").empty();
                        $("tbody").append(response.detail);
                        $("#total").html(response.total);
                    } else {
                        $("#total").html(0);
                    }
                    $('#search').val('');
                    $('#qty').val(1);
                    setTimeout(() => {
                        $('#search').focus();
                    }, 500);
                }
            });
        }

        function search() {
            $.ajax({
                url: "{{ route('checkstock.data') }}",
                type: 'get',
                data: {
                    search: $('#search').val(),
                    qty: $('#qty').val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 0) {
                        Swal.fire({
                            icon: "warning",
                            title: response.msg,
                        }).then(function() {
                            $('#search').focus();
                        });
                    }
                    if (response.status == 1) {
                        $("tbody").empty();
                        $("tbody").append(response.detail);
                        $("#total").html(response.total);
                        if (response.new == 0) {
                            scrollToPosition(response.index);
                        } else if (response.new == 1) {
                            scrollToBottom();
                        }
                    }
                    $('#search').val('');
                    $('#qty').val(1);
                }
            });
        }

        function negative(id) {
            $.ajax({
                url: "{{ route('checkstock.negative') }}",
                type: 'get',
                data: {id: id},
                dataType: 'json',
                success: function(response) {
                    $("tbody").empty();
                    loadData();
                }
            });
        }

        function remove(id) {
            $.ajax({
                url: "{{ route('checkstock.remove') }}",
                type: 'get',
                data: {id: id},
                dataType: 'json',
                success: function(response) {
                    $("tbody").empty();
                    loadData();
                }
            });
        }

        const download = async (url, filename) => {
            const response = await fetch(url);
            const blob = await response.blob();
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            link.click();
        };
</script>
@endsection