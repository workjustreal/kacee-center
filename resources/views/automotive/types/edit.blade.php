@extends('layouts.master-nopreloader-layout', ['page_title' => 'แก้ไขข้อมูลรถ'])
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">automotive</a></li>
                            <li class="breadcrumb-item active">แก้ไขข้อมูลรถ</li>
                        </ol>
                    </div>
                    <h4 class="page-title">แก้ไขข้อมูลรถ</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- start form -->
        @if ($name == 'BRAND')
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card border">
                                <div class="card-header"><b>แก้ไขยี่ห้อรถ</b></div>
                                <div class="card-body">
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
                                    <form action="{{ route('automotive.store-type') }}" class="wow fadeInLeft"
                                        method="post">
                                        @csrf
                                        <div class="col-12">
                                            <div class="col-md-9 col-lg-5 pt-2">
                                                <div class="form-group ">
                                                    <label class="control-label">ยี่่ห้อ (Brand)</label>
                                                    <input type="text"
                                                        class="form-control form-control-md form-control-required"
                                                        id="brand_name" name="brand_name" placeholder="กรุณาป้อนยี่่ห้อ"
                                                        value="{{$brands->brand_name}}">
                                                </div>
                                            </div>

                                            <div class="col-md-9 col-lg-5 pt-2">
                                                <div class="fom-group ">
                                                    <label class="control-label">หมายเหตุ :</label>
                                                    <textarea class="form-control form-control-md form-control-required" id="comment" name="comment" placeholder="..."
                                                        rows="3" value="">@if ($brands->comment) {{$brands->comment}} @endif</textarea>
                                                </div>
                                            </div>


                                            <div class="col-lg-12 col-md-12 col-sm-12 pt-3">
                                                <input type="hidden" name="SQL" value="EDIT">
                                                <input type="hidden" name="page" value="brand">
                                                <input type="hidden" name="ID" value="{{$brands->brand_id}}">
                                                <a class="btn btn-white" href="{{ url('/automotive/main') }}"><i
                                                        class="fe-arrow-left"></i> ย้อนกลับ</a>
                                                <button type="submit" class="btn btn-primary mx-2" id="btn-submit"><i
                                                        class="fe-save"></i> บันทึก</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($name == 'MODEL')
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card border">
                                <div class="card-header"><b>แก้ไขรุ่นรถ</b></div>
                                <div class="card-body">
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
                                    <form action="{{ route('automotive.store-type') }}" class="wow fadeInLeft"
                                        method="post">
                                        @csrf
                                        <div class="col-12">
                                            <div class="col-md-9 col-lg-5 pt-2">
                                                <div class="form-group ">
                                                    <label class="control-label">ยี่่ห้อ (Brand)</label>
                                                    <select class="form-select form-select-md form-control-required"
                                                        aria-label=".form-select-md" id="brand_id" name="brand_id">
                                                        <option value="" selected="selected">----- กรุณาเลือก
                                                            -----
                                                        </option>
                                                        @foreach ($_brand as $b)
                                                            <option value="{{ $b->brand_id }}"
                                                                @if ($b->brand_id == $models->brand_id) selected @endif>
                                                                {{ $b->brand_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-9 col-lg-5 pt-2">
                                                <div class="form-group ">
                                                    <label class="control-label">รุ่น (Model)</label>
                                                    <input type="text"
                                                        class="form-control form-control-md form-control-required"
                                                        id="model_name" name="model_name" placeholder="กรุณาป้อนรุ่นรถ"
                                                        value="{{ $models->model_name}}">
                                                </div>
                                            </div>

                                            <div class="col-md-9 col-lg-5 pt-2">
                                                <div class="fom-group ">
                                                    <label class="control-label">หมายเหตุ :</label>
                                                    <textarea class="form-control form-control-md form-control-required" id="comment" name="comment" placeholder="..."
                                                        rows="3" value="">@if ($brands->comment) {{$brands->comment}} @endif</textarea>
                                                </div>
                                            </div>


                                            <div class="col-lg-12 col-md-12 col-sm-12 pt-3">
                                                <input type="hidden" name="SQL" value="EDIT">
                                                <input type="hidden" name="page" value="model">
                                                <input type="hidden" name="ID" value="{{ $models->model_id }}">
                                                <a class="btn btn-white" href="{{ url('/automotive/main') }}"><i
                                                        class="fe-arrow-left"></i> ย้อนกลับ</a>
                                                <button type="submit" class="btn btn-primary mx-2" id="btn-submit"><i
                                                        class="fe-save"></i> บันทึก</button>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        @endif

        @if ($name == 'TYPES')
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card border">
                                <div class="card-header"><b>แก้ไขประเภทรถ</b></div>
                                <div class="card-body">
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
                                    <form action="{{ route('automotive.store-type') }}" class="wow fadeInLeft"
                                        method="post">
                                        @csrf
                                        <div class="col-12">
                                            <div class="col-md-9 col-lg-5 pt-2">
                                                <div class="form-group ">
                                                    <label class="control-label">ประเภทรถ</label>
                                                    <input type="text"
                                                        class="form-control form-control-md form-control-required"
                                                        id="type_name" name="type_name" placeholder="กรุณาป้อนประเภทรถ"
                                                        value="{{ $types->type_name }}">
                                                </div>
                                            </div>

                                            <div class="col-md-9 col-lg-5 pt-2">
                                                <div class="fom-group ">
                                                    <label class="control-label">หมายเหตุ :</label>
                                                    <textarea class="form-control form-control-md form-control-required" id="comment" name="comment" placeholder="..."
                                                        rows="3" value="">@if ($types->comment) {{$types->comment}} @endif</textarea>
                                                </div>
                                            </div>


                                            <div class="col-lg-12 col-md-12 col-sm-12 pt-3">
                                                <input type="hidden" name="SQL" value="EDIT">
                                                <input type="hidden" name="page" value="types">
                                                <input type="hidden" name="ID" value="{{ $types->type_id }}">
                                                <a class="btn btn-white" href="{{ url('/automotive/main') }}"><i
                                                        class="fe-arrow-left"></i> ย้อนกลับ</a>
                                                <button type="submit" class="btn btn-primary mx-2" id="btn-submit"><i
                                                        class="fe-save"></i> บันทึก</button>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- end form -->
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
@endsection
