@extends('layouts.master-layout', ['page_title' => "จัดกลุ่มสินค้า"])
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                            <li class="breadcrumb-item active">สินค้า</li>
                        </ol>
                    </div>
                    <h4 class="page-title">จัดกลุ่มสินค้า</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                <label for="sale_category">หมวดหมู่ดูยอดขายรวม</label>
                                <select class="form-select" name="sale_category" id="sale_category">
                                    <option value="all" selected>หมวดหมู่สินค้าทั้งหมด</option>
                                    <option value="none">--ไม่มีหมวดหมู่--</option>
                                    @foreach ($sale_category as $sale_category)
                                        <option value="{{ $sale_category->sale_category }}">
                                            {{ $sale_category->sale_category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                <label for="main_category">หมวดหมู่หลัก</label>
                                <select class="form-select" name="main_category" id="main_category">
                                    <option value="all" selected>หมวดหมู่สินค้าทั้งหมด</option>
                                    <option value="none">--ไม่มีหมวดหมู่--</option>
                                    @foreach ($main_category as $main_category)
                                        <option value="{{ $main_category->main_category }}">
                                            {{ $main_category->main_category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                <label for="sec_category">หมวดหมู่รอง</label>
                                <select class="form-select" name="sec_category" id="sec_category">
                                    <option value="all" selected>หมวดหมู่สินค้าทั้งหมด</option>
                                    <option value="none">--ไม่มีหมวดหมู่--</option>
                                    @foreach ($sec_category as $sec_category)
                                        <option value="{{ $sec_category->sec_category }}">
                                            {{ $sec_category->sec_category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                <label for="online_category">หมวดหมู่แผนกออนไลน์</label>
                                <select class="form-select" name="online_category" id="online_category">
                                    <option value="all" selected>หมวดหมู่สินค้าทั้งหมด</option>
                                    <option value="none">--ไม่มีหมวดหมู่--</option>
                                    @foreach ($online_category as $online_category)
                                        <option value="{{ $online_category->online_category }}">
                                            {{ $online_category->online_category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                <label for="daily_category">รายงานรายวัน</label>
                                <select class="form-select" name="daily_category" id="daily_category">
                                    <option value="all" selected>ทั้งหมด</option>
                                    <option value="none">--ไม่มี--</option>
                                    @foreach ($daily_category as $daily_category)
                                        <option value="{{ $daily_category->daily_category }}">
                                            {{ $daily_category->daily_category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                <label for="search">ค้นหา</label>
                                <input type="text" class="form-control" placeholder="ค้นหาสินค้า" name="search" id="search" value="">
                            </div>
                            @if (Auth::User()->manageProductCat())
                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                <br><a class="btn btn-primary waves-effect waves-light float-end" href="{{ url('product/category-file') }}">อัปเดตข้อมูล</a>
                            </div>
                            @endif
                        </div>
                        <div id="toolbar" class="row"></div>
                        <table id="table"
                            data-toggle="table"
                            data-loading-template="loadingTemplate"
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
                                "fileName": "จัดกลุ่มสินค้า",
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
                                <tr class="text-center invisible">
                                    @if (Auth::User()->manageProductCat())
                                    <th colspan="13">ค้นหาสินค้า</th>
                                    @else
                                    <th colspan="12">ค้นหาสินค้า</th>
                                    @endif
                                </tr>
                                <tr>
                                    <th data-field="no" data-sortable="true" data-force-export="true">ลำดับ</th>
                                    <th data-field="stkcod" data-sortable="true" data-formatter="nameFormatterSKU" data-force-export="true">รหัสสินค้า</th>
                                    <th data-field="barcod" data-sortable="true" data-formatter="nameFormatterBarcode" data-visible="false" data-force-export="true">บาร์โค้ด</th>
                                    <th data-field="stkdes" data-sortable="true" data-force-export="true">รายละเอียด</th>
                                    <th data-field="sale_category" data-sortable="true" data-force-export="true">หมวดหมู่ยอดขายรวม</th>
                                    <th data-field="main_category" data-sortable="true" data-force-export="true">หมวดหมู่หลัก</th>
                                    <th data-field="sec_category" data-sortable="true" data-force-export="true">หมวดหมู่รอง</th>
                                    <th data-field="model" data-sortable="true" data-force-export="true">รุ่นสินค้า</th>
                                    <th data-field="color_code" data-sortable="true" data-force-export="true">สี</th>
                                    <th data-field="size" data-sortable="true" data-force-export="true">ขนาด</th>
                                    <th data-field="online_category" data-sortable="true" data-force-export="true">หมวดหมู่แผนกออนไลน์</th>
                                    <th data-field="daily_category" data-sortable="true" data-force-export="true">รายงานรายวัน</th>
                                    @if (Auth::User()->manageProductCat())
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
                                <label for="edit_stkcod" class="text-primary">รหัสสินค้า</label>
                                <input type="text" class="form-control bg-light" id="edit_stkcod" name="edit_stkcod" readonly>
                            </div>
                            <div class="col-12 mb-2">
                                <label for="edit_stkdes" class="text-primary">รายละเอียด</label>
                                <input type="text" class="form-control bg-light" id="edit_stkdes" name="edit_stkdes" readonly>
                            </div>
                            <div class="col-12 mb-2">
                                <label for="edit_sale_category" class="text-primary">หมวดหมู่ดูยอดขายรวม</label>
                                <input type="text" class="form-control typeahead" id="edit_sale_category" name="edit_sale_category" autocomplete="off">
                            </div>
                            <div class="col-12 mb-2">
                                <label for="edit_main_category" class="text-primary">หมวดหมู่หลัก</label>
                                <input type="text" class="form-control typeahead" id="edit_main_category" name="edit_main_category" autocomplete="off">
                            </div>
                            <div class="col-12 mb-2">
                                <label for="edit_sec_category" class="text-primary">หมวดหมู่รอง</label>
                                <input type="text" class="form-control typeahead" id="edit_sec_category" name="edit_sec_category" autocomplete="off">
                            </div>
                            <div class="col-12 mb-2">
                                <label for="edit_model" class="text-primary">รุ่นสินค้า</label>
                                <input type="text" class="form-control typeahead" id="edit_model" name="edit_model" autocomplete="off">
                            </div>
                            <div class="col-12 mb-2">
                                <label for="edit_color_code" class="text-primary">สี</label>
                                <input type="text" class="form-control typeahead" id="edit_color_code" name="edit_color_code" autocomplete="off">
                            </div>
                            <div class="col-12 mb-2">
                                <label for="edit_size" class="text-primary">ขนาด</label>
                                <input type="text" class="form-control typeahead" id="edit_size" name="edit_size" autocomplete="off">
                            </div>
                            <div class="col-12 mb-2">
                                <label for="edit_online_category" class="text-primary">หมวดหมู่แผนกออนไลน์</label>
                                <input type="text" class="form-control typeahead" id="edit_online_category" name="edit_online_category" autocomplete="off">
                            </div>
                            <div class="col-12 mb-2">
                                <label for="edit_daily_category" class="text-primary">รายงานรายวัน</label>
                                <input type="text" class="form-control typeahead" id="edit_daily_category" name="edit_daily_category" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">ปิด</button>
                        <button type="button" class="btn btn-primary" onclick="updateProductConfirmation();">บันทึก</button>
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
    <script src="{{asset('assets/js/bootstrap-table-style.js')}}"></script>
    <script src="{{ asset('assets/js/barcodes/index.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap3-typeahead.js') }}"></script>
    <script src="{{ asset('assets/js/product-category-search.js') }}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/xlsx.core.min.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/tableExport.min.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/bootstrap-table-export.min.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/bootstrap-table-print.min.js')}}"></script>
    <!-- third party js ends -->
    <script type="text/javascript">
        var $table = $('#table');
        var myEditModal = new bootstrap.Modal(document.getElementById('editModal'));
        $(document).ready(function() {
            $("#sale_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    sale_category: $("#sale_category").val()
                });
                rebuild();
            });
            $("#main_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    main_category: $("#main_category").val()
                });
                rebuild();
            });
            $("#sec_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    sec_category: $("#sec_category").val()
                });
                rebuild();
            });
            $("#online_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    online_category: $("#online_category").val()
                });
                rebuild();
            });
            $("#daily_category").change(function() {
                $table.bootstrapTable('refreshOptions', {
                    daily_category: $("#daily_category").val()
                });
                rebuild();
            });
        });
        function nameFormatterSKU(value, row) {
            return `<div class="d-flex justify-content-between align-items-center"><span class="text-line me-2">`+row.stkcod+`</span><i class="fas fa-copy copy-button" role="button" onclick="copy('` + row.stkcod + `','#copy_button_` + (row.no-1) + `')" id="copy_button_` + (row.no-1) + `" title="Copy"></i></div>`;
        }
        function nameFormatterBarcode(value, row) {
            return `<div class="d-flex justify-content-between align-items-center"><span class="text-line me-2">`+row.barcod+`</span><i class="fas fa-copy copy-button" role="button" onclick="copy('` + row.barcod + `','#copy2_button_` + (row.no-1) + `')" id="copy2_button_` + (row.no-1) + `" title="Copy"></i></div>`;
        }
        function queryParams(params) {
            params.sale_category = $("#sale_category").val();
            params.main_category = $("#main_category").val();
            params.sec_category = $("#sec_category").val();
            params.online_category = $("#online_category").val();
            params.daily_category = $("#daily_category").val();
            $('button[name="print"]').html('<i class="dripicons-print mt-1"></i>');
            $('div.export > button').html('<i class="dripicons-download mt-1"></i>');
            $('div.keep-open > button').html('<i class="dripicons-checklist mt-1"></i>');
            return params;
        }
        function ajaxRequest(params) {
            var url = "{{ Route('pd.category.search2') }}";
            $.get(url + '?' + $.param(params.data)).then(function (res) {
                params.success(res)
            });
        }

        function edit(stkcod) {
            $.ajax({
                url: "{{ url('product/stkcod-edit/search') }}",
                method: 'GET',
                data: {
                    search: stkcod
                },
                dataType: 'json',
                success: function(res) {
                    if (Object.keys(res).length !== 0) {
                        $('#edit_stkcod').val(res.stkcod);
                        $('#edit_stkdes').val(res.stkdes);
                        $('#edit_sale_category').val(res.sale_category);
                        $('#edit_main_category').val(res.main_category);
                        $('#edit_sec_category').val(res.sec_category);
                        $('#edit_online_category').val(res.online_category);
                        $('#edit_daily_category').val(res.daily_category);
                        $('#edit_model').val(res.model);
                        $('#edit_color_code').val(res.color_code);
                        $('#edit_size').val(res.size);
                        myEditModal.show();
                    }
                }
            });
        }

        function updateProductConfirmation() {
            var stkcod = $('#edit_stkcod').val();
            if (stkcod.length >= 2) {
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
                            url: "{{ url('/product/category-edit-update') }}",
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                stkcod: $('#edit_stkcod').val(),
                                sale_category: $('#edit_sale_category').val(),
                                main_category: $('#edit_main_category').val(),
                                sec_category: $('#edit_sec_category').val(),
                                online_category: $('#edit_online_category').val(),
                                daily_category: $('#edit_daily_category').val(),
                                model: $('#edit_model').val(),
                                color_code: $('#edit_color_code').val(),
                                size: $('#edit_size').val(),
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
                                            sale_category: $("#sale_category").val()
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
