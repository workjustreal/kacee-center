@extends('layouts.master-layout', ['page_title' => "แก้ไขจำนวนวันหยุดประจำปี"])
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Kacee</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Leave</a></li>
                        <li class="breadcrumb-item active">แก้ไขจำนวนวันหยุดประจำปี</li>
                    </ol>
                </div>
                <h4 class="page-title">แก้ไขจำนวนวันหยุดประจำปี</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form action="{{ route('leave-type-property.update') }}" class="wow fadeInLeft" method="POST"
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
                            <input type="hidden" class="form-control" id="id" name="id" value="{{ $leave_type_property->leave_type_ppt_id }}">
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="name" class="form-label">ชื่อวันหยุด</label>
                                    <input type="text" class="form-control form-control-required" id="name" name="name" value="{{ $leave_type_property->leave_type_ppt_name }}" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="day" class="form-label">จำนวนวันหยุด (วัน) / ต่อปี</label>
                                    <input type="number" class="form-control form-control-required" id="day" name="day" min="1" max="99" value="{{ $leave_type_property->leave_type_ppt_day }}" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label class="form-label">สิทธิ์ประเภทพนักงาน</label><br>
                                    <div class="form-check form-check-success form-check-inline ml-2">
                                        <input type="checkbox" class="form-check-input" id="monthly" name="monthly" value="{{ $leave_type_property->leave_type_ppt_monthly }}" {{ ($leave_type_property->leave_type_ppt_monthly==1) ? 'checked' : '' }}>
                                        <label for="monthly">รายเดือน </label>
                                    </div>
                                    <div class="form-check form-check-success form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="daily" name="daily" value="{{ $leave_type_property->leave_type_ppt_daily }}" {{ ($leave_type_property->leave_type_ppt_daily==1) ? 'checked' : '' }}>
                                        <label for="daily">รายวัน </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label for="status" class="form-label">สถานะ</label><br>
                                    <div class="radio radio-success form-check-inline ml-2">
                                        <input type="radio" id="inlineRadio1" name="status" value="1" title="ACTIVE" checked {{ ($leave_type_property->leave_type_ppt_status==1) ? 'checked' : '' }}>
                                        <label for="inlineRadio1">ใช้งาน </label>
                                    </div>
                                    <div class="radio form-check-inline">
                                        <input type="radio" id="inlineRadio2" name="status" value="0" title="INACTIVE" {{ ($leave_type_property->leave_type_ppt_status==0) ? 'checked' : '' }}>
                                        <label for="inlineRadio2">ไม่ใช้งาน </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-3 mt-3 mb-5">
                                    @if ($errors->any())
                                    <a class="btn btn-secondary" href="{{ url('/leave/manage/leave-type-property') }}">ย้อนกลับ</a>
                                    @else
                                    <button type="button" class="btn btn-secondary" onclick="history.back()">ย้อนกลับ</button>
                                    @endif
                                    <button type="submit" class="btn btn-primary mx-2">อัปเดต</button>
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
<!-- third party js ends -->
@endsection
