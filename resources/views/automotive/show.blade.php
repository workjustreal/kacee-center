@extends('layouts.master-nopreloader-layout', ['page_title' => 'รายละเอียดข้อมูลรถ'])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/selectize/selectize.min.css') }}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">automotive</a></li>
                            <li class="breadcrumb-item active">รายละเอียดข้อมูลรถ</li>
                        </ol>
                    </div>
                    <h4 class="page-title">รายละเอียดข้อมูลรถ</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- start form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card card-border">
                            <div class="card-header">รายละเอียดข้อมูลรถ</div>
                            <div class="card-body">
                                @php
                                    switch ($car->status) {
                                        case '1':
                                            $_status = 'ปกติ';
                                            $_text = 'bg-success';
                                            break;
                                        case '0':
                                            $_status = 'ไม่ได้ใช้งาน';
                                            $_text = 'bg-danger';
                                            break;
                                    }
                                    $_models = $manageCar['_model'] ? $manageCar['_model']->model_name : '-';
                                @endphp
                                
                                <h5 class="mb-2">ทะเบียนรถ : <span class="text-primary px-2">{{ $car->car_id }}</span></h5>
                                <h5 class="mb-2">ยี่ห้อ (Brand) : <span class="text-primary px-2">{{ $manageCar['_brand']->brand_name }}</span></h5>
                                <h5 class="mb-2">รุ่น (Model) : <span class="text-primary px-2">{{ $_models }}</span></h5>
                                <h5 class="mb-2">ประเภทรถ : <span class="text-primary px-2">{{ $manageCar['_types']->type_name }}</span></h5>
                                <h5 class="mb-2">สี (Color) : <span class="text-primary px-2">{{ $car->color }}</span></h5>
                                <h5 class="mb-2">ฝ่าย / แผนก : <span class="text-primary px-2">{{ $car->dept_id }}</span></h5>
                                <h5 class="mb-2">รายละเอียด :<span class="text-primary px-2">{{ $car->comment ? $car->comment : '-' }}</span></h5>
                                <h5 class="mb-2">สถานะ : 
                                    <span class="badge {{$_text}} px-3">{{ $_status }}</span>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end form -->
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    
@endsection
