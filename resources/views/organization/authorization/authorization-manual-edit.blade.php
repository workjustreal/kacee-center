@extends('layouts.master-layout', ['page_title' => "แก้ไขสิทธิ์การอนุมัติลางาน (รายบุคคล)"])
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Kacee</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Organization</a></li>
                        <li class="breadcrumb-item active">แก้ไขสิทธิ์การอนุมัติลางาน (รายบุคคล)</li>
                    </ol>
                </div>
                <h4 class="page-title">แก้ไขสิทธิ์การอนุมัติลางาน (รายบุคคล)</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form action="{{ route('authorization-manual.update') }}" class="wow fadeInLeft" method="POST"
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
                            <input type="hidden" class="form-control" id="id" name="id" value="{{ $authorization->id }}">
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="emp_id" class="form-label">พนักงาน</label>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-12 mb-1">
                                            <input type="text" class="form-control form-control-required" id="emp_id" name="emp_id" value="{{ $authorization->emp_id }}" placeholder="ค้นหาพนักงาน" autocomplete="off">
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <input type="text" class="form-control bg-light" id="emp_name" name="emp_name" value="{{ $emp_name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="auth" class="form-label">ผู้อนุมัติ 1</label>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-12 mb-1">
                                            <input type="text" class="form-control form-control-required" id="auth" name="auth" value="{{ $authorization->auth }}" placeholder="ค้นหาพนักงาน" autocomplete="off">
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <input type="text" class="form-control bg-light" id="auth_name" name="auth_name" value="{{ $auth_name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="auth2" class="form-label">ผู้อนุมัติ 2</label>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-12 mb-1">
                                            <input type="text" class="form-control form-control-required" id="auth2" name="auth2" value="{{ $authorization->auth2 }}" placeholder="ค้นหาพนักงาน" autocomplete="off">
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <input type="text" class="form-control bg-light" id="auth2_name" name="auth2_name" value="{{ $auth2_name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-3 mt-3 mb-5">
                                    <a class="btn btn-secondary" href="{{ url('/organization/authorization-manual') }}">ย้อนกลับ</a>
                                    <button type="submit" class="btn btn-primary mx-2" id="btn-submit">อัปเดต</button>
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
        $('#emp_id').on('keyup focus', function(){
            getEmp($(this), $('#emp_name'));
        });
        $('#emp_id').on('blur', function(){
            getCheckEmp($(this), $('#emp_name'));
        });
        $('#auth').on('keyup focus', function(){
            getEmp($(this), $('#auth_name'));
        });
        $('#auth').on('blur', function(){
            getCheckEmp($(this), $('#auth_name'));
        });
        $('#auth2').on('keyup focus', function(){
            getEmp($(this), $('#auth2_name'));
        });
        $('#auth2').on('blur', function(){
            getCheckEmp($(this), $('#auth2_name'));
        });
    });
</script>
@endsection
