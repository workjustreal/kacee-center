@extends('layouts.master-nopreloader-layout', ['page_title' => 'ข้อมูลรถ'])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/css/placeholder-loading.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item active">ข้อมูลรถ</li>
                        </ol>
                    </div>
                    <h4 class="page-title">ข้อมูลรถ</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- start table -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="float-end">
                            <a href="{{ url('/automotive/create') }}" class="btn btn-lg btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus-circle me-1"></i> เพิ่มข้อมูลรถ
                            </a>
                        </div>

                        <div class="row justify-content-between mb-2">
                            <div class="col-auto">
                                <div class="row">
                                    <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                        <label for="brand_category">ยี่ห้อรถ</label>
                                        <select class="form-select" name="brand_category" id="brand_category">
                                            <option value="" selected>ทั้งหมด</option>
                                            @foreach ($brands as $b)
                                                <option value="{{ $b->brand_id }}">
                                                    {{ $b->brand_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                        <label for="type_category">ประเภทรถ</label>
                                        <select class="form-select" name="type_category" id="type_category">
                                            <option value="" selected>ทั้งหมด</option>
                                            @foreach ($types as $t)
                                                <option value="{{ $t->type_id }}">{{ $t->type_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                        <label for="search">ค้นหา</label>
                                        <input type="text" class="form-control" placeholder="ค้นหา" name="search"
                                            id="search" value="">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <table class="table table-striped text-nowrap" id="table" data-toggle="table"
                            data-loading-template="loadingTemplate" data-buttons-class="btn btn-sm btn-secondary"
                            data-ajax="ajaxRequest" data-query-params="queryParams" data-undefined-text=""
                            data-search="true" data-search-align="left" data-pagination="true"
                            data-search-selector="#search" data-page-size="10">
                            <thead>
                                <tr>
                                    <th data-field="id" data-sortable="true">ID</th>
                                    <th data-field="car_id" data-sortable="true">ทะเบียนรถ</th>
                                    <th data-field="brand" data-sortable="true">brand</th>
                                    <th data-field="model" data-sortable="true">model</th>
                                    <th data-field="type" data-sortable="true">ประเภทรถ</th>
                                    <th data-field="color" data-sortable="true">สีรถ</th>
                                    <th data-field="dept_id" data-sortable="true">แผนก</th>
                                    <th data-field="status" data-sortable="true">สถานะ</th>
                                    <th data-field="action" data-sortable="false" data-width="150" data-width="200">
                                        จัดการ
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end table form -->
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/bootstrap-tables.init.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-table-style.js') }}"></script>
    <script type="text/javascript">
        var $table = $('#table');
        $(document).ready(function() {
            $("#brand_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    brand_category: $("#brand_category").val()
                });
                rebuild();
            });
            $("#model_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    model_category: $("#model_category").val()
                });
                rebuild();
            });
            $("#type_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    type_category: $("#type_category").val()
                });
                rebuild();
            });
            $("#dept_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    dept_category: $("#dept_category").val()
                });
                rebuild();
            });
        });

        function queryParams(params) {
            params.brand_category = $("#brand_category").val();
            params.model_category = $("#model_category").val();
            params.type_category = $("#type_category").val();
            params.dept_category = $("#dept_category").val();
            return params;
        }


        function ajaxRequest(params) {
            var url = "{{ route('automotive.search') }}";
            $.get(url + '?' + $.param(params.data)).then(function(res) {
                params.success(res)
            });
        }

        function deleteCarConfirmation(id) {
            Swal.fire({
                icon: "warning",
                title: "คุณต้องการลบข้อมูลพนักงาน ใช่ไหม?",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ดำเนินการลบ!",
                cancelButtonText: "ยกเลิก",
                showLoaderOnConfirm: true,
                stopKeydownPropagation: false,
                preConfirm: () => {
                    return fetch(`/automotive/del/` + id)
                        .then((response) => {
                            if (!response.ok) {
                                throw new Error(response.statusText);
                            }
                            return response.json();
                        })
                        .catch((error) => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading(),
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: "success",
                        title: "ลบข้อมูลเรียบร้อย!",
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            });
        }
    </script>
@endsection
