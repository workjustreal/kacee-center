@extends('layouts.master-layout', ['page_title' => "เพิ่มการแจ้งเตือน"])
@section('css')
<!-- third party css -->
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">การแจ้งเตือน</li>
                    </ol>
                </div>
                <h4 class="page-title">เพิ่มการแจ้งเตือน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form action="{{ route('notification.store') }}" class="wow fadeInLeft" method="POST"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="app_id" class="form-label">ระบบงาน</label>
                                    <select class="form-select form-control-required" id="app_id" name="app_id" required>
                                        <option value="" selected="selected" disabled>-</option>
                                        @foreach ($application as $list)
                                        <option value="{{ $list->id }}" @if(old('app_id')==$list->id) selected @endif>{{ $list->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="from" class="form-label">จาก</label>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-12 mb-1">
                                            <input type="text" class="form-control form-control-required" id="from" name="from" placeholder="ค้นหาพนักงาน" autocomplete="off" required>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <input type="text" class="form-control bg-light" id="from_name" name="from_name" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="to" class="form-label">ถึง</label>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-12 mb-1">
                                            <input type="text" class="form-control form-control-required" id="to" name="to" placeholder="ค้นหาพนักงาน" autocomplete="off" required>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <input type="text" class="form-control bg-light" id="to_name" name="to_name" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="title" class="form-label">TITLE</label>
                                    <input type="text" class="form-control form-control-required" id="title" name="title" placeholder="TITLE" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="description" class="form-label">DESCRIPTION</label>
                                    <input type="text" class="form-control form-control-required" id="description" name="description" placeholder="DESCRIPTION" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="url" class="form-label">URL</label>
                                    <input type="text" class="form-control form-control-required" id="url" name="url" placeholder="URL" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="job_id" class="form-label">JOB ID</label>
                                    <input type="text" class="form-control form-control-required" id="job_id" name="job_id" placeholder="ID" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="type" class="form-label">TYPE</label>
                                    <select class="form-select form-control-required" id="type" name="type" required>
                                        <option value="" selected="selected" disabled>-</option>
                                        <option value="00">00 = ประกาศสำคัญ</option>
                                        <option value="01">01 = ทั่วไป</option>
                                        <option value="02">02 = ทั่วไป (ยกเลิก, ลบ)</option>
                                        <option value="03">03 = ทั่วไป (แยกย่อยจากระบบหลัก)</option>
                                        <option value="04">04 = ทั่วไป (แยกย่อยจากระบบหลัก) (ยกเลิก, ลบ)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-3 mt-3 mb-5">
                                    <a class="btn btn-secondary" href="{{ url('/admin/notifications') }}">ย้อนกลับ</a>
                                    <button type="submit" class="btn btn-primary mx-2" id="btn-submit">บันทึก</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap3-typeahead.js') }}"></script>
<script src="{{ asset('assets/js/pages/authorization.init.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    $(document).ready(function() {
        $('[data-toggle="select2"]').select2();
        $('#from').on('keyup focus', function(){
            getEmp($(this), $('#from_name'));
        });
        $('#from').on('blur', function(){
            getCheckEmp($(this), $('#from_name'));
        });
        $('#to').on('keyup focus', function(){
            getEmp($(this), $('#to_name'));
        });
        $('#to').on('blur', function(){
            getCheckEmp($(this), $('#to_name'));
        });
        $('#app_id').on('change', function(){
            $("#title").val($('#app_id option:selected').text());
        });
    });
</script>
@endsection
