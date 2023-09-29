@extends('layouts.master-layout', ['page_title' => "ดูข้อมูลหน่วยงาน"])
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Organization</a></li>
                        <li class="breadcrumb-item active">ดูข้อมูลหน่วยงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">ดูข้อมูลหน่วยงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form action="" class="wow fadeInLeft" method="POST"
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
                                    <div class="form-group">
                                        <label class="control-label">รหัสหน่วยงาน</label>
                                        <input type="text" class="form-control text-uppercase" id="dept_id"
                                            name="dept_id" value="{{ $department->dept_id }}" minlength="9" maxlength="9" disabled />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <div class="form-group">
                                        <label class="control-label">ชื่อหน่วยงาน</label>
                                        <input type="text" class="form-control" id="dept_name"
                                            name="dept_name" value="{{ $department->dept_name }}" disabled />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <div class="form-group">
                                        <label class="control-label">ชื่อหน่วยงาน (EN)</label>
                                        <input type="text" class="form-control" id="dept_name_en"
                                            name="dept_name_en" value="{{ $department->dept_name_en }}" disabled />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <div class="form-group">
                                        <label class="control-label">ระดับ</label>
                                        <select class="form-select" aria-label=".form-select-sm"
                                            id="level" name="level" disabled>
                                            <option value="" selected="selected">-</option>
                                            <option value="0" @if($department->level == '0') selected
                                                @endif>ระดับบริษัท</option>
                                            <option value="1" @if($department->level == '1') selected
                                                @endif>ระดับส่วน</option>
                                            <option value="2" @if($department->level == '2') selected
                                                @endif>ระดับฝ่าย</option>
                                            <option value="3" @if($department->level == '3') selected
                                                @endif>ระดับแผนก</option>
                                            <option value="4" @if($department->level == '4') selected
                                                @endif>ระดับหน่วย</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <div class="form-group">
                                        <label class="control-label">ภายใต้หน่วยงาน</label>
                                        <select class="form-select" aria-label=".form-select-sm"
                                            id="dept_parent" name="dept_parent" disabled>
                                            <option value="-">-</option>
                                            @foreach ($dept_parent as $list)
                                            <option value="{{ $list->dept_parent }}" @if($department->dept_parent==$list->dept_id) selected @endif @if($department->level != $list->level+1) class="hidd"
                                                @endif>{{ $list->dept_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-3 mb-5">
                                    <button type="button" class="btn btn-secondary" onclick="history.back()">ย้อนกลับ</button>
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
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<!-- third party js ends -->
@endsection
