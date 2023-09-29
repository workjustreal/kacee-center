@extends('layouts.master-layout', ['page_title' => "เพิ่มพื้นที่การขาย"])
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
                        <li class="breadcrumb-item active">เพิ่มพื้นที่การขาย</li>
                    </ol>
                </div>
                <h4 class="page-title">เพิ่มพื้นที่การขาย</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form action="{{ route('sales-area.store') }}" class="wow fadeInLeft" method="POST"
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
                                        <label class="control-label">รหัสพื้นที่การขาย</label> <span class="text-danger">(รหัส 4 ตัว เช่น Facebook = FB01, FB02)</span>
                                        <input type="text" class="form-control form-control-required text-uppercase" id="area_code"
                                            name="area_code" value="{{ old('area_code') }}" autocomplete="off" minlength="4" maxlength="4" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <div class="form-group">
                                        <label class="control-label">ฝ่าย</label>
                                        <select class="form-select form-control-required" id="dept_id" name="dept_id" required>
                                            <option value="">-</option>
                                            @foreach ($dept as $list)
                                            <option value="{{ $list->dept_id }}" @if(old('dept_id')==$list->dept_id) selected @endif>{{ $list->dept_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <div class="form-group">
                                        <label class="control-label">รายละเอียด</label>
                                        <input type="text" class="form-control" id="area_description"
                                            name="area_description" value="{{ old('area_description') }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-5 mb-5">
                                    <a class="btn btn-secondary" href="{{ url('organization/sales-area') }}">ย้อนกลับ</a>
                                    <button type="submit" class="btn btn-primary mx-2">บันทึก</button>
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
</script>
@endsection
