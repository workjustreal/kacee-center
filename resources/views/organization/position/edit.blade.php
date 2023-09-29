@extends('layouts.master-layout', ['page_title' => "แก้ไขข้อมูลตำแหน่งงาน"])
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
                        <li class="breadcrumb-item active">แก้ไขข้อมูลตำแหน่งงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">แก้ไขข้อมูลตำแหน่งงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form action="{{ route('position.update') }}" class="wow fadeInLeft" method="POST"
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
                                        <label class="control-label">รหัสตำแหน่งงาน</label>
                                        <input type="text" class="form-control form-control-required bg-light" id="position_id"
                                            name="position_id" value="{{ $position->position_id }}" autocomplete="off" minlength="3" maxlength="3" required readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <div class="form-group">
                                        <label class="control-label">ชื่อตำแหน่งงาน</label>
                                        <input type="text" class="form-control form-control-required" id="position_name"
                                            name="position_name" value="{{ $position->position_name }}" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <div class="form-group">
                                        <label class="control-label">ชื่อตำแหน่งงาน (EN)</label>
                                        <input type="text" class="form-control" id="position_name_en"
                                            name="position_name_en" value="{{ $position->position_name_en }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-3 mb-5">
                                    <a class="btn btn-secondary" href="{{ url('organization/position') }}">ย้อนกลับ</a>
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
@endsection
