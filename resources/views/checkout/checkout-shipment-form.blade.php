@extends('layouts.master-layout', ['page_title' => 'เช็คเอาท์การจัดส่ง'])
@section('css')
    <link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        /* #checkoutLog tbody tr:first-child {
            background: #f8f9c2 !important;
        } */
        .firstTR {
            background: #f8f9c2 !important;
        }
        .bg-success-c2 {
            background-color: #2eef93 !important;
            color: #037b46;
        }
        #sig-canvas {
            border: 2px dotted #CCCCCC;
            border-radius: 15px;
            cursor: crosshair;
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                            <li class="breadcrumb-item active">เช็คเอาท์</li>
                        </ol>
                    </div>
                    <h4 class="page-title">เช็คเอาท์การจัดส่ง</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body p-2">
                        @php
                            $checkout_data = (!isset($checkoutheader)) ? 0 : 1;
                        @endphp
                        @if ($checkout_data == 0)
                            <div class="row justify-content-center">
                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <form action="{{ route('checkout-shipment.submit') }}" method="POST"
                                        enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="mb-3 text-center">
                                            <label class="form-label">เลือกขนส่ง</label>
                                            <select class="form-select" id="ship_com" name="ship_com" required>
                                                @foreach ($ship_com as $list)
                                                <option value="{{ $list->id }}" {{ ($list->id==1) ? 'selected' : ''}}>{{ $list->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3 text-center">
                                            <input type="text" class="form-control text-center" placeholder="ทะเบียนรถ"
                                                name="vehicle_registration" id="vehicle_registration" autocomplete="off" required>
                                        </div>
                                        <div class="mb-3 text-center">
                                            <input type="text" class="form-control text-center" placeholder="หมายเหตุ"
                                                name="remark" id="remark" autocomplete="off">
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" id="btn-search" name="btn-search"
                                                class="btn btn-warning waves-effect waves-light w-100">ตกลง</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- Edit Detail Modal -->
                            <div id="editModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="editModalLabel">แก้ไขข้อมูล</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 mb-1 text-center">
                                                    <label for="running" class="form-label">เลขรัน</label><br>
                                                    <input type="text" class="form-control text-center text-muted border-0" name="running" id="running" value="{{ $checkoutheader->running }}" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 mb-1 text-center">
                                                    <label for="vehicle_registration" class="form-label">ทะเบียนรถ</label><br>
                                                    <input type="text" class="form-control text-center" placeholder="ทะเบียนรถ"
                                                name="vehicle_registration" id="vehicle_registration" value="{{ $checkoutheader->vehicle_registration }}" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 mb-1 text-center">
                                                    <label for="remark" class="form-label">หมายเหตุ</label><br>
                                                    <input type="text" class="form-control text-center" placeholder="หมายเหตุ"
                                                name="remark" id="remark" value="{{ $checkoutheader->remark }}" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary waves-effect waves-light" data-bs-dismiss="modal">ปิด</button>
                                            <button type="button" class="btn btn-primary waves-effect waves-light" onclick="editHeader();" data-bs-dismiss="modal">บันทึก</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xl-12">
                                    <div class="float-end">
                                        <a type="button" class="btn btn-secondary waves-effect waves-light" href="{{ url('/checkout/shipment-history') }}"><i class="mdi mdi-keyboard-backspace"></i></a>
                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#editModal"><i class="mdi mdi-pencil"></i></button><br>
                                        <button type="button" class="btn btn-success waves-effect waves-light float-end my-1" data-bs-toggle="modal" data-bs-target="#signature-modal"><i class="mdi mdi-signature-freehand"></i></button>
                                    </div>
                                    <h5>เลขรัน: <b class="text-blue">{{ $checkoutheader->running }}</b></h5>
                                    <h5>ทะเบียนรถ: <b class="text-blue">{{ $checkoutheader->vehicle_registration }}</b></h5>
                                    <h5>ขนส่ง: <b class="text-blue">{{ $checkoutheader->ship_com_name }}</b></h5>
                                    @if ($checkoutheader->remark != "")
                                        <h5>หมายเหตุ: <b class="text-blue">{{ $checkoutheader->remark }}</b></h5>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xl-12">
                                    <div class="pb-1 text-center border-bottom">
                                        <input type="text" class="form-control text-center" placeholder="หมายเลขขนส่ง"
                                            name="trackingnumber" id="trackingnumber" autocomplete="off" onclick="$(this).select()" onfocus="$(this).css('background', 'yellow');" onblur="$(this).css('background', 'white');" inputmode="none">
                                    </div>
                                    <div class="d-flex justify-content-between mx-1">
                                        <h5><i id="reloading" class="fas fa-sync-alt" onclick="get_data();"></i></h5>
                                        <h5 class="text-primary" id="total"></h5>
                                        <h5 class="text-blue">วันที่ {{ \Carbon\Carbon::parse($checkoutheader->checkout_date)->format('d/m/Y') }}</h5>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="checkoutLog" class="table table-sm w-100 fs-6" data-page-size="25" data-pagination="true" data-search="false">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10px;">#</th>
                                                    <th>หมายเลขขนส่ง</th>
                                                    <th>หมายเลข SO</th>
                                                    <th>แพ็คเกจ</th>
                                                    <th>ร้านค้า</th>
                                                    <th>เวลา</th>
                                                    <th>#</th>
                                                </tr>
                                            </thead>
                                            <thead class="subthead"></thead>
                                            <tbody></tbody>
                                            <tfoot></tfoot>
                                        </table>
                                    </div>
                                    <div class="text-center mt-2"><p class="text-pink">***แสดงสูงสุด 50 รายการ***</p></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="toast-container position-fixed bottom-0 end-0 p-3 mb-0" style="z-index: 11">
            <div id="toastAlert" class="toast align-items-center bg-success-c2" role="alert" aria-live="polite" aria-atomic="true" data-bs-delay="2000">
                <div class="d-flex">
                    <div class="toast-body">
                        <span id="action_msg"></span>
                    </div>
                    <button type="button" class="btn-close btn-close-white waves-effect waves-light me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <!-- Signature Modal -->
        <div id="signature-modal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="signature-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="signature-modalLabel">เซ็นรับของ
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 text-center">
                                <canvas id="sig-canvas" width="300" height="120" class="@if(isset($checkoutheader->signature)) hidd @endif">
                                    Get a better browser, bro.
                                </canvas>
                            </div>
                        </div>
                        @if ($checkout_data == 1)
                        <div class="row">
                            <div class="col-12 text-center">
                                <img id="sig-image" src="@if(strlen($checkoutheader->signature)>0){{ URL::asset('assets/images/signature').'/'.$checkoutheader->signature }} @endif" class="border @if(strlen($checkoutheader->signature)<=0) hidd @endif" alt="Your signature will go here!"/>
                            </div>
                        </div>
                        @endif
                        <div class="row hidd">
                            <div class="col-12 mb-3">
                                <textarea id="sig-dataUrl" class="form-control" rows="5">Data URL for your signature will go here!</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-center mt-1 mb-3">
                                <button class="btn btn-success waves-effect waves-light hidd" id="sig-submitBtn">ยืนยันลายเซ็น</button>
                                <button class="btn btn-warning waves-effect waves-light @if(isset($checkoutheader->signature)) hidd @endif" id="sig-clearBtn">ล้าง</button>
                                <button class="btn btn-success waves-effect waves-light @if(isset($checkoutheader->signature)) hidd @endif" onclick="submit_signature()">บันทึก</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Select SO Modal -->
        <div id="select-modal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            role="dialog" aria-labelledby="select-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="select-modalLabel">หมายเลข SO แบบงานชุด
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <h4 class="text-center">หมายเลขขนส่ง<p id="tracking-select" class="text-primary"></p></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <h4 class="text-center">เลือกหมายเลข SO</h4>
                                <div id="so-select"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submit_checkbox()">บันทึก</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Info Alert Modal -->
        <div id="info-status-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <div class="text-center">
                            <i class="dripicons-information h1 text-info"></i>
                            <h4 class="mt-2 info-status"></h4>
                            <p class="mt-3 info-status-detail"></p>
                            <button type="button" class="btn btn-info waves-effect waves-light my-2" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <audio class="hidd" id="beepsound" controls>
            <source src="{{ URL::asset('assets/sound/scanner-beeps-barcode.mp3') }}" type="audio/mpeg">
            Your browser does not support the audio tag.
        </audio>
        <audio class="hidd" id="errorsound" controls>
            <source
                src="{{ URL::asset('assets/sound/Error-beep-sound-effect.mp3') }}"
                type="audio/mpeg">
            Your browser does not support the audio tag.
        </audio>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
    <script src="{{asset('assets/js/signature.js')}}"></script>
    <script type="text/javascript">
        var mySelectModal = new bootstrap.Modal(document.getElementById('select-modal'));
        var myStatusModal = new bootstrap.Modal(document.getElementById('info-status-modal'));
        $(document).ready(function() {
            var checkout_data = "{{ $checkout_data }}";
            setTimeout(() => {
                if (checkout_data == 1) {
                    $('#trackingnumber').focus();
                    get_data();
                } else {
                    $('#vehicle_registration').focus();
                }
            }, 500);
            $('#trackingnumber').keypress(function(event) {
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if (keycode == '13') {
                    search();
                }
            });
        });
        document.onkeydown = function (t) {
            if(t.which == 9){
                return false;
            }
        }

        function beepSoundPlay() {
            const audio = document.querySelector("#beepsound");
            audio.play();
            setTimeout(function(){
                audio.pause();
                audio.currentTime = 0;
            },1200);
        }

        function errorSoundPlay() {
            const audio = document.querySelector("#errorsound");
            audio.play();
            setTimeout(function(){
                audio.pause();
                audio.currentTime = 0;
            },1200);
        }

        function successMsg(msg) {
            beepSoundPlay();
            var myToastEl = document.getElementById('toastAlert');
            var myToast = bootstrap.Toast.getInstance(myToastEl);
            document.getElementById('action_msg').innerHTML = msg;
            myToast.show();
            clearTrackingnumber();
        }

        function clearTrackingnumber() {
            $('#trackingnumber').val('');
            $('#trackingnumber').focus();
        }

        function delConfirmationCheckoutShipmentItem(id, so) {
            errorSoundPlay();
            Swal.fire({
                title: "คุณต้องการลบ ใช่ไหม?",
                html: "หมายเลข SO: "+so+"<br>ลบครั้งละ 1 แพ็คเกจ",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ดำเนินการลบ!",
                cancelButtonText: "ยกเลิก",
            }).then((willDelete) => {
                if (willDelete.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': '{{csrf_token()}}'
                        }
                    });
                    $.ajax({
                        url: "{{ url('checkout/shipment-del-item') }}/"+id,
                        method: 'GET',
                        dataType: 'json',
                        success: function(res) {
                            if (res.success == true) {
                                Swal.fire({
                                    icon: "success",
                                    title: res.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                setTimeout(() => {
                                    get_data();
                                }, 1500);
                            } else {
                                errorSoundPlay();
                                Swal.fire({
                                    icon: "warning",
                                    title: res.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                            }
                        }
                    });
                }
            });
        }

        function editHeader() {
            var vehicle_registration = $("#vehicle_registration").val();
            if (vehicle_registration == "") {
                errorSoundPlay();
                Swal.fire({
                    icon: "warning",
                    title: "ใส่ทะเบียนรถ",
                    timer: 2000,
                    showConfirmButton: false,
                });
                return false;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });
            $.ajax({
                url: "{{ Route('checkout-shipment.update') }}",
                method: 'POST',
                data: {
                    running: $("#running").val(),
                    vehicle_registration: $("#vehicle_registration").val(),
                    remark: $("#remark").val(),
                },
                dataType: 'json',
                success: function(res) {
                    // console.log(res);
                    if (res.success == true) {
                        Swal.fire({
                            icon: "success",
                            title: res.message,
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        successMsg(res.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        errorSoundPlay();
                        Swal.fire({
                            icon: "warning",
                            title: res.message,
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        clearTrackingnumber();
                    }
                }
            });
        }

        function isCanvasEmpty(canvas) {
            const blankCanvas = document.createElement('canvas');
            blankCanvas.width = canvas.width;
            blankCanvas.height = canvas.height;
            return canvas.toDataURL() === blankCanvas.toDataURL();
        }

        function submit_signature() {
            document.getElementById("sig-submitBtn").click();
            setTimeout(() => {
            var canvas = document.getElementById('sig-canvas');
            if (isCanvasEmpty(canvas)){
                errorSoundPlay();
                Swal.fire({
                    icon: "warning",
                    title: "โปรดเซ็นรับของ",
                    timer: 2000,
                    showConfirmButton: false,
                });
                return false;
            }
            var sig_dataUrl = $("#sig-dataUrl").val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });
            $.ajax({
                url: "{{ Route('checkout-shipment.signature') }}",
                method: 'POST',
                data: {
                    running: $("#running").val(),
                    data_url: $("#sig-dataUrl").val(),
                },
                dataType: 'json',
                success: function(res) {
                    // console.log(res);
                    if (res.success == true) {
                        Swal.fire({
                            icon: "success",
                            title: res.message,
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        successMsg(res.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        errorSoundPlay();
                        Swal.fire({
                            icon: "warning",
                            title: res.message,
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        clearTrackingnumber();
                    }
                }
            });
            }, 500);
        }

        function submit_checkbox() {
            if (!$("input[name='checkout_checkbox']:checked").val()) {
                errorSoundPlay();
                Swal.fire({
                    icon: "warning",
                    title: "โปรดเลือกหมายเลข SO",
                    timer: 2000,
                    showConfirmButton: false,
                });
                return false;
            } else {
                var checkout_checkbox = $("input[name='checkout_checkbox']:checked").map(function() {return $(this).val();}).get().join(",");
                if (checkout_checkbox.split(",").length > 0) {
                    search(checkout_checkbox, "");
                }
            }
        }

        function search(sonumber = "", addpackaging = "") {
            var trackingnumber = $.trim($("#trackingnumber").val());
            $("#trackingnumber").val(trackingnumber.split(" ")[0]);
            var trackingnumber = $("#trackingnumber").val();
            if (trackingnumber == "") {
                errorSoundPlay();
                Swal.fire({
                    icon: "warning",
                    title: "ใส่หมายเลขขนส่ง",
                    timer: 2000,
                    showConfirmButton: false,
                });
                return false;
            } else {
                $("#reloading").addClass("fa-spin");
            }
            $.ajax({
                url: "{{ Route('checkout-shipment.search') }}",
                method: 'GET',
                data: {
                    trackingnumber: trackingnumber,
                    sonumber: sonumber,
                    addpackaging: addpackaging,
                    running: $("#running").val(),
                    vehicle_registration: $("#vehicle_registration").val(),
                    remark: $("#remark").val(),
                },
                dataType: 'json',
                success: function(res) {
                    // console.log(res);
                    if (res.success == true) {
                        $("#reloading").removeClass("fa-spin");
                        if ('result' in res) {
                            errorSoundPlay();
                            var htmltracking = '';
                            var html = '';
                            for (var i=0; i<res.result.items_count; i++) {
                                html += btnSelection(res.result.data[i].checkout, res.result.data[i].so_status, res.result.data[i].so, i);
                                htmltracking = res.result.data[i].trackingnumber;
                            }
                            $('#so-select').html(html);
                            $('#tracking-select').html(htmltracking);
                            mySelectModal.show();
                            return false;
                        }
                        if ('confirm' in res) {
                            errorSoundPlay();
                            confirmAdd(res.so);
                            return false;
                        }
                        mySelectModal.hide();
                        successMsg(res.message);
                        get_data();
                    } else {
                        errorSoundPlay();
                        $("#reloading").removeClass("fa-spin");
                        Swal.fire({
                            icon: "warning",
                            title: res.message,
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        clearTrackingnumber();
                    }
                }
            });
        }

        function confirmAdd(sonumber) {
            if (sonumber != "") {
                Swal.fire({
                    title: "ยืนยันการเช็คเอาท์?",
                    html: "SO ที่เลือกบางรายการถูกเช็คเอาท์แล้ว<br>ต้องการเพิ่มจำนวนแพ็คเกจหรือไม่?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "ยืนยัน!",
                    cancelButtonText: "ยกเลิก",
                }).then((willConfirm) => {
                    if (willConfirm.isConfirmed) {
                        mySelectModal.hide();
                        search(sonumber, "add");
                    }
                });
            }
        }

        function btnSelection(checkout, so_status, so, item) {
            var disabled = '';
            var bg = '';
            var status_text = '';
            if (checkout == true) {
                bg = 'success';
                status_text = '(เช็คเอาท์แล้ว)';
            } else {
                if (so_status == "notfound") {
                    disabled = 'disabled';
                    bg = 'dark';
                    status_text = '(ไม่พบหมายเลข SO)';
                } else if (so_status == "cancel") {
                    disabled = 'disabled';
                    bg = 'danger';
                    status_text = '(SO ถูกยกเลิก)';
                } else if (so_status == "notcheckin") {
                    disabled = 'disabled';
                    bg = 'secondary';
                    status_text = '(SO ยังไม่ได้เช็คอิน)';
                } else if (so_status == "checkout") {
                    disabled = 'disabled';
                    bg = 'success';
                    status_text = '(SO ถูกเช็คเอาท์ไปแล้ว)';
                } else if (so_status == "checkin") {
                    bg = 'warning';
                    status_text = '(เลือกเพื่อเช็คเอาท์)';
                }
            }
            var btn = `<label class="btn btn-`+bg+` waves-effect waves-light w-100 mb-1 `+disabled+`">
                            <div class="form-check mb-1 form-check-`+bg+`">
                                <div class="d-flex justify-content-start">
                                    <input class="form-check-input bg-`+bg+` border-0" type="checkbox" id="checkout_checkbox`+item+`" name="checkout_checkbox" value="`+so+`" autocomplete="off" style="width: 25px; height: 25px;">
                                    <span class="mx-2 mt-1">`+so+` `+status_text+`</span>
                                </div>
                            </div>
                        </label>`;
            return btn;
        }

        function infoStatus(trackingnumber, checkout_count, so_count, packaging_qty) {
            var info = (checkout_count == so_count) ? '<span class="bg-success text-white rounded-pill px-2">ครบแล้ว</span>' : '<span class="bg-danger text-white rounded-pill px-2">ยังไม่ครบ</span>';
            var detail = 'หมายเลขขนส่ง: <b class="text-primary">'+trackingnumber+'</b><br>มีหมายเลข SO: <b class="text-primary">'+so_count+' รายการ</b><br>แพ็คเกจ: <b class="text-primary">'+packaging_qty+' รายการ</b><br>เช็คเอาท์แล้ว <b class="text-primary">'+checkout_count+' รายการ</b>';
            $(".info-status").html(info);
            $(".info-status-detail").html(detail);
            myStatusModal.show();
        }

        function get_data() {
            $("#reloading").addClass("fa-spin");
            $.ajax({
                url: "{{ Route('checkout-shipment.data') }}",
                method: 'GET',
                data: {
                    running: $("#running").val(),
                    vehicle_registration: $("#vehicle_registration").val(),
                    remark: $("#remark").val(),
                },
                dataType: 'json',
                success: function(json) {
                    // console.log(json);
                    var active = 1;
                    var tracking = "";
                    var bg = "table-secondary text-secondary";
                    var html = '';
                    for (var i = 0; i < json.data.length; i++) {
                        if (i > 0) {
                            if (json.data[i].trackingnumber != tracking) {
                                tracking = json.data[i].trackingnumber;
                                bg = (bg=="") ? "table-secondary text-secondary" : "";
                            }
                            active = 1;
                        } else {
                            bg = "firstTR";
                        }
                        var status_bg = (json.data[i].checkout_count == json.data[i].so_count) ? 'text-success' : 'text-danger';
                        var onclick = `onclick="infoStatus(\'`+json.data[i].trackingnumber+`\', `+json.data[i].checkout_count+`, `+json.data[i].so_count+`, `+json.data[i].packaging_qty+`)"`;
                        var date = new Date(json.data[i].updated_at);
                        var updated_at = date.getHours().toString().padStart(2, "0") + ':' + date.getMinutes().toString().padStart(2, "0") + ':' + date.getSeconds().toString().padStart(2, "0");
                        html += `<tr class="`+bg+`">`;
                        html += `<td `+onclick+`><i class="fas fa-circle `+status_bg+` fs-5"></i></td>`;
                        html += `<td `+onclick+`><span class="d-block">`+json.data[i].trackingnumber+`</span></td>`;
                        html += `<td `+onclick+`><span class="d-block">`+json.data[i].so+`</span></td>`;
                        html += `<td `+onclick+`><span class="d-block">`+json.data[i].packaging_qty+`</span></td>`;
                        html += `<td `+onclick+`><span class="d-block">`+json.data[i].eplatform_name+`</span></td>`;
                        html += `<td `+onclick+`><span class="d-block">`+updated_at+`</span></td>`;
                        html += `<td><i class="fas fa-trash text-danger fs-5" onclick="delConfirmationCheckoutShipmentItem(`+json.data[i].id+`, '`+json.data[i].so+`')"></i></td>`;
                        html += `</tr>`;
                    }
                    $('tbody').html(html);
                    if (json.recordsTotal > 0) {
                        $("#total").html(json.recordsTotal + " รายการ");
                        htmlFoot = `<tr class="text-dark">`;
                        htmlFoot += `<td><i class="fas fa-asterisk fs-5"></i></td>`;
                        htmlFoot += `<td><b>`+json.dataSumTracking+`</b></td>`;
                        htmlFoot += `<td><b>`+json.dataSumSO+`</b></td>`;
                        htmlFoot += `<td><b>`+json.dataSumPackaging+`</b></td>`;
                        htmlFoot += `<td></td>`;
                        htmlFoot += `<td></td>`;
                        htmlFoot += `<td></td>`;
                        htmlFoot += `</tr>`;
                    } else {
                        htmlFoot = '';
                    }
                    $('.subthead').html(htmlFoot);
                    $('tfoot').html(htmlFoot);
                    $("#reloading").removeClass("fa-spin");
                    // setTimeout(get_data, 5000);
                }
            });
            clearTrackingnumber();
        }
    </script>
@endsection
