@extends('layouts.master-layout', ['page_title' => "อัปโหลดข้อมูลเข้า-ออกงาน"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/ladda/ladda.min.css') }}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
<style>
    .dropify-message > p {
        font-size: 1.75rem;
        color: #cccccc;
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
                        <li class="breadcrumb-item active">อัปโหลดข้อมูล</li>
                    </ol>
                </div>
                <h4 class="page-title">อัปโหลดข้อมูลเข้า-ออกงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <!-- file preview template -->
    <div class="d-none" id="uploadPreviewTemplate">
        <div class="card mt-1 mb-0 shadow-none border">
            <div class="p-2">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img data-dz-thumbnail src="#" class="avatar-sm rounded bg-light" alt="">
                    </div>
                    <div class="col ps-0">
                        <a href="javascript:void(0);" class="text-muted fw-bold" data-dz-name></a>
                        <p class="mb-0" data-dz-size></p>
                    </div>
                    <div class="col-auto">
                        <!-- Button -->
                        <a href="" class="btn btn-link btn-lg text-muted" data-dz-remove>
                            <i class="dripicons-cross"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="form-horizontal" id="upload-form" action="{{ url('leave/manage/upload-file') }}" method="POST"
                        enctype="multipart/form-data" onsubmit="return SubmitForm(this);">
                        {{ csrf_field() }}
                        <input type="file" id="file" name="file" accept=".csv" data-plugins="dropify" data-height="300" data-max-file-size="5M" data-allowed-file-extensions="csv" />
                        {!! $errors->first('file', '<span class="text-danger">:message</span>') !!}
                        <div class="mt-3 text-center">
                            <button id="loading" name="loading" class="btn btn-primary hidd" type="button" disabled>
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                รอสักครู่...
                            </button>
                            <button type="submit" id="submit" name="submit" class="ladda-button btn btn-primary" dir="ltr" data-style="zoom-out" title="UPLOAD">อัปโหลด</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script src="{{asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
<script src="{{asset('assets/libs/dropify/dropify.min.js')}}"></script>
<script src="{{ asset('assets/libs/ladda/ladda.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/loading-btn.init.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    !function(e){"use strict";var o=function(){this.$body=e("body")};o.prototype.init=function(){Dropzone.autoDiscover=!1,e('[data-plugin="dropzone"]').each((function(){var o=e(this).attr("action"),i=e(this).data("previewsContainer"),r={url:o};i&&(r.previewsContainer=i);var t=e(this).data("uploadPreviewTemplate");t&&(r.previewTemplate=e(t).html()),e(this).dropzone(r)}))},e.FileUpload=new o,e.FileUpload.Constructor=o}(window.jQuery),function(e){"use strict";window.jQuery.FileUpload.init()}(),$('[data-plugins="dropify"]').length>0&&$('[data-plugins="dropify"]').dropify({messages:{default:"Drag and drop a file here or click",replace:"Drag and drop or click to replace",remove:"Remove",error:"Ooops, something wrong appended."},error:{fileSize:"The file size is too big (5M max)."}});
    function SubmitForm(form){
        if (document.getElementById('file').value == "") {
            Swal.fire({
                icon: "warning",
                title: "ยังไม่ได้เลือกไฟล์",
                showConfirmButton: false,
                timer: 2000,
            });
            return false;
        }
        Swal.fire({
            title: 'กำลังอัปโหลดข้อมูล',
            html: 'กรุณารอสักครู่...',
            showConfirmButton: false,
            allowOutsideClick: false,
            onBeforeOpen: () => {
                Swal.showLoading()
            },
        });
        $('#loading').show();
        $('#submit').hide();
    }
</script>
@endsection