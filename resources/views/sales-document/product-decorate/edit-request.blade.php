@extends('layouts.master-layout', ['page_title' => 'แก้ไขคำขอ'])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/selectize/selectize.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->
    <style>
        #btn-request {
            margin-bottom: -48px;
        }

        .mistake {
            pointer-events: none;
        }

        .read textarea {
            pointer-events: none;
            background-color: #f3f7f9;
        }

        .read input {
            pointer-events: none;
            background-color: #f3f7f9;
        }

        .line {
            display: block;
            margin: 25px
        }

        .line h2 {
            font-size: 15px;
            text-align: center;
            border-bottom: 1.5px solid #e7e7e7;
            position: relative;
        }

        .line h2 span {
            background-color: #f5f6f8;
            position: relative;
            top: 10px;
            padding: 0 20px;
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Product decorate</a></li>
                            <li class="breadcrumb-item active">Request</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        @inject('thaiDateHelper', '\App\Services\ThaiDateHelperService')
        <div class="row">
            <div class="col-12 col-lg-3">
                <span class="line">
                    <h2><span class="text-muted">ประวัติ</span></h2>
                </span>
                @foreach ($log as $logact)
                    <div class="bg-white mb-2 rounded">
                        <div class="col-12">
                            <div class="d-flex flex-row">
                                <div class="p-2">
                                    <img src="{{ URL::asset('assets/images/users/thumbnail/user-1.jpg') }}"
                                        class="rounded-circle" width="36px" height="36px" alt="">
                                </div>
                                <div class="p-2">
                                    <h5 class="text-dark">
                                        <span></span>{{ $logact->name }}
                                        {{ $logact->surname }}
                                        ({{ $logact->nickname }})
                                        <small class="text-muted">
                                            {{ $thaiDateHelper->shortDateFormat($logact->updated_at) }},
                                            {{ \Carbon\Carbon::parse($logact->updated_at)->format('H:i') . ' น.' }}</small>
                                    </h5>
                                    @if (
                                        $logact->description == 'ManagerApprove' ||
                                            $logact->description == 'Manager DisApprove' ||
                                            $logact->description == 'SecretaryApprove' ||
                                            $logact->description == 'Secretary DisApprove')
                                        @if ($logact->description == 'ManagerApprove')
                                            @php
                                                $name_mnapp = $logact->name . ' ' . $logact->surname . ' ' . '(' . $logact->nickname . ')';
                                                $comment_mnapp = $logact->comment;
                                            @endphp
                                            <i class="mdi mdi-share-outline me-1"></i><small
                                                class="text-success">(อนุมัติ)</small> {{ $logact->comment }}
                                        @elseif($logact->description == 'Manager DisApprove')
                                            <i class="mdi mdi-share-outline me-1"></i><small
                                                class="text-danger">(ไม่อนุมัติ)</small> {{ $logact->comment }}
                                            @php
                                                $name_mnapp = $logact->name . ' ' . $logact->surname . ' ' . '(' . $logact->nickname . ')';
                                                $comment_mnapp = $logact->comment;
                                            @endphp
                                        @endif
                                        @if ($logact->description == 'SecretaryApprove')
                                            <i class="mdi mdi-share-outline me-1"></i><small
                                                class="text-success">(อนุมัติ)</small> {{ $logact->comment }}
                                            @php
                                                $name_secapp = $logact->name . ' ' . $logact->surname . ' ' . '(' . $logact->nickname . ')';
                                                $comment_secapp = $logact->comment;
                                            @endphp
                                        @elseif($logact->description == 'Secretary DisApprove')
                                            <i class="mdi mdi-share-outline me-1"></i><small
                                                class="text-danger">(ไม่อนุมัติ)</small> {{ $logact->comment }}
                                            @php
                                                $name_secapp = $logact->name . ' ' . $logact->surname . ' ' . '(' . $logact->nickname . ')';
                                                $comment_secapp = $logact->comment;
                                            @endphp
                                        @endif
                                    @else
                                        @php
                                            $name_secapp = $logact->name . ' ' . $logact->surname . ' ' . '(' . $logact->nickname . ')';
                                            $comment_secapp = $logact->description;
                                        @endphp
                                        <i class="mdi mdi-share-outline me-1"></i> {{ $logact->description }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @php
                $mn_approve = '';
                foreach ($result as $value) {
                    if ($value->mn_approve == '' && $value->sec_approve == '') {
                        $edit = true;
                    } else {
                        $mn_approve = $value->mn_approve;
                        $edit = false;
                    }
                }
            @endphp
            <div class="col-12 col-lg-6">
                <div class="card border-primary border">
                    <div class="card-header bg-primary text-center">
                        <h4 class="text-white">แก้ไขคำขอ</h4>
                    </div>
                    <div class="card-body">
                        @foreach ($result as $req)
                            <form class="form-horizontal" method="post" enctype="multipart/form-data" id="request_update"
                                action="{{ route('decorate.update.Request', [$req->doc_id, $req->id, $req->doc_status]) }}">
                                {{ csrf_field() }}
                                <div class="text-end"><b>{{ $doc_id }}</b></div>
                                <div class="row @if ($edit == false) read @endif">
                                    <div class="col-lg-6 mt-2">
                                        <div class="form-group ">
                                            <label class="control-label">รหัสลูกค้า</label>
                                            <input type="text" name="customer_code"
                                                class="form-control form-control-md form-control-required @if ($status != 2) bg-light @endif"
                                                @if ($status != 2) readonly @else id="customer_code" @endif
                                                autocomplete="off" required="" value="{{ $req->customer_code }}"
                                                placeholder="กรุณากรอกรหัสลูกค้า" />
                                            <div id="suggesstion-box"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-2">
                                        <div class="form-group ">
                                            <label class="control-label">ชื่อร้าน</label>
                                            <input type="text" class="form-control form-control-md form-control-required"
                                                id="customer_name" name="customer_name" placeholder="กรุณากรอกชื่อร้าน"
                                                value="{{ $req->customer_name }}" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mt-2">
                                        <div class="form-group ">
                                            <label class="control-label">สถานะลูกค้า</label>
                                            <input type="text"
                                                class="form-control form-control-md form-control-required bg-light"
                                                id="customer_status" name="customer_status"
                                                placeholder="กรุณาเลือกสถานะลูกค้า" value="{{ $req->customer_status }}"
                                                required="" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-2">
                                        <div class="form-group ">
                                            <label class="control-label">วงเงินอนุมัติ</label>
                                            <input type="number"
                                                class="form-control form-control-md form-control-required bg-light"
                                                id="limit" name="limit" placeholder="กรุณากรอกวงเงิน"
                                                value="{{ $req->limit }}" required="" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-2">
                                    <div class="form-group ">
                                        <label class="control-label">เจ้าหน้าที่ขายที่รับผิดชอบ</label>
                                        <input type="text"
                                            class="form-control form-control-md form-control-required bg-light"
                                            id="staf" name="staf" placeholder="กรุณากรอกชื่อเจ้าหน้าที่"
                                            value="{{ $req->employee }}" required="" readonly>
                                    </div>
                                </div>
                                <div class=" @if ($status != 2) mistake @endif">
                                    <div class="row">
                                        <div class="col-lg-12 mt-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="rd_request"
                                                    id="rd_request01" value="ขอเพื่อสนับสนุนการขายโดยเสนอส่วนลด/ราคาพิเศษ"
                                                    @if ($req->request == 'ขอเพื่อสนับสนุนการขายโดยเสนอส่วนลด/ราคาพิเศษ') checked @endif>
                                                <label class="form-check-label"
                                                    for="rd_request01">ขอเพื่อสนับสนุนการขายโดยเสนอส่วนลด/ราคาพิเศษ</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mt-2">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="rd_request"
                                                    id="rd_request02" value="ขอเพื่อสนับสนุนการขายโดยไม่คิดค่าใช้จ่าย"
                                                    @if ($req->request == 'ขอเพื่อสนับสนุนการขายโดยไม่คิดค่าใช้จ่าย') checked @endif>
                                                <label class="form-check-label"
                                                    for="rd_request02">ขอเพื่อสนับสนุนการขายโดยไม่คิดค่าใช้จ่าย
                                                    (ฟรี)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mt-2">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="rd_request"
                                                    id="other" value="other"
                                                    @if ($req->request == 'other') checked @endif>
                                                <label class="form-check-label" for="other">อื่นๆ</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div @if ($edit == false) class="read" @endif>
                                    <div class="col-lg-12 mt-2 mb-2">
                                        <div class="fom-group f_comment" @if ($req->request != 'other') hidden @endif>
                                            <label class="control-label">วัตถุประสงค์</label>
                                            <textarea class="form-control form-control-md form-control-required" id="note" name="note" placeholder="..."
                                                rows="7">{{ $req->note }}</textarea>
                                        </div>
                                    </div>
                                    {{-- <b>รายละเอียดสินค้า</b>
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <div class="form-group">
                                                <label class="control-label">รายการสินค้า</label>
                                                <input type="text"
                                                    class="form-control form-control-md form-control-required"
                                                    id="product_list" name="product_list"
                                                    placeholder="กรุณากรอกรายการสินค้า" autocomplete="off"
                                                    value="{{ $req->product_list }}" required="">
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="form-group ">
                                                <label class="control-label">ขนาดสินค้า</label>
                                                <input type="text"
                                                    class="form-control form-control-md form-control-required"
                                                    id="product_size" name="product_size"
                                                    placeholder="กรุณากรอกขนาดสินค้า" value="{{ $req->product_size }}"
                                                    required="" autocomplete="off">
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="form-group ">
                                                <label class="control-label">รวมมูลค่าสินค้า</label>
                                                <input type="number"
                                                    class="form-control form-control-md form-control-required"
                                                    id="product_price" name="product_price"
                                                    placeholder="กรุณากรอกขนาดสินค้า" value="{{ $req->product_price }}"
                                                    required="" autocomplete="off">
                                            </div>
                                        </li>
                                    </ul> --}}
                                </div>
                                @php
                                    $ck_again = '';
                                    foreach ($log as $again) {
                                        if ($again->description == 'แก้ไขเพื่อขออีกครั้ง') {
                                            $ck_again = 'true';
                                            $comm_again = $again->comment;
                                        } else {
                                            $ck_agai = 'false';
                                        }
                                    }
                                @endphp
                                <div @if (($edit == false && $mn_approve != auth()->user()->emp_id) || $status == 0 || $status == 3 || $ck_again == 'true') class="read" @endif>
                                    <div class="col-lg-12 mt-3">
                                        <div class="fom-group">
                                            <label class="control-label">รายละเอียด รายการสินค้า, ขนาด และ
                                                ราคาสินค้าที่ขอ</label>
                                            <textarea class="form-control form-control-md form-control-required" id="description" name="description"
                                                placeholder="..." rows="7">{{ strip_tags($req->description) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <b>หมายเหตุเพิ่มเติม</b>
                                        <textarea class="form-control form-control-required" name="more" id="more" rows="7">{{ $req->more }}</textarea>
                                    </div>
                                </div>
                                @if ($ck_again == 'true')
                                    <div class="col-lg-12 mt-2">
                                        <div class="fom-group">
                                            <label class="control-label">หมายเหตุ<span
                                                    class="text-danger">(ร้องขออีกครั้ง)</span></label>
                                            <textarea class="form-control form-control-md form-control-required" id="againedit" name="againedit"
                                                placeholder="..." rows="7">{{ $comm_again }}</textarea>
                                        </div>
                                    </div>
                                @endif
                                @if ($status == 3 || $status == 0)
                                    <div class="col-lg-12 mt-2">
                                        <div class="fom-group">
                                            <label class="control-label">หมายเหตุ <span
                                                    class="text-danger">(ร้องขออีกครั้ง)</span></label>
                                            <textarea class="form-control form-control-md form-control-required" id="again" name="again" placeholder="..."
                                                rows="7"></textarea>
                                        </div>
                                    </div>
                                @endif
                                <hr>
                                <h5 class="text-primary"><u>ส่วนแนบไฟล์</u></h5>
                                @if ($file != '')
                                    <div class="col-md-4 col-12 mt-3" id="old_file">
                                        <div class="card m-1 shadow-none border">
                                            <div class="p-2">
                                                <div class="row align-items-center">
                                                    <div class="col-auto pe-0">
                                                        <div class="avatar-sm">
                                                            <span
                                                                class="avatar-title bg-soft-primary text-primary rounded">
                                                                <i class="mdi mdi-folder-zip font-18"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col text-primary">
                                                        {{ $file->file }} <br>
                                                        <small class="mb-0 font-10 text-dark">download</small>
                                                    </div>
                                                    <input type="text" value="{{ $file->file }}" id="old_file"
                                                        name="old_file" hidden>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12">
                                    @error('file')
                                        <div class="alert alert-danger mt-1 mb-1">กรุณาตรวจสอบไฟล์</div>
                                    @enderror
                                    <label class="control-label">ไฟล์<span class="text-blue">
                                            (csv,txt,xlx,xls,xlxs,pdf)
                                        </span></label>
                                    <div class="fom-group">
                                        <input type="file" class="form-control form-control-md form-control-required"
                                            id="file" name="file" accept=".csv,.txt,.xlx,.xls,.xlxs,.pdf"
                                            onchange="$('#old_file').attr('hidden',true);">
                                    </div>
                                </div>
                                <div class="col-12 mt-2" id="old_image">
                                    <div class="row">
                                        @if ($img != '')
                                            @foreach ($img as $img)
                                                <div class="col-2 mt-2">
                                                    <img src="{{ asset('assets/images/decorate/' . $img) }}"
                                                        class="w-100" alt="decorate image" title="decorate image">
                                                </div>
                                                <input type="text" value="{{ $img }}" name="old_images[]"
                                                    hidden>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </form>
                            <label class="control-label mt-2">รูปภาพ<span class="text-blue">
                                    (เฉพาะไฟล์นามสกุล .png/.jpg/.jpeg)</span></label>
                            <form action="{{ route('decorate.image.request') }}" method="POST"
                                enctype="multipart/form-data" onsubmit="return SubmitForm(this);" class="dropzone"
                                id="dropzone" data-plugin="dropzone">
                                @csrf
                                <div class="fallback">
                                    <input id="file" name="file" type="file" accept="image/*" multiple />
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
                        @endforeach
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
                    url: "{{ url('/sales-document/product-decorate/clear/image') }}",
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
            addedfiles: function(file) {
                $('#old_image').attr('hidden', true);
            },
            maxfilesexceeded: function(file) {
                this.removeFile(file);
            },
            renameFile: function(file) {
                var dt = new Date();
                var time = dt.getTime();
                return time + file.name;
            },
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            addRemoveLinks: true,
            removedfile: function(file, im_name) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('decorate.remove.img') }}",
                    data: {
                        name: file.upload.filename,
                    },
                });
                var _ref;
                return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) :
                    void 0;
            },
            timeout: 5000,
            error: function(file, response) {
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาตรวจสอบไฟล์รูปภาพ',
                    text: 'รูปภาพเกิน 5 รูป หรือ ขนาดไฟล์ใหญ่เกินไป',
                })
                this.removeFile(file);
            }
        }
        $("#btn-submit").on("click", function() {
            if ($('#again').val() == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'กรอกข้อมูลไม่ครบ',
                    text: 'กรุณากรอก หมายเหตุ!',
                })
            } else {
                $('#chksubmit').val('submit');
                $('#request_update').submit();
            }
        });
        // clerfile dropzone
        // Dropzone.forElement("#dropzone").removeAllFiles(true);

        $(document).ready(function() {
            $("#other").click(function() {
                $(".f_comment").prop("hidden", false);
            });
            $("#rd_request01, #rd_request02").click(function() {
                $(".f_comment").prop("hidden", true);
                $("#note").val("");
            });

            $('#fileInput').change(function() {
                var files = this.files;
                console.log(files);
                if (files.length > 3 && files > 40000) {
                    alert('สามารถใส่รูปภาพได้ไม่เกิน 3 รูป');
                    $(this).val("");
                    return;
                }
                $('#preview').empty();
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var img = $('<img>').attr('src', e.target.result).css('max-width', '100px');
                        var delBtn = $('<button>').text('X').click(function() {
                            var index = Array.from($('#preview img')).indexOf(img.get(0));
                            img.remove();
                            $(this).remove();
                            var inputFiles = $('#fileInput')[0].files;
                            var dataTransfer = new DataTransfer();
                            for (var j = 0; j < inputFiles.length; j++) {
                                if (j !== index) {
                                    dataTransfer.items.add(inputFiles[j])
                                }
                            }
                            document.getElementById('fileInput').files = dataTransfer.files;
                        }).addClass('upload__img-close');
                        var container = $('<div>').addClass('col-auto preview-image').append(img)
                            .append(delBtn);
                        $('#preview').append(container);

                        // $('#preview').append(img).append(delBtn);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
        var route = "{{ route('request.search.Auto') }}";
        var routename = "{{ route('request.search.AutoName') }}";
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
                if (item.prenam) {
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

        $("#customer_name").typeahead({
            minLength: 1,
            items: 10,
            showHintOnFocus: "all",
            selectOnBlur: false,
            autoSelect: true,
            displayText: function(item) {
                return item.prenam + " " + item.cusnam;
            },
            afterSelect: function(item) {
                this.$element[0].value = item.cusnam;
                if (item.cusnam) {
                    $("#customer_code").val(item.cuscod);
                    $("#customer_status").val(item.paycond);
                    $("#limit").val(item.crline);
                    $("#staf").val(item.slmnam);
                } else {
                    $("#customer_name").val(item.cusnam);
                }
            },
            source: function(search, process) {
                return $.get(
                    routename, {
                        search: search
                    },
                    function(data) {
                        $("#customer_code").val("");
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
