@extends('layouts.master-layout', ['page_title' => "แก้ไขขนส่ง"])
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                        <li class="breadcrumb-item active">ขนส่งสินค้า</li>
                    </ol>
                </div>
                <h4 class="page-title">แก้ไขขนส่ง</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" action="/checkout/ship-com-update/{{$ship_com->id}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{method_field('PUT')}}
                            <div class="mb-3">
                                <label class="form-label">ชื่อขนส่ง</label>
                                <input id="name" name="name" type="text" class="form-control" value="{{ $ship_com->name }}"
                                placeholder="ชื่อขนส่ง" autocomplete="off" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ตรวจสอบจาก <span class="text-danger">* กรณีตรวจสอบหลายแบบให้คั่นด้วย ( , ) comma</span></label>
                                <input type="text" class="form-control text-uppercase" id="check" name="check" value="{{ $ship_com->check }}">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">สถานะ</label><br>
                                <div class="radio radio-success form-check-inline ml-2">
                                    <input type="radio" id="inlineRadio1" value="1" name="status" title="SHOW" checked {{ ($ship_com->status==1) ? 'checked' : '' }}>
                                    <label for="inlineRadio1">ใช้งาน </label>
                                </div>
                                <div class="radio form-check-inline">
                                    <input type="radio" id="inlineRadio2" value="0" name="status" title="HIDDEN" {{ ($ship_com->status==0) ? 'checked' : '' }}>
                                    <label for="inlineRadio2">ไม่ใช้งาน </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button type="submit" name="submit" class="btn btn-primary mt-3" title="SAVE"> บันทึก</button>
                            </div>
                        </form>
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