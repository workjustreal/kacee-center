@extends('layouts.master-layout', ['page_title' => 'รายละเอียดสต๊อก'])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->
    <style>
        div.dt-buttons {
            position: relative;
            float: right;
        }

        .dataTables_filter>label {
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">STOCK</a></li>
                            <li class="breadcrumb-item active">DETAIL</li>
                        </ol>
                    </div>
                    <h4 class="page-title">สต๊อกสินค้า</h4>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-box">
                                        <div class="row">
                                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                                <label for="stock">คลังสินค้า</label>
                                                <select class="form-select" name="stock" id="stock">
                                                    <option value="all" selected>คลังสินค้าทั้งหมด</option>
                                                    <option value="none">== ไม่มีคลังสินค้า ==</option>
                                                    @foreach ($location as $loca)
                                                        <option value="{{ $loca->typcod }}">
                                                            <p>{{ $loca->typcod }}</p> : {{ $loca->typdes }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                                <label for="category">หมวดหมู่</label>
                                                <select class="form-select" name="category" id="category">
                                                    <option value="all" selected>หมวดหมู่ทั้งหมด</option>
                                                    <option value="none">== ไม่มีหมวดหมู่ ==</option>
                                                    @foreach ($category as $cat)
                                                        <option value="{{ $cat->sale_category }}">
                                                            {{ $cat->sale_category }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                                <label for="category">Odoo Status</label>
                                                <select class="form-select" name="odoo" id="odoo">
                                                    <option value="all" selected>สถานะทั้งหมด</option>
                                                    <option value="none">== ไม่มีสถานะ ==</option>
                                                    <option value="Y">Yes</option>
                                                    <option value="N">No</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                                <label for="search">ค้นหา</label>
                                                <input type="text" class="form-control" placeholder="ค้นหาสินค้า"
                                                    name="search" id="search" value="">
                                            </div>
                                            <p class="text-end text-primary">อัพเดทล่าสุด : <b>{{ $update->updated_at }}</b>
                                            </p>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div id="loadingXL" class="text-center"></div>
                                                <p id="totalRecord" style="float: left;"></p>
                                                <table id="productTable"
                                                    class="display dataTable table table-striped dt-responsive nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">SKU</th>
                                                            <th scope="col">ชื่อสินค้า</th>
                                                            <th scope="col">รหัสคลัง</th>
                                                            <th scope="col">คลังสินค้า</th>
                                                            <th scope="col">จำนวนสินค้า</th>
                                                            <th scope="col">หน่วย</th>
                                                            <th scope="col">หมวดหมู่</th>
                                                            <th scope="col">วันที่เคลื่อนไหวล่าสุด</th>
                                                            <th scope="col">Odoo</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
    <script src="{{ asset('assets/js/barcodes/index.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatables/buttons.colVis.min.js') }}"></script>
    <!-- third party js ends -->

    <script>
        $(document).ready(function() {
            setTimeout(() => {
                product_data();
            }, 500);
            $('#stock, #category,#odoo').on('change', function() {
                product_data();
            });
        });

        function product_data(isSearch = '') {
            var stock = $('#stock').val();
            var category = $('#category').val();
            var status = $('#odoo').val();
            $.ajax({
                url: "{{ Route('stock.search') }}",
                method: 'GET',
                data: {
                    stock: stock,
                    category: category,
                    status: status,
                },
                dataType: 'json',
                beforeSend: function() {
                    $("#productTable").css("visibility", "hidden");
                    $('#loadingXL').slideDown();
                    $('#loadingXL').html(
                        '<div class="spinner-grow avatar-md text-secondary m-2" role="status"><span class="visually-hidden">Loading...</span></div>'
                    );
                },
                complete: function() {
                    $('#loadingXL').slideUp();
                    $("#productTable").css("visibility", "visible");
                },
            }).then(function(json, textStatus, jqXHR) {
                var data = [];
                for (var i = 0; i < json.data.length; i++) {
                    var _data = ([
                        json.data[i].sku,
                        json.data[i].name,
                        json.data[i].storage,
                        json.data[i].storage_des,
                        json.data[i].qty,
                        json.data[i].unit_des,
                        json.data[i].sale_category,
                        json.data[i].lmov_date,
                    ]);
                    if (json.data[i].status == "Y") {
                        _data.push('<span class="badge badge-soft-success">yes</span>');
                    } else if (json.data[i].status == "N") {
                        _data.push('<span class="badge badge-soft-danger">no</span>');
                    } else {
                        _data.push('-');
                    }
                    data.push(_data);
                }
                var table = $('#productTable').DataTable({
                    destroy: true,
                    data: data,
                    deferRender: true,
                    scrollX: true,
                    // scrollY:        400,
                    scrollCollapse: true,
                    scroller: true,
                    dom: 'Blfrtip',
                    'lengthMenu': [25, 50, 100],
                    pageLength: 25,
                    buttons: ['print', 'excel', 'colvis'],
                    buttons: [{
                            extend: 'print',
                            className: 'btn btn-sm btn-light',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            title: 'Stock_Express_' + new Date().getTime(),
                            className: 'btn btn-sm btn-light',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                                format: {
                                    body: function(data, row, column, node) {
                                        return column === 8 ? data.replace(/<.*?>/g, '') :
                                            data;
                                    }
                                }
                            },
                            customize: function(xlsx) {
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                $('row c[r^="C"]', sheet).attr('s', '50');
                                var col = $('col', sheet);
                                col.each(function() {
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
                    // columnDefs: [{
                    //     targets: 2,
                    //     visible: false
                    // }],
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
                        table.search(search).draw();
                    }, 500);
                });
                if (isSearch != '') {
                    table.search(isSearch).draw();
                }
                $("#productTable").css("visibility", "visible");
            });
        }
    </script>
@endsection
