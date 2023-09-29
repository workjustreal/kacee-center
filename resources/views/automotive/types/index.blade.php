@extends('layouts.master-nopreloader-layout', ['page_title' => 'เพิ่มประเภทรถ'])
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
                            <li class="breadcrumb-item active">เพิ่มประเภทรถ</li>
                        </ol>
                    </div>
                    <h4 class="page-title">เพิ่มประเภทรถ</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- start table -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <div class="card card-border">
                                <div class="float-end">
                                    <a href="{{ url('/automotive/add/brand') }}"
                                        class="btn btn-lg btn-primary waves-effect waves-light me-1">
                                        <i class="mdi mdi-plus-circle me-1"></i> เพิ่มยี่ห้อ (brand)
                                    </a>
                                    <a href="{{ url('/automotive/add/model') }}"
                                        class="btn btn-lg btn-primary waves-effect waves-light me-1">
                                        <i class="mdi mdi-plus-circle me-1"></i> เพิ่มรุ่น (Model)
                                    </a>
                                    <a href="{{ url('/automotive/add/types') }}"
                                        class="btn btn-lg btn-primary waves-effect waves-light me-1">
                                        <i class="mdi mdi-plus-circle me-1"></i> เพิ่มประเภทรถ
                                    </a>
                                </div>
                            </div>

                            {{-- start brand --}}
                            <div class="col-6">
                                <div class="card border">
                                    <div class="card-header">ยี่ห้อ (Brand)</div>
                                    <div class="card-body">
                                        <div class="row justify-content-between mb-2">
                                            <div class="col-auto">
                                                <div class="row">
                                                    <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                                        <label for="searchBrand">ค้นหา</label>
                                                        <input type="text" class="form-control" placeholder="ค้นหา"
                                                            name="searchBrand" id="searchBrand" value="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <table class="table table-striped text-nowrap" id="tableBrand" data-toggle="table"
                                            data-loading-template="loadingTemplate"
                                            data-buttons-class="btn btn-sm btn-secondary" data-ajax="ajaxRequestBrand"
                                            data-query-params="queryParams" data-undefined-text="" data-search="true"
                                            data-search-align="left" data-pagination="true" data-search-selector="#searchBrand"
                                            data-page-size="5">
                                            <thead>
                                                <tr>
                                                    <th data-field="id" data-sortable="true">ID</th>
                                                    <th data-field="brand" data-sortable="true">ยี่ห้อ (brand)</th>
                                                    <th data-field="action" data-sortable="false" data-width="150"
                                                        data-width="200">
                                                        จัดการ
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            {{-- end brand --}}

                            {{-- start model --}}
                            <div class="col-6">
                                <div class="card border">
                                    <div class="card-header">รุ่น (Model)</div>
                                    <div class="card-body">
                                        <div class="row justify-content-between mb-2">
                                            <div class="col-auto">
                                                <div class="row">
                                                    <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                                        <label for="searchModel">ค้นหา</label>
                                                        <input type="text" class="form-control" placeholder="ค้นหา"
                                                            name="searchModel" id="searchModel" value="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <table class="table table-striped text-nowrap" id="tableModel" data-toggle="table"
                                            data-loading-template="loadingTemplate"
                                            data-buttons-class="btn btn-sm btn-secondary" data-ajax="ajaxRequestModel"
                                            data-query-params="queryParams" data-undefined-text="" data-search="true"
                                            data-search-align="left" data-pagination="true"
                                            data-search-selector="#searchModel" data-page-size="5">
                                            <thead>
                                                <tr>
                                                    <th data-field="id" data-sortable="true">ID</th>
                                                    <th data-field="brand" data-sortable="true">ยี่ห้อ (Brand)</th>
                                                    <th data-field="model" data-sortable="true">รุ่น (Model)</th>
                                                    <th data-field="action" data-sortable="false" data-width="150"
                                                        data-width="200">
                                                        จัดการ
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            {{-- end model --}}

                            {{-- start types --}}
                            <div class="col-6">
                                <div class="card border">
                                    <div class="card-header">ประเภทรถ</div>
                                    <div class="card-body">
                                        <div class="row justify-content-between mb-2">
                                            <div class="col-auto">
                                                <div class="row">
                                                    <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                                        <label for="searchTypes">ค้นหา</label>
                                                        <input type="text" class="form-control" placeholder="ค้นหา"
                                                            name="searchTypes" id="searchTypes" value="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <table class="table table-striped text-nowrap" id="tableTypes" data-toggle="table"
                                            data-loading-template="loadingTemplate"
                                            data-buttons-class="btn btn-sm btn-secondary" data-ajax="ajaxRequestTypes"
                                            data-query-params="queryParams" data-undefined-text="" data-search="true"
                                            data-search-align="left" data-pagination="true"
                                            data-search-selector="#searchTypes" data-page-size="5">
                                            <thead>
                                                <tr>
                                                    <th data-field="id" data-sortable="true">ID</th>
                                                    <th data-field="type" data-sortable="true">ประเภทรถ</th>
                                                    <th data-field="action" data-sortable="false" data-width="150"
                                                        data-width="200">
                                                        จัดการ
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            {{-- end types --}}

                        </div>
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
        function ajaxRequestBrand(params) {
            var url = "{{ route('automotive.searchBrand') }}";
            $.get(url + '?' + $.param(params.data)).then(function(res) {
                params.success(res)
            });
        }
        function ajaxRequestModel(params) {
            var url = "{{ route('automotive.searchModel') }}";
            $.get(url + '?' + $.param(params.data)).then(function(res) {
                params.success(res)
            });
        }
        function ajaxRequestTypes(params) {
            var url = "{{ route('automotive.searchTypes') }}";
            $.get(url + '?' + $.param(params.data)).then(function(res) {
                params.success(res)
            });
        }

        function deleteConfirmation(id, name) {
            // console.log(name);
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
                    return fetch(`/automotive/delete/` + id + `/` + name)
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
