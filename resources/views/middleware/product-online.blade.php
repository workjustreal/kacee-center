@extends('layouts.master-layout', ['page_title' => "จัดกลุ่มสินค้าออนไลน์"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Middleware</a></li>
                            <li class="breadcrumb-item active">จัดกลุ่มสินค้าออนไลน์</li>
                        </ol>
                    </div>
                    <h4 class="page-title">จัดกลุ่มสินค้าออนไลน์</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <div class="row">
                                    <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                        <label for="category">หมวดหมู่</label>
                                        <select class="form-select" name="category" id="category">
                                            <option value="all" selected>ทั้งหมด</option>
                                            <option value="none">--ไม่มีหมวดหมู่--</option>
                                            @foreach ($category as $cat)
                                                <option value="{{ $cat->category }}">
                                                    {{ $cat->category }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                        <label for="search">ค้นหา</label>
                                        <input type="text" class="form-control" placeholder="ค้นหาสินค้า" name="search" id="search" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                <label>&nbsp;</label>
                                <div class="text-sm-end">
                                    @if (Auth::User()->manageProductCatOnline() || Auth::User()->isDeptSaleOnline())
                                        <a class="btn btn-primary waves-effect waves-light float-end" href="{{ url('middleware/product-online/category-file') }}">อัปเดตข้อมูล</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div id="toolbar" class="row"></div>
                        <table id="table"
                            data-toggle="table"
                            data-loading-template="loadingTemplate"
                            data-cookie="true"
                            data-cookie-id-table="productOnlinePageId"
                            data-buttons-class="btn btn-sm btn-secondary"
                            data-toolbar="#toolbar"
                            data-ajax="ajaxRequest"
                            data-query-params="queryParams"
                            data-undefined-text=""
                            data-search="true"
                            data-search-align="left"
                            data-search-selector="#search"
                            data-pagination="true"
                            data-page-size="25"
                            data-show-columns="true"
                            data-show-print="true"
                            data-show-export="true"
                            data-export-data-type="all"
                            data-export-types='["excel"]'
                            data-export-options='{
                                "fileName": "จัดกลุ่มสินค้าออนไลน์",
                                "mso": {
                                    "fileFormat": "xlsx",
                                    "worksheetName": ["Sheet1"],
                                    "xlsx": {
                                        "formatId": {
                                            "numbers": 1
                                        }
                                    }
                                },
                                "ignoreColumn": ["manage"]
                            }'
                            class="table table-striped text-nowrap">
                            <thead>
                                <tr>
                                    <th data-field="no" data-sortable="true" data-force-export="true" data-width="100">ลำดับ</th>
                                    <th data-field="stkcod" data-sortable="true" data-formatter="nameFormatterSKU" data-force-export="true">รหัสสินค้า</th>
                                    <th data-field="name" data-sortable="true" data-force-export="true">ชื่อสินค้า</th>
                                    <th data-field="category" data-sortable="true" data-force-export="true">หมวดหมู่</th>
                                    @if (Auth::User()->manageProductCatOnline() || Auth::User()->isDeptSaleOnline())
                                    <th data-field="manage" data-sortable="false" data-print-ignore="true">แก้ไข</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="fullWidthModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h4 class="modal-title">แก้ไขข้อมูลสินค้า</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label for="edit_sku" class="text-primary">รหัสสินค้า</label>
                                <input type="hidden" class="form-control" id="edit_id" name="edit_id" readonly>
                                <input type="text" class="form-control bg-light" id="edit_sku" name="edit_sku" readonly>
                            </div>
                            <div class="col-12 mb-2">
                                <label for="edit_name" class="text-primary">ชื่อสินค้า</label>
                                <input type="text" class="form-control" id="edit_name" name="edit_name">
                            </div>
                            <div class="col-12 mb-2">
                                <label for="edit_category" class="text-primary">หมวดหมู่</label>
                                <input type="text" class="form-control typeahead" id="edit_category" name="edit_category" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-danger" onclick="deleteProductConfirmation();">ลบ</button>
                        <div>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">ปิด</button>
                            <button type="button" class="btn btn-primary" onclick="updateProductConfirmation();">บันทึก</button>
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
    <script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/bootstrap-table-cookie.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap-table-style.js')}}"></script>
    <script src="{{ asset('assets/js/barcodes/index.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap3-typeahead.js') }}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/xlsx.core.min.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/tableExport.min.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/bootstrap-table-export.min.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/bootstrap-table-print.min.js')}}"></script>
    <!-- third party js ends -->
    <script type="text/javascript">
        var $table = $('#table');
        var myEditModal = new bootstrap.Modal(document.getElementById('editModal'));
        var base_url = window.location.protocol + "//" + window.location.host;
        $(document).ready(function() {
            $("#category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    category: $("#category").val()
                });
                rebuild();
            });
            $("#edit_category").typeahead({
                minLength: 1,
                items: 10,
                showHintOnFocus: "all",
                selectOnBlur: false,
                autoSelect: true,
                displayText: function (item) {
                    return item.category;
                },
                afterSelect: function (item) {
                    this.$element[0].value = item.category;
                    $("#edit_category").val(item.category);
                },
                source: function (search, process) {
                    return $.get(
                        base_url + "/middleware/product-online/category/search",
                        { search: search },
                        function (data) {
                            return process(data);
                        }
                    );
                },
            });
        });
        function nameFormatterSKU(value, row) {
            return `<div class="d-flex justify-content-between align-items-center"><span class="text-line me-2">`+row.sku+`</span><i class="fas fa-copy copy-button" role="button" onclick="copy('` + row.sku + `','#copy_button_` + (row.no-1) + `')" id="copy_button_` + (row.no-1) + `" title="Copy"></i></div>`;
        }
        function queryParams(params) {
            setTimeout(() => {
                params.category = $("#category").val();
                params.chanel = $("#chanel").val();
                $('button[name="print"]').html('<i class="dripicons-print mt-1"></i>');
                $('div.export > button').html('<i class="dripicons-download mt-1"></i>');
                $('div.keep-open > button').html('<i class="dripicons-checklist mt-1"></i>');
            }, 200);
            return params;
        }
        function ajaxRequest(params) {
            setTimeout(() => {
                var url = "{{ url('middleware/product-online/search') }}";
                $.get(url + '?' + $.param(params.data)).then(function (res) {
                    params.success(res)
                });
            }, 500);
        }

        function edit(id, sku) {
            $.ajax({
                url: "{{ url('middleware/product-online/sku-edit/search') }}",
                method: 'GET',
                data: {
                    id: id,
                    search: sku
                },
                dataType: 'json',
                success: function(res) {
                    if (Object.keys(res).length !== 0) {
                        $('#edit_id').val(res.id);
                        $('#edit_sku').val(res.sku);
                        $('#edit_name').val(res.name);
                        $('#edit_category').val(res.category);
                        myEditModal.show();
                    }
                }
            });
        }

        function updateProductConfirmation() {
            var id = $('#edit_id').val();
            var sku = $('#edit_sku').val();
            if (id > 0 && sku.length >= 2) {
                Swal.fire({
                    icon: "warning",
                    title: 'ยืนยันการบันทึกข้อมูล ใช่ไหม?',
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "ยืนยัน!",
                    cancelButtonText: "ยกเลิก",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': '{{csrf_token()}}'
                            }
                        });
                        $.ajax({
                            url: "{{ url('middleware/product-online/category-edit-update') }}",
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                id: $('#edit_id').val(),
                                sku: $('#edit_sku').val(),
                                name: $('#edit_name').val(),
                                category: $('#edit_category').val(),
                            },
                            success: function(res) {
                                if (res.success == true) {
                                    Swal.fire({
                                        icon: "success",
                                        title: res.message,
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                    myEditModal.hide();
                                    setTimeout(() => {
                                        $table.bootstrapTable('refreshOptions', {
                                            category: $("#category").val()
                                        });
                                        rebuild();
                                    }, 500);
                                } else {
                                    Swal.fire({
                                        icon: "warning",
                                        title: res.message,
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                }
                            }
                        });
                    }
                });
            }
        }

        function deleteProductConfirmation() {
            var id = $('#edit_id').val();
            var sku = $('#edit_sku').val();
            if (id > 0 && sku.length >= 2) {
                Swal.fire({
                    icon: "warning",
                    title: 'ยืนยันการลบข้อมูล ใช่ไหม?',
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "ยืนยัน!",
                    cancelButtonText: "ยกเลิก",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': '{{csrf_token()}}'
                            }
                        });
                        $.ajax({
                            url: "{{ url('middleware/product-online/category-edit-delete') }}",
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                id: $('#edit_id').val(),
                                sku: $('#edit_sku').val(),
                                name: $('#edit_name').val(),
                                category: $('#edit_category').val(),
                            },
                            success: function(res) {
                                if (res.success == true) {
                                    Swal.fire({
                                        icon: "success",
                                        title: res.message,
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                    myEditModal.hide();
                                    setTimeout(() => {
                                        $table.bootstrapTable('refreshOptions', {
                                            category: $("#category").val()
                                        });
                                        rebuild();
                                    }, 500);
                                } else {
                                    Swal.fire({
                                        icon: "warning",
                                        title: res.message,
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                }
                            }
                        });
                    }
                });
            }
        }
    </script>
@endsection
