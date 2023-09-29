@extends('layouts.master-layout', ['page_title' => "เพิ่มสิทธิ์การอนุมัติ (ตามหน่วยงาน)"])
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
                        <li class="breadcrumb-item active">เพิ่มสิทธิ์การอนุมัติ (ตามหน่วยงาน)</li>
                    </ol>
                </div>
                <h4 class="page-title">เพิ่มสิทธิ์การอนุมัติ (ตามหน่วยงาน)</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form action="{{ route('authorization.store') }}" class="wow fadeInLeft" method="POST"
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
                                    <label for="dept_id" class="form-label">หน่วยงาน / แผนก</label>
                                    <select class="form-control form-control-required" data-toggle="select2" id="dept_id" name="dept_id" required>
                                        <option value="" selected="selected">-</option>
                                        <optgroup label="ส่วน">
                                            @foreach ($level1 as $list)
                                            <option value="{{ $list->dept_id }}" @if(old('dept_id')==$list->dept_id) selected @endif>{{ $list->dept_name }} ({{ $list->dept_id }})</option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="ฝ่าย">
                                            @foreach ($level2 as $list)
                                            <option value="{{ $list->dept_id }}" @if(old('dept_id')==$list->dept_id) selected @endif>{{ $list->dept_name }} ({{ $list->dept_id }})</option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="แผนก">
                                            @foreach ($level3 as $list)
                                            <option value="{{ $list->dept_id }}" @if(old('dept_id')==$list->dept_id) selected @endif>{{ $list->dept_name }} ({{ $list->dept_id }})</option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="หน่วยงาน">
                                            @foreach ($level4 as $list)
                                            <option value="{{ $list->dept_id }}" @if(old('dept_id')==$list->dept_id) selected @endif>{{ $list->dept_name }} ({{ $list->dept_id }})</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="auth" class="form-label">ผู้อนุมัติ 1</label>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-12 mb-1">
                                            <input type="text" class="form-control form-control-required" id="auth" name="auth" placeholder="ค้นหาพนักงาน" autocomplete="off">
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <input type="text" class="form-control bg-light" id="auth_name" name="auth_name" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="auth2" class="form-label">ผู้อนุมัติ 2</label>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-12 mb-1">
                                            <input type="text" class="form-control form-control-required" id="auth2" name="auth2" placeholder="ค้นหาพนักงาน" autocomplete="off">
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <input type="text" class="form-control bg-light" id="auth2_name" name="auth2_name" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-3 mt-3 mb-5">
                                    <a class="btn btn-secondary" href="{{ url('/organization/authorization') }}">ย้อนกลับ</a>
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
