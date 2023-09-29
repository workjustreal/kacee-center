@extends('layouts.master-nopreloader-layout', ['page_title' => 'แก้ไขข้อมูลรถ'])
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
                            <li class="breadcrumb-item active">แก้ไขข้อมูลรถ</li>
                        </ol>
                    </div>
                    <h4 class="page-title">แก้ไขข้อมูลรถ</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- start form -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card border">
                            <div class="card-header"><b>แก้ไขข้อมูลรถ</b></div>
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
                                <form action="{{ route('automotive.store') }}" class="wow fadeInLeft" method="post">
                                    @csrf
                                    <div class="col-12">
                                        <div class="col-md-9 col-lg-5 pt-2">
                                            <div class="form-group ">
                                                <label class="control-label">ทะเบียนรถ</label>
                                                <input type="text"
                                                    class="form-control form-control-md form-control-required"
                                                    id="car_id" name="car_id" placeholder="----- กรุณากรอก -----"
                                                    value="{{ $car->car_id }}">
                                            </div>
                                        </div>

                                        <div class="col-md-9 col-lg-5 pt-2">
                                            <div class="form-group ">
                                                <label class="control-label">ยี่ห้อ (Brand)</label>
                                                <select class="form-select form-select-md form-control-required"
                                                    aria-label=".form-select-md" id="car_brand" name="car_brand"
                                                    onchange="getModel(this);">
                                                    <option value="" selected="selected">----- กรุณาเลือก
                                                        -----
                                                    </option>
                                                    @foreach ($brands as $b)
                                                        <option value="{{ $b->brand_id }}"
                                                            @if ($car->brand == $b->brand_id) selected @endif >
                                                            {{ $b->brand_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-9 col-lg-5 pt-2">
                                            <div class="form-group ">
                                                <label class="control-label">รุ่น (Model)</label>
                                                <select class="form-select form-select-md form-control-required"
                                                    aria-label=".form-select-md" id="car_model" name="car_model">
                                                    <option value="" selected="selected">----- กรุณาเลือก
                                                        -----
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-9 col-lg-5 pt-2">
                                            <div class="form-group ">
                                                <label class="control-label">ประเภทรถ</label>
                                                <select class="form-select-md selectize-programmatic" id="car_type"
                                                    name="car_type">
                                                    <option value="" selected="selected">----- กรุณาเลือก
                                                        -----
                                                    </option>
                                                    @foreach ($types as $t)
                                                        <option value="{{ $t->type_id }}" 
                                                            @if ($car->type == $t->type_id) selected @endif >
                                                            {{ $t->type_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-9 col-lg-5 pt-2">
                                            <div class="form-group ">
                                                <label class="control-label">สี (Color)</label>
                                                <input type="text"
                                                    class="form-control form-control-md form-control-required"
                                                    id="car_color" name="car_color" placeholder="----- กรุณากรอก -----"
                                                    value="{{ $car->color }}">
                                            </div>
                                        </div>

                                        <div class="col-md-9 col-lg-5 pt-2">
                                            <div class="form-group ">
                                                <label class="control-label">แผนก</label>
                                                <select class="selectize-programmatic" aria-label=".form-select-md"
                                                    id="dept_id" name="dept_id">
                                                    <option value="" selected="selected">----- กรุณาเลือก
                                                        -----</option>
                                                    @foreach ($department as $list)
                                                        <option value="{{ $list->dept_id }}"
                                                            @if ($car->dept_id ==$list->dept_id) selected @endif >
                                                            @if ($list->level == '0')
                                                                *
                                                            @endif
                                                            @if ($list->level == '1')
                                                                -
                                                            @endif
                                                            @if ($list->level == '2')
                                                                --
                                                            @endif
                                                            @if ($list->level == '3')
                                                                ---
                                                            @endif
                                                            @if ($list->level == '4')
                                                                ----
                                                            @endif
                                                            {{ $list->dept_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-9 col-lg-5 pt-2">
                                            <div class="fom-group ">
                                                <label class="control-label">สถานะ :</label>
                                                <br>
                                                <div class="form-check form-check-inline range_full m-2">
                                                    <input class="form-check-input" type="radio" name="car_status"
                                                        id="normal" value="1" @if ($car->status == 1) checked @endif >
                                                    <label class="form-check-label" for="normal">ปกติ</label>
                                                </div>

                                                <div class="form-check form-check-inline range_full m-2">
                                                    <input class="form-check-input" type="radio" name="car_status"
                                                        id="not" value="0" @if ($car->status == 0) checked @endif >
                                                    <label class="form-check-label" for="not">ไม่ใช้งาน</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-9 col-lg-5 pt-2">
                                            <div class="fom-group ">
                                                <label class="control-label">หมายเหตุ :</label>
                                                <textarea class="form-control form-control-md form-control-required" id="car_detail" name="car_detail"
                                                    placeholder="..." rows="2" value="">{{ $car->comment ? $car->comment: ''}}</textarea>
                                            </div>
                                        </div>


                                        <div class="col-lg-12 col-md-12 col-sm-12 pt-3">
                                            <input type="hidden" name="SQL" value="EDIT">
                                            <input type="hidden" name="ID" value="{{ $car->id }}">
                                            <a class="btn btn-white" href="{{ url('/automotive/automotive') }}"><i
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
        <!-- end form -->
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.selectize-programmatic').selectize();
            getModel();
        });

        function getModel() {
            let model = "{{ $car->model }}";
            let brand = document.querySelector("#car_brand");
            if (brand.value != "") {
                let url = "{{ url('automotive/get_model_parent') }}/" + brand.value;
                fetch(url)
                    .then((response) => response.json())
                    .then((result) => {
                        let modelParent = document.querySelector("#car_model");
                        modelParent.innerHTML = '<option value="">----- กรุณาเลือก -----</option>';
                        for (let i = 0; i < result.data.length; i++) {
                            let option = document.createElement("option");
                            option.value = result.data[i].model_id;
                            option.text = result.data[i].model_name;
                            if (model == result.data[i].model_id) {
                                option.selected = true;
                            }
                            modelParent.appendChild(option);
                        }
                    });
            }
        }
    </script>
@endsection
