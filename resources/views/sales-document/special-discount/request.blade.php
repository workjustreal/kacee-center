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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Product Decorate</a></li>
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
                        <form id="create_request" class="form-horizontal" method="post" enctype="multipart/form-data"
                            action="{{ route('special.create.request') }}">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-lg-6 mt-2">
                                    <div class="form-group ">
                                        <label class="control-label">รหัสลูกค้า</label>
                                        <input type="text" name="customer_code" id="customer_code"
                                            class="form-control form-control-md form-control-required" autocomplete="off"
                                            required="" placeholder="กรุณากรอกรหัสลูกค้า"
                                            value="{{ old('customer_code') }}" />
                                        <div id="suggesstion-box"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-2">
                                    <div class="form-group ">
                                        <label class="control-label">ชื่อร้าน</label>
                                        <input type="text"
                                            class="form-control form-control-md form-control-required bg-light" readonly
                                            id="customer_name" name="customer_name" placeholder="กรุณากรอกชื่อร้าน"
                                            autocomplete="off" value="{{ old('customer_name') }}" required="">
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
                                            autocomplete="off" value="{{ old('customer_status') }}" required="">
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-2">
                                    <div class="form-group ">
                                        <label class="control-label">วงเงินอนุมัติ</label>
                                        <input type="number"
                                            class="form-control form-control-md form-control-required bg-light" readonly
                                            id="limit" name="limit" placeholder="กรุณากรอกวงเงิน"
                                            value="{{ old('limit') }}" autocomplete="off" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-2">
                                <div class="form-group ">
                                    <label class="control-label">เจ้าหน้าที่ขายที่รับผิดชอบ</label>
                                    <input type="text"
                                        class="form-control form-control-md form-control-required bg-light" readonly
                                        id="staf" name="staf" placeholder="กรุณากรอกชื่อเจ้าหน้าที่"
                                        value="{{ old('staf') }}" autocomplete="off" required="">
                                </div>
                            </div>
                            <div class="mt-2">
                                <b>รายละเอียด รายการสินค้า, ขนาด และ ราคาสินค้าที่ขอ</b>
                                <textarea class="form-control form-control-required" name="product_detail" id="product_detail" rows="15">{{ old('product_detail') }}</textarea>
                            </div>

                            {{-- ส่วนแนบไฟล์ --}}
                            <hr>
                            <h5 class="card-title mt-3 text-primary text-decoration-underline">ส่วนแนบไฟล์</h5>
                            <div class="col-12 mt-2 mb-2">
                                @error('file')
                                    <div class="alert alert-danger mt-1 mb-1">กรุณาตรวจสอบไฟล์</div>
                                @enderror
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <label class="control-label">ไฟล์<span class="text-blue">
                                        (เฉพาะไฟล์นามสกุล .csv/.txt/.xlx/.xls/.xlsx/.pdf)</span></label>
                                <div class="fom-group">
                                    <input type="file" id="file" name="file"
                                        class="form-control form-control-md form-control-required"
                                        accept=".csv,.txt,.xlx,.xls,.xlsx,.pdf">
                                </div>
                            </div>
                        </form>
                        <label class="control-label">รูปภาพ<span class="text-blue">
                                (เฉพาะไฟล์นามสกุล .png/.jpg/.jpeg)</span></label>
                        <form action="{{ route('special.image.request') }}" method="POST" enctype="multipart/form-data"
                            onsubmit="return SubmitForm(this);" class="dropzone" id="dropzone" data-plugin="dropzone">
                            @csrf
                            <div class="fallback">
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
                                    class="fe-arrow-left"></i> ย้อนกลับ</a>
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
            $(document).ready(function() {
                $("#other").click(function() {
                    $(".f_comment").prop("hidden", false);
                    $("#note").prop('required', true);

                });
                $("#rd_request01, #rd_request02").click(function() {
                    $(".f_comment").prop("hidden", true);
                    $("#note").val("");
                    $("#note").prop('required', false);
                });
            });

            // check save form delete image
            window.addEventListener('beforeunload', function(event) {
                if ($('#chksubmit').val() == '') {
                    $.ajax({
                        type: 'GET',
                        url: "{{ url('/sales-document/special-discount/clear/image') }}",
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

            // dropzone image
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
                        url: "{{ route('special.remove.img') }}",
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

            // check input
            $("#btn-submit").on("click", function() {
                if ($('#customer_code').val() == '' || $('#product_detail').val() == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'กรอกข้อมูลไม่ครบ',
                        text: 'กรุณากรอกให้ครบ',
                    })
                } else if ($('#other').is(':checked') && $('#note').val() == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'กรอกข้อมูลไม่ครบ!',
                        text: 'กรุณากรอกวัตถุประสงค์!',
                    })
                } else {
                    $('#chksubmit').val('submit');
                    $('#create_request').submit();
                }
            });

            //search customer
            var route = "{{ route('special.search.auto') }}";
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
