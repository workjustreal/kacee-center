@extends('layouts.master-layout', ['page_title' => "ค้นหาสินค้า"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
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
                <h4 class="page-title">ค้นหาสินค้า</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-auto col-md-3 col-sm-12 mb-2">
                            <select class="form-select" name="group" id="group">
                                <option value="all" selected>กลุ่มสินค้าทั้งหมด</option>
                                @foreach ($groups as $group)
                                <option value="{{ $group->typcod }}">
                                    {{ $group->typdes }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-auto col-md-4 col-sm-12 mb-2">
                            <input type="text" class="form-control" placeholder="ค้นหาสินค้า" name="search" id="search" value="">
                        </div>
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
                                        <th>ราคา</th>
                                        <th>กลุ่ม</th>
                                        <th>ซีรีย์</th>
                                        <th>ประเภท</th>
                                        <th>เพิ่มเติม</th>
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
@endsection
@section('script')
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script src="{{asset('assets/js/barcodes/index.js')}}"></script>
<script src="{{asset('assets/libs/datatables/datatables.min.js')}}"></script>
<script src="{{asset('assets/libs/jszip/jszip.min.js')}}"></script>
<script src="{{ asset('assets/js/datatables/buttons.colVis.min.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    $(document).ready(function() {
        setTimeout(() => {
            product_data();
        }, 500);
        $('#group').on('change', function() {
            product_data();
        });
    });

    function product_data() {
            var group = $('#group').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var res = $.ajax({
                url: "{{ Route('pd.search') }}",
                method: 'GET',
                data: {
                    group: group,
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
                error: function(xhr) {
                    console.log(xhr.responseText);
                },
            }).then(function(json, textStatus, jqXHR){
                var data = [];
                for ( var i=0 ; i<json.data.length ; i++ ) {
                    data.push( [
                        (i+1).toLocaleString("en-US"),
                        `<div class="d-flex justify-content-between align-items-center"><span class="text-line me-2">`+json.data[i].stkcod+`</span><i class="fas fa-copy copy-button" role="button" onclick="copy('` + json.data[i].stkcod + `','#copy_button_` + i + `')" id="copy_button_` + i + `" title="Copy"></i></div>`,
                        `<div class="d-flex justify-content-between align-items-center"><span class="text-line me-2">`+json.data[i].barcod+`</span><i class="fas fa-copy copy-button" role="button" onclick="copy('` + json.data[i].barcod + `','#copy2_button_` + i + `')" id="copy2_button_` + i + `" title="Copy"></i></div>`,
                        // `<div class="d-flex justify-content-between align-items-center"><span class="text-line me-2">\u200C`+json.data[i].stkcod+`</span><i class="fas fa-copy copy-button" role="button" onclick="copy('` + json.data[i].stkcod + `','#copy_button_` + i + `')" id="copy_button_` + i + `" title="Copy"></i></div>`,
                        // `<div class="d-flex justify-content-between align-items-center"><span class="text-line me-2">\u200C`+json.data[i].barcod+`</span><i class="fas fa-copy copy-button" role="button" onclick="copy('` + json.data[i].barcod + `','#copy2_button_` + i + `')" id="copy2_button_` + i + `" title="Copy"></i></div>`,
                        json.data[i].stkdes,
                        json.data[i].sellpr1,
                        json.data[i].typdes,
                        json.data[i].series,
                        json.data[i].product_type,
                        json.data[i].detail
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
                            // exportOptions: {
                            //     columns: ':visible'
                            // }
                        },
                        {
                            extend: 'excel',
                            className: 'btn btn-sm btn-light',
                            // exportOptions: {
                            //     columns: ':visible'
                            // }
                            exportOptions: {
                                columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ],
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
                $("#productTable").css("visibility", "visible");
            });
        }

    // function product_data2() {
    //     $('#datatable-buttons').hide();
    //     var table = $('#datatable-buttons').DataTable();
    //     table.destroy();
    //     var group = $('#group').val();
    //     var search = $('#search').val();
    //     var show = $('input[name="show"]:checked').val();
    //     $.ajax({
    //         url: "{{ Route('pd.search') }}",
    //         method: 'GET',
    //         data: {
    //             group: group,
    //             search: search,
    //             show: show,
    //         },
    //         dataType: 'json',
    //         beforeSend: function(){
    //             $('#loadingXL').slideDown();
    //             $('#loadingXL').html('<div class="spinner-grow avatar-md text-secondary m-2" role="status"><span class="visually-hidden">Loading...</span></div>');
    //         },
    //         success: function(res) {
    //             var htmlView = '';
    //             if (res.data.length <= 0) {
    //                 $("#totalRecord").text('');
    //                 htmlView += `
    //                         <tr>
    //                             <td>ไม่พบข้อมูล</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
    //                         </tr>`;
    //             } else {
    //                 $("#totalRecord").text("ทั้งหมด: "+res.total.toLocaleString("en-US")+" รายการ");
    //                 for (let i = 0; i < res.data.length; i++) {
    //                     htmlView += `
    //                             <tr>
    //                                 <td class="lh35">` + (i + 1).toLocaleString("en-US") + `</td>
    //                                 <td class="lh35">
    //                                     <div class="d-flex justify-content-between align-items-center">
    //                                         <span class="text-line me-2">` + res.data[i].stkcod + `</span>
    //                                         <i class="fas fa-copy copy-button" role="button" onclick="copy('` + res
    //                         .data[i].stkcod + `','#copy_button_` + i + `')" id="copy_button_` + i + `" title="Copy"></i>
    //                                     </div>
    //                                 </td>
    //                                 <td class="lh35">
    //                                     <div class="d-flex justify-content-between align-items-center">
    //                                         <span class="text-line me-2">` + res.data[i].barcod + `</span>
    //                                         <i class="fas fa-copy copy-button" role="button" onclick="copy('` + res
    //                         .data[i].barcod + `','#copy2_button_` + i + `')" id="copy2_button_` + i + `" title="Copy"></i>
    //                                     </div>
    //                                 </td>
    //                                 <td class="lh35">` + res.data[i].stkdes + `</td>
    //                                 <td class="lh35">` + res.data[i].typdes + `</td>
    //                                 <td class="lh35">` + res.data[i].series + `</td>
    //                                 <td class="lh35">` + res.data[i].product_type + `</td>
    //                                 <td class="lh35">` + res.data[i].detail + `</td>
    //                             </tr>`;
    //                 }
    //             }
    //             $('tbody').html(htmlView);
    //             $('#datatable-buttons').show();
    //             if (res.data.length > 0) {
    //                 var table = $('#datatable-buttons').DataTable();
    //                 table.destroy();
    //                 var table = $('#datatable-buttons').DataTable({
    //                     dom: 'Bfrtip',
    //                     lengthChange: false,
    //                     searching: false,
    //                     pageLength: 25,
    //                     buttons: ['print', 'excel'],
    //                     buttons: [
    //                         { extend: 'print', className: 'btn-light' },
    //                         { extend: 'excel', className: 'btn-light' },
    //                     ],
    //                     "language": {
    //                         "paginate": {
    //                             "previous": "<i class='mdi mdi-chevron-left'>",
    //                             "next": "<i class='mdi mdi-chevron-right'>"
    //                         }
    //                     },
    //                     "drawCallback": function () {
    //                         $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
    //                     },
    //                 });
    //             }
    //         },
    //         complete: function(){
    //             $('#loading').hide();
    //             $('#loadingXL').slideUp();
    //         }
    //     });
    // }
</script>
@endsection
