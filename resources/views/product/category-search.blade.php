@extends('layouts.master-layout', ['page_title' => "จัดกลุ่มสินค้า"])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->
    <style>
        div.dt-buttons {
            position: relative;
            float: right;
        }
        .dataTables_filter > label {
            display: none;
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
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div id="loadingXL" class="text-center"></div>
                                <p id="totalRecord" style="float: left;"></p>
                                <table id="productTable" class="display dataTable table table-striped dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>รหัสสินค้า</th>
                                            <th>บาร์โค้ด</th>
                                            <th>รายละเอียด</th>
                                            <th>หมวดหมู่ยอดขายรวม</th>
                                            <th>หมวดหมู่หลัก</th>
                                            <th>หมวดหมู่รอง</th>
                                            <th>รุ่นสินค้า</th>
                                            <th>สี</th>
                                            <th>ขนาด</th>
                                            <th>หมวดหมู่แผนกออนไลน์</th>
                                            <th>รายงานรายวัน</th>
                                            <th>แก้ไข</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
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
    <script src="{{ asset('assets/js/barcodes/index.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatables/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap3-typeahead.js') }}"></script>
    <script src="{{ asset('assets/js/product-category-search.js') }}"></script>
    <!-- third party js ends -->
    <script type="text/javascript">
        var myEditModal = new bootstrap.Modal(document.getElementById('editModal'));
        $(document).ready(function() {
            setTimeout(() => {
                product_data();
            }, 500);
            $('#sale_category, #main_category, #sec_category, #online_category, #daily_category').on('change', function() {
                product_data();
            });
        });

        function product_data(isSearch = '') {
            var manage = "{{ Auth::User()->manageProductCat() }}";
            var userid = "{{ Auth::User()->id }}";
            var sale_category = $('#sale_category').val();
            var main_category = $('#main_category').val();
            var sec_category = $('#sec_category').val();
            var online_category = $('#online_category').val();
            var daily_category = $('#daily_category').val();
            var res = $.ajax({
                url: "{{ Route('pd.category.search') }}",
                method: 'GET',
                data: {
                    sale_category: sale_category,
                    main_category: main_category,
                    sec_category: sec_category,
                    online_category: online_category,
                    daily_category: daily_category,
                },
                dataType: 'json',
                beforeSend: function() {
                    $("#productTable").css("visibility", "hidden");
                    $('#loadingXL').slideDown();
                    $('#loadingXL').html('<div class="spinner-grow avatar-md text-secondary m-2" role="status"><span class="visually-hidden">Loading...</span></div>');
                },
                complete: function() {
                    $('#loadingXL').slideUp();
                    $("#productTable").css("visibility", "visible");
                },
            }).then(function(json, textStatus, jqXHR){
                var data = [];
                for ( var i=0 ; i<json.data.length ; i++ ) {
                    var btnEdit = ``;
                    if (manage) {
                        btnEdit = `<i class="fas fa-pen" role="button" onclick="edit('` + json.data[i].stkcod + `')" title="Edit"></i>`;
                    }
                    data.push( [
                        (i+1).toLocaleString("en-US"),
                        `<div class="d-flex justify-content-between align-items-center"><span class="text-line me-2">`+json.data[i].stkcod+`</span><i class="fas fa-copy copy-button" role="button" onclick="copy('` + json.data[i].stkcod + `','#copy_button_` + i + `')" id="copy_button_` + i + `" title="Copy"></i></div>`,
                        `<div class="d-flex justify-content-between align-items-center"><span class="text-line me-2">`+json.data[i].barcod+`</span><i class="fas fa-copy copy-button" role="button" onclick="copy('` + json.data[i].barcod + `','#copy2_button_` + i + `')" id="copy2_button_` + i + `" title="Copy"></i></div>`,
                        json.data[i].stkdes,
                        json.data[i].sale_category,
                        json.data[i].main_category,
                        json.data[i].sec_category,
                        json.data[i].model,
                        json.data[i].color_code,
                        json.data[i].size,
                        json.data[i].online_category,
                        json.data[i].daily_category,
                        btnEdit
                    ] );
                }
                var table = $('#productTable').DataTable({
                    destroy: true,
                    data:           data,
                    deferRender:    true,
                    scrollX: true,
                    // scrollY:        400,
                    scrollCollapse: true,
                    scroller:       true,
                    dom: 'Blfrtip',
                    'lengthMenu': [25, 50, 100],
                    pageLength: 25,
                    buttons: ['print', 'excel', 'colvis'],
                    buttons: [
                        {
                            extend: 'print',
                            className: 'btn btn-sm btn-light',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            className: 'btn btn-sm btn-light',
                            exportOptions: {
                                columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11 ],
                                format: {
                                    body: function (data, row, column, node ) {
                                        switch (column) {
                                            case 1:
                                                text = data.replace(/<[^>]*>/g, "");
                                                break;
                                            case 2:
                                                text = data.replace(/<[^>]*>/g, "");
                                                break;
                                            default:
                                                text = data;
                                        }
                                        return text;
                                    }
                                }
                            },
                            customize: function( xlsx ) {
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                var col = $('col', sheet);
                                col.each(function () {
                                    $(this).attr('width', 15);
                                });
                                $(col[0]).attr('width', 8);
                            }
                        },
                        {
                            extend: 'colvis',
                            className: 'btn btn-sm btn-light',
                            columns: ':not(.noVis)'
                        },
                    ],
                    columnDefs: [
                        { targets: 2, visible: false }
                    ],
                    "language": {
                        "paginate": {
                            "previous": "<i class='mdi mdi-chevron-left'>",
                            "next": "<i class='mdi mdi-chevron-right'>"
                        },
                        "buttons": {
                            "collection": "ชุดข้อมูล",
                            "colvis": "การมองเห็นคอลัมน์",
                            "colvisRestore": "เรียกคืนการมองเห็น",
                        },
                        "lengthMenu": "แสดง _MENU_ รายการ",
                        "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                        "infoEmpty": "แสดงทั้งหมด 0 ถึง 0 จาก 0 รายการ",
                        "infoFiltered": "(กรองข้อมูลทั้งหมด _MAX_ รายการ)",
                        "emptyTable": "ไม่มีข้อมูลในตาราง",
                        "zeroRecords": "ไม่พบข้อมูล",
                    },
                    "drawCallback": function() {
                        $('.dataTables_paginate > .pagination').addClass(
                            'pagination-rounded');
                    },
                });
                var timeout = null;
                $('#search').on('keyup', function() {
                    var search = this.value;
                    clearTimeout(timeout);
                    timeout = setTimeout(function() {
                        table.search( search ).draw();
                    }, 500);
                });
                if (isSearch != '') {
                    table.search( isSearch ).draw();
                }
                $("#productTable").css("visibility", "visible");
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
                                        product_data($("#search").val());
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
