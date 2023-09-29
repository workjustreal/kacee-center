@extends('layouts.master-layout', ['page_title' => 'สินค้าไม่พบในสต๊อก'])
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">ODOO</a></li>
                            <li class="breadcrumb-item active">NONE STOCK</li>
                        </ol>
                    </div>
                    <h4 class="page-title">สินค้าไม่พบในคลัง</h4>
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
                                                            <th scope="col">คลัง</th>
                                                            <th scope="col">จำนวน</th>
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
            $('#odoo, #stock').on('change', function() {
                product_data();
            });
        });

        function product_data(isSearch = '') {
            var status = $('#odoo').val();
            var stock = $('#stock').val();
            $.ajax({
                url: "{{ Route('od.searchNoneStock') }}",
                method: 'GET',
                data: {
                    stock: stock,
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
                    var _data = [
                        json.data[i].sku,
                        json.data[i].name,
                        json.data[i].storage,
                        json.data[i].storage_des,
                        json.data[i].total_qty,
                        json.data[i].lmov_date,
                        // new Date(json.data[i].updated_at).toLocaleString(),
                    ];
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
                            title: 'NoneStock_' + new Date().getTime(),
                            className: 'btn btn-sm btn-light',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6],
                                format: {
                                    body: function(data, row, column, node) {
                                        return column === 6 ? data.replace(/<.*?>/g, '') :
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
