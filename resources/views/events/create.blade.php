@extends('layouts.master-layout', ['page_title' => "เพิ่มประกาศบริษัท"])
@section('css')
<!-- third party css -->
<link href="{{ asset('assets/libs/bootstrap-4.5.3/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/summernote/summernote-bs4.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/summernote/summernote-lite.min.css') }}" rel="stylesheet" type="text/css" /> --}}
<link href="{{ asset('assets/css/inputdate/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
@endsection
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0 bg-light">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                        <li class="breadcrumb-item active">ประกาศบริษัท</li>
                    </ol>
                </div>
                <h4 class="page-title">ประกาศบริษัท</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <form id="form" action="{{ route('events.store') }}" method="post" enctype="multipart/form-data" onsubmit="return SubmitForm(this);">
                    {{ csrf_field() }}
                    <div class="card-body ribbon-box">
                        <div class="row">
                            <div class="col-md-2 col-6">
                                <div class="ribbon ribbon-primary float-start">สร้างกิจกรรม</div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <label for="">หัวข้อ</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <label for="">เริ่ม</label>
                                <input type="text" class="form-control datepicker" id="start" name="start" value="{{ old('start') }}" required>
                                @if ($errors->has('start'))
                                <span class="text-danger">กรุณากรอกข้อมูล</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <label for="">สิ้นสุด</label>
                                <input type="text" class="form-control datepicker" id="end" name="end" value="{{ old('end') }}" required>
                                @if ($errors->has('end'))
                                <span class="text-danger">กรุณากรอกข้อมูล</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <label for="summernote">รายละเอียด</label>
                                <textarea class="form-control" name="description" id="description">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="incalendar" name="incalendar">
                                    <label class="form-check-label" for="incalendar">เพิ่มลงในปฏิทิน</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="show" name="show" checked>
                                    <label class="form-check-label" for="show">แสดง</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="info" name="info">
                                    <label class="form-check-label" for="info">แจ้งเตือน</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="btncreevent" class="btn btn-success waves-effect waves-light mt-3">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-4.5.3/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/summernote/summernote-bs4.min.js') }}"></script>
<script src="{{ asset('assets/libs/summernote/summernote-file.js') }}"></script>
{{-- <script src="{{ asset('assets/libs/summernote/summernote-lite.min.js') }}"></script> --}}
{{-- inputdate --}}
<script src="{{ asset('assets/js/inputdate/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
<script src="{{ asset('assets/js/inputdate/form-pickers.init.js') }}"></script>
{{-- datatable --}}
<script src="{{ asset('assets/js/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables/datatables.init.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#description').summernote({
        height: 400,
        toolbar: [
            ['style', ['style', 'fontsize']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'file']],
            ['view', ['fullscreen', 'codeview', 'undo', 'redo', 'help']],
        ],
        callbacks: {
            onFileUpload: function(file) {
                //Your own code goes here
                myOwnCallBack(file[0]);
            },
        },
    });
    $('.note-modal .modal-dialog .modal-header button.close').on('click', function(){
        $('.note-modal').modal('hide');
    });
    // $('.note-image-dialog').modal('hide');
    // $(".dropdown-toggle").dropdown();
    // var styleEle = $("style#fixed");
    // if (styleEle.length == 0) {
    //   $("<style id=\"fixed\">.note-editor .dropdown-toggle::after { all: unset; } .note-editor .note-dropdown-menu { box-sizing: content-box; } .note-editor .note-modal-footer { box-sizing: content-box; }</style>")
    //   .prependTo("body");
    // }
});
function myOwnCallBack(file) {
        var formData = new FormData();
        formData.append('file', file);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url : "{{ route('events.file.upload') }}",
            type : 'POST',
            data : formData,
            cache: false,
            processData : false,
            contentType : false,
            xhr: function() { //Handle progress upload
                let myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) myXhr.upload.addEventListener('progress', progressHandlingFunction, false);
                return myXhr;
            },
            success : function(reponse) {
                // console.log(reponse);
                if(reponse.status === true) {
                    let elem;
                    //Other file type
                    elem = document.createElement("a");
                    let linkText = document.createTextNode(file.name);
                    elem.appendChild(linkText);
                    elem.title = file.name;
                    elem.href = reponse.name;
                    elem.target = '_blank';
                    elem.name = 'attach_file';
                    $('#description').summernote('editor.insertNode', elem);
                }
            }
        });
    }
    function progressHandlingFunction(e) {
        if (e.lengthComputable) {
            //Log current progress
            console.log((e.loaded / e.total * 100) + '%');

            //Reset progress on complete
            if (e.loaded === e.total) {
                console.log("Upload finished.");
            }
        }
    }
function SubmitForm(form){
    var title = document.getElementById("title");
    var start = document.getElementById("start");
    var end = document.getElementById("end");
    if (title.value == "" || start.value == "" || end.value == "") {
        Swal.fire({
            icon: "warning",
            title: "กรอกข้อมูลให้ครบถ้วน",
            timer: 2000,
            showConfirmButton: false,
        });
        return false;
    }
}
</script>
@endsection