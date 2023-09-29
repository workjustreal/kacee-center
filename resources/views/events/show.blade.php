@extends('layouts.master-layout', ['page_title' => "ประกาศบริษัท"])
@section('css')
<style>
    img {
        max-width: 100%;
        height: auto;
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
            <div class="card ribbon-box">
                <div class="card-body">
                    <div class="ribbon ribbon-primary float-end"><span>ประกาศบริษัท</span></div>
                    <h3>{{ $event->title }}</h3>
                    <small class="text-muted">จาก: {{ \Carbon\Carbon::parse($event->start)->format('d/m/Y') }} ถึง: {{ \Carbon\Carbon::parse($event->end)->format('d/m/Y') }}</small>
                    <hr />
                    <div class="d-flex align-items-start mb-3 mt-1">
                        <img class="d-flex me-2 rounded-circle" src="{{url('assets/images/users/'.$user->image)}}" onerror="this.onerror=null;this.src='{{url('assets/images/users/thumbnail/user-1.jpg')}}'" alt="placeholder image" width="32" height="32">
                        <div class="w-100">
                            <h6 class="m-0 font-14">{{ $user->name . " " . $user->surname }}</h6>
                            <small class="text-muted">หน่วยงาน: {{ $user->dept_name }}</small>
                            <small class="float-end"><small class="text-muted">อัปเดตล่าสุด </small>{{ \Carbon\Carbon::parse($event->updated_at)->locale('th_TH')->isoFormat('dd, lll น.') }}</small>
                        </div>
                    </div>
                    <div>{!! $event->description !!}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
@endsection