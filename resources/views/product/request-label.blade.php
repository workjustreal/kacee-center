@extends('layouts.master-layout', ['page_title' => "ร้องขอสติ๊กเกอร์บาร์โค้ด"])
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Product</a></li>
                        <li class="breadcrumb-item active">ร้องขอสติ๊กเกอร์บาร์โค้ด</li>
                    </ol>
                </div>
                <h4 class="page-title">ร้องขอสติ๊กเกอร์บาร์โค้ด</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between mb-2">
                        <div class="col-auto">
                            <div class="mb-2">
                                <input type="text" class="form-control" id="search" name="search" autocomplete="off" placeholder="ค้นหา">
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="mb-2 text-sm-end">
                                <a href="{{ url('product/request-label/create') }}" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-plus-circle me-1"></i> ร้องขอสติ๊กเกอร์ </a>
                            </div>
                        </div>
                    </div>
                    <table id="table" data-toggle="table" data-ajax="ajaxRequest" data-search="true"
                        data-search-align="left" data-pagination="true" data-search-selector="#search" data-page-size="10" class="table">
                        <thead>
                            <tr>
                                <th data-field="no" data-sortable="true" data-width="100">ลำดับ</th>
                                <th data-field="request_id" data-sortable="true" data-width="200">รหัสร้องขอ</th>
                                <th data-field="label" data-sortable="true">ขนาดสติ๊กเกอร์</th>
                                <th data-field="sku_total" data-sortable="true">รหัสสินค้ารวม</th>
                                <th data-field="qty_total" data-sortable="true">จำนวนรวม</th>
                                <th data-field="remark" data-sortable="true">หมายเหตุ</th>
                                <th data-field="user" data-sortable="true">ผู้ร้องขอ</th>
                                <th data-field="dept" data-sortable="true">หน่วยงาน/แผนก</th>
                                <th data-field="date" data-sortable="true">วันที่ร้องขอ</th>
                                <th data-field="status" data-sortable="true">สถานะ</th>
                                <th data-field="action" data-sortable="false" data-width="120">จัดการ</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    var $table = $("#table");
    function ajaxRequest(params) {
        var url = "{{ route('request-label.search') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    function deleteRequestLabelConfirmation(request_id) {
        Swal.fire({
            icon: "warning",
            title: "คุณต้องการลบข้อมูลใช่ไหม?",
            text: "รหัสร้องขอ: #" + request_id,
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการลบ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch(`/product/request-label/del/` + request_id)
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
                    timer: 2000,
                });
                $table.bootstrapTable("refreshOptions", {
                    search: $("#search").val(),
                });
            }
        });
    }
</script>
@endsection