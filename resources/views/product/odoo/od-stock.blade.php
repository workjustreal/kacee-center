@extends('layouts.masterpreloader-layout', ['page_title' => 'Odoo Stock'])
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
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                            <li class="breadcrumb-item active">Odoo Stock</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Stock</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                <label for="stock">คลังสินค้า</label>
                                <select class="form-select" name="stock" id="stock">
                                    <option value="all" selected disabled>เลือกคลังสินค้า</option>
                                    @foreach ($location as $location)
                                        <option value="{{ $location->code }}">{{ $location->name }}</option>
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
                                <input type="text" class="form-control" placeholder="ค้นหาสินค้า" name="search"
                                    id="search" value="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div id="loadingXL" class="text-center"></div>
                                <p id="totalRecord" style="float: left;"></p>
                                <table id="productTable"
                                    class="display dataTable table table-striped dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">คลังสินค้า</th>
                                            <th rowspan="2">SKU</th>
                                            <th rowspan="2">ชื่อสินค้า</th>
                                            <th rowspan="2">หน่วยนับ</th>
                                            <th id="th_qty_ex" class="text-center">Qty Express</th>
                                        </tr>
                                        <tr id="tr_subheader">
                                            <th id="stsum" class="text-center" width="60">รวม</th>
                                            <th id="odoo" class="text-center" width="60">Odoo</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card ribbon-box">
                    <div class="card-body">
                        <div class="ribbon ribbon-primary float-start"><i class="mdi mdi-circle-edit-outline me-1"></i>
                            สินค้าไม่พบใน Express
                        </div>
                        <div class="ribbon-content">
                            <div class="row">
                                <div class="col-12">
                                    <table id="tbnone" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>คลัง</th>
                                                <th>SKU</th>
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
            $("#odoo").prop("disabled", true);
            $('#stock,#odoo').on('change', function() {
                product_data();
                none_ex();
                $("#odoo").prop("disabled", false);
            });
        });

        function product_data(isSearch = '') {
            var stock = $('#stock').val();
            var odoo = $('#odoo').val();
            $.ajax({
                url: "{{ Route('od.stock.search') }}",
                method: 'GET',
                data: {
                    stock: stock,
                    status: odoo,
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
                var stor_list = [];
                if (stock == "ON") {
                    stor_list = ["06", "OL"];
                } else if (stock == "F4") {
                    stor_list = ["06", "10"];
                } else if (stock == "RA") {
                    stor_list = ["06", "10"];
                } else if (stock == "PT") {
                    stor_list = ["06", "10"];
                } else {
                    stor_list = ["06"];
                }
                $("#th_qty_ex").attr("colspan", stor_list.length + 1);
                var subheader = '';
                for (let s = 0; s < stor_list.length; s++) {
                    subheader += '<th class="text-center" width="60">' + stor_list[s] + '</th>';
                }
                subheader += '<th class="text-center" width="60">รวม</th>';
                subheader += '<th class="text-center" width="60">Odoo</th>';
                $("#tr_subheader").html(subheader);
                var data = [];
                for (var i = 0; i < json.data.length; i++) {
                    var list = JSON.parse(json.data[i].list);
                    var _stock = [];
                    for (let s = 0; s < stor_list.length; s++) {
                        var _chk = false;
                        for (let l = 0; l < list.length; l++) {
                            if (stor_list[s] == list[l].storage) {
                                _stock.push(list[l].qty);
                                _chk = true;
                                break;
                            }
                        }
                        if (_chk == false) {
                            _stock.push(0);
                        }
                    }
                    let sum = 0;
                    _stock.forEach(item => {
                        sum += item;
                    });
                    _stock.push(sum);
                    var _data = [json.data[i].loccod, json.data[i].stkcod, json.data[i].name, json.data[i].unit];
                    for (let s = 0; s < _stock.length; s++) {
                        _data.push(_stock[s]);
                    }
                    if (json.data[i].status == "Y") {
                        _data.push('<span class="badge badge-soft-success">yes</span>');
                    } else if (json.data[i].status == "N") {
                        _data.push('<span class="badge badge-soft-danger">no</span>');
                    } else {
                        _data.push('-');
                    }
                    data.push(_data);
                }
                if ($.fn.dataTable.isDataTable('#productTable')) {
                    $('#productTable').DataTable().destroy();
                    $('#productTable').empty();
                    $('#productTable').html('<table id="productTable"\
                                               class="display dataTable table table-striped dt-responsive nowrap w-100">\
                                               <thead>\
                                                   <tr>\
                                                       <th rowspan="2">คลังสินค้า</th>\
                                                       <th rowspan="2">SKU</th>\
                                                       <th rowspan="2">ชื่อสินค้า</th>\
                                                       <th id="th_qty_ex" class="text-center">Qty Express</th>\
                                                   </tr>\
                                                   <tr id="tr_subheader">\
                                                       <th id="stsum" class="text-center">รวม</th>\
                                                       <th id="stsum" class="text-center">Odoo</th>\
                                                   </tr>\
                                               </thead>\
                                           </table>');
                    $("#th_qty_ex").attr("colspan", stor_list.length + 1);
                    var subheader = '';
                    for (let s = 0; s < stor_list.length; s++) {
                        subheader += '<th class="text-center" width="60">' + stor_list[s] + '</th>';
                    }
                    subheader += '<th class="text-center" width="60">รวม</th>';
                    subheader += '<th class="text-center" width="60">Odoo</th>';
                    $("#tr_subheader").html(subheader);
                }
                var _col_export = stor_list.length == 1 ? [0, 1, 2, 3, 4, 5, 6, ] : [0, 1, 2, 3, 4, 5, 6, 7, ];
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
                            title: 'Stock_Odoo_' + new Date().getTime(),
                            className: 'btn btn-sm btn-light',
                            exportOptions: {
                                columns: _col_export,
                                format: {
                                    body: function(data, row, column, node) {
                                        var _col = stor_list.length == 1 ? 6 : 7;
                                        return column === _col ? data.replace(/<.*?>/g, '') : data;
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

        function none_ex() {
            var stock = $('#stock').val();
            $.ajax({
                url: "{{ route('od.none.ex') }}",
                method: 'GET',
                data: {
                    stock: stock,
                },
                dataType: 'json',
                beforeSend: function() {
                    $("#tbnone").css("visibility", "hidden");
                },
                complete: function() {
                    $("#tbnone").css("visibility", "visible");
                },
            }).then(function(json) {
                var data = [];
                for (var i = 0; i < json.noneex.length; i++) {
                    data.push([
                        json.noneex[i].loccod,
                        json.noneex[i].stkcod,
                    ]);
                }
                $('#tbnone').dataTable({
                    destroy: true,
                    data: data,
                    dom: 'Blfrtip',
                    'lengthMenu': [10, 25, 50, 100],
                    pageLength: 10,
                    buttons: ['print', 'excel'],
                    buttons: [{
                            extend: 'print',
                            className: 'btn btn-sm btn-light',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            title: 'noneStock_ex' + new Date().getTime(),
                            className: 'btn btn-sm btn-light',
                            exportOptions: {
                                columns: [0, 1, ],
                                format: {
                                    body: function(data, row, column, node) {
                                        return data;
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
                    ],
                });
                $("#tbnone").css("visibility", "visible");
            });
        }
    </script>
@endsection
