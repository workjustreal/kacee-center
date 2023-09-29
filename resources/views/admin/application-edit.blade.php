@extends('layouts.master-layout', ['page_title' => "แก้ไขระบบงาน"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">ระบบงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">แก้ไขระบบงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" action="{{ route('application.update') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{method_field('PUT')}}
                            <input id="id" name="id" type="hidden" class="form-control" value="{{ $application->id }}" />
                            <div class="mb-3">
                                <label for="product" class="form-label">ชื่อระบบงาน</label>
                                <input id="name" name="name" type="text" class="form-control" value="{{ $application->name }}"
                                placeholder="ชื่อระบบงาน" autocomplete="off" required />
                            </div>
                            {{-- <div class="mb-3">
                                <label for="status" class="form-label">อัปเดตรูปภาพ</label><br>
                                <div class="radio radio-success form-check-inline ml-2">
                                    <input type="radio" id="image_update1" value="1" name="image_update" title="YES">
                                    <label for="image_update1">ใช่ </label>
                                </div>
                                <div class="radio form-check-inline">
                                    <input type="radio" id="image_update2" value="0" name="image_update" title="NO" checked>
                                    <label for="image_update2">ไม่ใช่ </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="fallback">
                                    <label for="image" class="form-label">รูปภาพ </label><br>
                                    <input id="image" name="image" class="form-control" type="file" title="Upload Image" accept="image/*" onchange="showPreview(event);" style="display: none" />
                                    {!! $errors->first('image', '<span class="text-danger">:message</span>') !!}
                                    @if ($application->image == 'noimage.jpg')
                                    <img class="mt-2" id="imgPreview" src="{{ url('assets/images/noimage.jpg') }}" alt="" width="80">
                                    @else
                                    <img class="mt-2" id="imgPreview" src="{{ url('assets/images/application/'.$application->image) }}" alt="" width="80">
                                    @endif
                                    <input name="image_old" type="hidden" class="form-control" value="{{ $application->image }}" />
                                </div>
                            </div> --}}
                            <div class="mb-3">
                                <label for="icon" class="form-label">ไอคอน (Feather Icons) </label><br>
                                <div class="input-group mb-3">
                                    <button class="btn btn-outline-secondary" type="button" id="button-icon" data-bs-toggle="modal" data-bs-target="#choose-icon-modal">เลือกไอคอน</button>
                                    <input id="icon" name="icon" type="text" class="form-control" value="{{ $application->icon }}"
                                    placeholder="ไอคอนระบบงาน" autocomplete="off" readonly required />
                                </div>
                                <div id="icon_preview">
                                    @if ($application->icon != '')
                                        <i class="{{ $application->icon }} fs-1" style="color: {{ $application->color }}"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">สีไอคอน</label>
                                <input type="text" class="form-control colorpicker-default" id="color" name="color"
                                value="{{ ($application->color!='') ? $application->color : '#999999' }}" onchange="setIconColor(this.value)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL ระบบ</label> <code>ใส่ parameters ได้ เช่น URL/{id}</code>
                                <input type="text" class="form-control" id="url" name="url" value="{{ $application->url }}">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">สถานะ (Top Menu)</label><br>
                                <div class="radio radio-success form-check-inline ml-2">
                                    <input type="radio" id="inlineRadio1" value="1" name="status" title="SHOW" checked {{ ($application->status==1) ? 'checked' : '' }}>
                                    <label for="inlineRadio1">แสดงผล </label>
                                </div>
                                <div class="radio form-check-inline">
                                    <input type="radio" id="inlineRadio2" value="0" name="status" title="HIDDEN" {{ ($application->status==0) ? 'checked' : '' }}>
                                    <label for="inlineRadio2">ไม่แสดงผล </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="detail" class="form-label">บันทึกเพิ่มเติม</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" placeholder="บันทึกรายละเอียดเพิ่มเติม"
                                    name="detail" rows="4" required>{{ $application->detail }}</textarea>
                            </div>
                            <div class="mb-3">
                                <button type="submit" name="submit" class="btn btn-primary mt-3" title="SAVE"> บันทึก</button>
                            </div>
                        </form>
                        @include('admin.app-icon-list')
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
    <script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
    <script src="{{asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.js')}}"></script>
    <!-- third party js ends -->
    <script type="text/javascript">
        $(document).ready(function(){
            $(".colorpicker-default").spectrum();
        });
        $(document).ready(function(){
            $('input[type=radio][name=image_update]').change(function() {
                if (this.value == 1) {
                    $('#image').show();
                } else {
                    $('#image').hide();
                }
            });
        });
        function showPreview(event){
            var preview = document.getElementById("imgPreview");
            if(event.target.files.length > 0){
                var src = URL.createObjectURL(event.target.files[0]);
                preview.src = src;
                preview.style.display = "block";
            }else{
                preview.src = '';
                preview.style.display = "none";
            }
        }
        function setIcon(i) {
            var name = $(i)[0].innerText.trim();
            $("#icon").val(name);
            var color = $("#color").val();
            $("#icon_preview").html('<i class="'+name+' fs-1" style="color: '+color+'"></i>');
            $('#btn-close').click();
        }
        function setIconColor(color) {
            $("#icon_preview > i").css('color', color);
        }
    </script>
@endsection