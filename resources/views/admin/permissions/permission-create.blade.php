@extends('layouts.master-layout', ['page_title' => "เพิ่มสิทธิ์การใช้งาน"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">สิทธิ์การใช้งาน</li>
                    </ol>
                </div>
                <h4 class="page-title">เพิ่มสิทธิ์การใช้งาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" action="{{ route('permission.store') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="mb-3">
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
                            <div class="mb-3">
                                <label for="permission" class="form-label">ชื่อสิทธิ์</label> <span class="text-danger">* เช่น <code>user-list</code> , <code>user-create</code></span>
                                <input id="permission" name="permission" type="text" class="form-control" value="{{ old('permission') }}"
                                    placeholder="ชื่อสิทธิ์" autocomplete="off" required />
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">รายละเอียดสิทธิ์</label>
                                <textarea class="form-control" id="description" placeholder="รายละเอียดสิทธิ์"
                                    name="description" rows="4" required>{{ old('description') }}</textarea>
                            </div>
                            <div class="mb-3 d-flex justify-content-between">
                                <a href="{{ url('admin/permissions') }}" class="btn btn-secondary mt-3"> ย้อนกลับ</a>
                                <button type="submit" class="btn btn-primary mt-3"> บันทึก</button>
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
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
<!-- third party js ends -->
@endsection