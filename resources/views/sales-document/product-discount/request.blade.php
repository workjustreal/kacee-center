@extends('layouts.master-layout', ['page_title' => 'สร้างคำขอ'])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/selectize/selectize.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->
    <style>
        #btn-request {
            margin-bottom: -48px;
        }

        ul .active {
            background-color: #f3f7f9 !important;
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Product Discount</a></li>
                            <li class="breadcrumb-item active">Request</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12 offset-lg-3 col-lg-6">
                <div class="card border-primary border">
                    <div class="card-header bg-primary text-center">
                        <h4 class="text-white">สร้างคำขอ</h4>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" method="post" id="create_request"
                            action="{{ route('request_create') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-lg-6 mt-2">
                                    <div class="form-group ">
                                        <label class="control-label">รหัสลูกค้า</label>
                                        <input type="text" name="customer_code" id="customer_code"
                                            class="form-control form-control-md form-control-required" autocomplete="off"
                                            required placeholder="กรุณากรอกรหัสลูกค้า" value="{{ old('customer_code') }}" />
                                        <div id="suggesstion-box"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-2">
                                    <div class="form-group ">
                                        <label class="control-label">ชื่อร้าน</label>
                                        <input type="text"
                                            class="form-control form-control-md form-control-required bg-light"
                                            id="customer_name" name="customer_name" placeholder="กรุณากรอกชื่อร้าน" readonly
                                            autocomplete="off" value="{{ old('customer_name') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mt-2">
                                    <div class="form-group ">
                                        <label class="control-label">สถานะลูกค้า</label>
                                        <input type="text"
                                            class="form-control form-control-md form-control-required bg-light" readonly
                                            id="customer_status" name="customer_status" placeholder="กรุณาเลือกสถานะลูกค้า"
                                            autocomplete="off" value="{{ old('customer_status') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-2">
                                    <div class="form-group ">
                                        <label class="control-label">วงเงินอนุมัติ</label>
                                        <input type="number"
                                            class="form-control form-control-md form-control-required bg-light" readonly
                                            id="limit" name="limit" placeholder="กรุณากรอกวงเงิน"
                                            value="{{ old('limit') }}" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-2">
                                <div class="form-group ">
                                    <label class="control-label">เจ้าหน้าที่ขายที่รับผิดชอบ</label>
                                    <input type="text"
                                        class="form-control form-control-md form-control-required bg-light" readonly
                                        id="staf" name="staf" placeholder="กรุณากรอกชื่อเจ้าหน้าที่"
                                        value="{{ old('staf') }}" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <label class="mt-3">สาเหตุ</label>
                                </div>
                                <div class="col-11">
                                    <div class="row">
                                        <div class="col-lg-5 mt-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="mistake" id="mistake01"
                                                    value="ความผิดพลาดจากการผลิตสินค้า"
                                                    @if (old('mistake') == 'ความผิดพลาดจากการผลิตสินค้า' || old('mistake') == '') checked @endif>
                                                <label class="form-check-label"
                                                    for="mistake01">ความผิดพลาดจากการผลิตสินค้า</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mt-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="mistake"
                                                    id="mistake02" value="ความผิดพลาดจากการรับคำสั่งซื้อ"
                                                    @if (old('mistake') == 'ความผิดพลาดจากการรับคำสั่งซื้อ') checked @endif>
                                                <label class="form-check-label"
                                                    for="mistake02">ความผิดพลาดจากการรับคำสั่งซื้อ</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 mt-2">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="mistake"
                                                    id="mistake03" value="ความผิดพลาดจากการขนส่งสินค้า"
                                                    @if (old('mistake') == 'ความผิดพลาดจากการขนส่งสินค้า') checked @endif>
                                                <label class="form-check-label"
                                                    for="mistake03">ความผิดพลาดจากการขนส่งสินค้า</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mt-2">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="mistake"
                                                    id="mistake04" value="ความผิดพลาดจากสินค้าและอุปกรณ์"
                                                    @if (old('mistake') == 'ความผิดพลาดจากสินค้าและอุปกรณ์') checked @endif>
                                                <label class="form-check-label"
                                                    for="mistake04">ความผิดพลาดจากสินค้าและอุปกรณ์
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 mt-2">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="mistake"
                                                    id="mistake05" value="ความผิดพลาดที่เกิดจากลูกค้าเอง"
                                                    @if (old('mistake') == 'ความผิดพลาดที่เกิดจากลูกค้าเอง') checked @endif>
                                                <label class="form-check-label"
                                                    for="mistake05">ความผิดพลาดที่เกิดจากลูกค้าเอง</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mt-2">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="mistake"
                                                    id="other" value="other"
                                                    @if (old('mistake') == 'other') checked @endif>
                                                <label class="form-check-label" for="other">อื่นๆ</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-2">
                                <div class="fom-group f_comment" @if (old('mistake') != 'other') hidden @endif>
                                    <label class="control-label">สาเหตุอื่นๆ</label>
                                    <textarea class="form-control form-control-md form-control-required" id="note" name="note"
                                        placeholder="กรอกหมายเหตุ" rows="7">{{ old('note') }}</textarea>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-2">
                                <div class="form-group ">
                                    <label class="control-label">เลขที่บิล</label>
                                    <input type="text" class="form-control form-control-md form-control-required"
                                        id="invoice" name="invoice" placeholder="กรุณากรอกเลขที่ IV"
                                        @if (old('invoice')) value="{{ old('invoice') }}" @else value="IV" @endif
                                        autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-2">
                                <div class="form-group ">
                                    <label class="control-label">รายการสินค้า</label>
                                    <input type="text" class="form-control form-control-md form-control-required"
                                        id="product_list" name="product_list" placeholder="กรุณากรอกรายการสินค้า"
                                        autocomplete="off" value="{{ old('product_list') }}" required>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-2">
                                <div class="form-group ">
                                    <label class="control-label">ยอดเงินที่ขอชดเชย / ค่าเสียหายที่ลูกค้าร้องขอ</label>
                                    <input type="text" class="form-control form-control-md form-control-required"
                                        id="customer_request" name="customer_request" placeholder="กรุณากรอกยอดเงิน"
                                        value="{{ old('customer_request') }}" required autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-12 mt-2">
                                <div class="fom-group">
                                    <label class="control-label">รายละเอียด</label>
                                    <textarea class="form-control form-control-md form-control-required" id="description" name="description"
                                        placeholder="กรอกรายละเอียด" rows="7">{{ old('description') }}</textarea>
                                </div>
                            </div>
                            <hr>
                            <h5 class="card-title mt-3 text-primary text-decoration-underline">ส่วนแนบไฟล์</h5>
                            <div class="col-12 mt-2 mb-2">
                                @error('file')
                                    <div class="alert alert-danger mt-1 mb-1">กรุณาตรวจสอบไฟล์</div>
                                @enderror
                                <label class="control-label">ไฟล์<span class="text-blue">
                                        (เฉพาะไฟล์นามสกุล .csv/.txt/.xlx/.xls/.xlsx/.pdf)</span></label>
                                <div class="fom-group">
                                    <input type="file" class="form-control form-control-md form-control-required"
                                        accept=".csv,.txt,.xlx,.xls,.xlsx,.pdf" id="file" name="file">
                                </div>
                            </div>
                        </form>
                        <label class="control-label">รูปภาพ<span class="text-blue">
                                (เฉพาะไฟล์นามสกุล .png/.jpg/.jpeg)</span></label>
                        <form action="{{ url('sales-document/discount-mistake/uplod-image/request') }}" method="POST"
                            enctype="multipart/form-data" onsubmit="return SubmitForm(this);" class="dropzone"
                            id="dropzone" data-plugin="dropzone">
                            @csrf
                            <div class="fallback">
                                {{-- <input id="image" name="image" type="file" accept="image/*" multiple /> --}}
                                <input type="file" id="image" name="image" accept="image/*" multiple />
                            </div>
                            <div class="dz-message needsclick">
                                <i class="h1 text-muted dripicons-cloud-upload"></i>
                                <h3>วางรูปที่นี่ หรือ คลิกเพื่ออัพโหลด.</h3>
                                <h4 class="text-danger">**เพิ่มรูปภาพได้ไม่เกิน 5 รูป**</h4>
                                <span class="text-muted font-13">(เฉพาะไฟล์นามสกุล
                                    <strong>.png/.jpg/.jpeg</strong>)</span>
                            </div>
                        </form>
                        <div class="col-lg-12 mt-3">
                            <button type="submit" class="btn btn-primary w-100" id="btn-submit"><i
                                    class="fe-save"></i>บันทึก</button>
                            <a href="javascript:history.back()"
                                class="btn btn-outline-dark waves-effect waves-light mt-2 w-100"><i
                                    class="fe-arrow-left"></i>
                                ย้อนกลับ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="text" id="chksubmit" hidden>
    @endsection
    @section('script')
        <!-- third party js -->
        <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap3-typeahead.js') }}"></script>
        <script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
        <script>
            window.addEventListener('beforeunload', function(event) {
                if ($('#chksubmit').val() == '') {
                    $.ajax({
                        type: 'GET',
                        url: "{{ url('/sales-document/discount-mistake/clear/image') }}",
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            console.log('ok');
                        }
                    });
                }
                // event.returnValue = 'Are you sure you want to leave?';
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            Dropzone.options.dropzone = {
                maxFilesize: 12,
                parallelUploads: 1,
                maxFiles: 5,
                maxfilesexceeded: function(file) {
                    Swal.fire({
                        icon: 'error',
                        title: 'กรุณาตรวจสอบรูปภาพ',
                        text: 'เพิ่มรูปภาพได้ไม่เกิน 5 รูป',
                    })
                    this.removeFile(file);
                },
                renameFile: function(file) {
                    var dt = new Date();
                    var time = dt.getTime();
                    var im_name = time + file.name;
                    return im_name;
                },
                acceptedFiles: ".jpeg,.jpg,.png,.gif",
                addRemoveLinks: true,
                removedfile: function(file, im_name) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('removefile') }}",
                        data: {
                            name: file.upload.filename,
                        },
                    });
                    var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) :
                        void 0;
                },
                timeout: 5000,
                // success: function(file, response) {
                //     console.log(response);
                // },
                error: function(file, response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'กรุณาตรวจสอบไฟล์รูปภาพ',
                        text: 'ขนาดไฟล์ใหญ่เกินไป',
                    })
                    this.removeFile(file);
                }
            }
            $("#btn-submit").on("click", function() {
                if ($('#customer_code').val() == '' || $('#product_list').val() == '' || $('#customer_request').val() ==
                    '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'กรอกข้อมูลไม่ครบ',
                        text: 'กรุณากรอกให้ครบ',
                    })
                } else {
                    $('#chksubmit').val('submit');
                    $('#create_request').submit();
                }
            });

            $(document).ready(function() {
                $("#other").click(function() {
                    $(".f_comment").prop("hidden", false);
                    $("#note").prop('required', true);

                });
                $("#mistake01, #mistake02, #mistake03, #mistake04, #mistake05").click(function() {
                    $(".f_comment").prop("hidden", true);
                    $("#note").val("");
                    $("#note").prop('required', false);
                });
            });

            var route = "{{ route('request.search.Auto') }}";
            $("#customer_code").typeahead({
                minLength: 1,
                items: 10,
                showHintOnFocus: "all",
                selectOnBlur: false,
                autoSelect: true,
                displayText: function(item) {
                    return item.cuscod + ' : ' + item.prenam + " " + item.cusnam;
                },
                afterSelect: function(item) {
                    this.$element[0].value = item.cuscod;
                    if (item.cusnam) {
                        $("#customer_name").val(item.prenam + " " + item.cusnam);
                        $("#customer_status").val(item.paycond);
                        $("#limit").val(item.crline);
                        $("#staf").val(item.slmnam);
                    } else {
                        $("#customer_name").val(item.cusnam);
                    }
                },
                source: function(search, process) {
                    return $.get(
                        route, {
                            search: search
                        },
                        function(data) {
                            $("#customer_name").val("");
                            $("#customer_status").val("");
                            $("#limit").val("");
                            $("#staf").val("");
                            return process(data);
                        }
                    );
                },
            });
        </script>
    @endsection
