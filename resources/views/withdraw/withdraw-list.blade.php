@extends('layouts.master-nopreloader-layout', ['page_title' => 'ใบเบิกอุปกรณ์'])
@section('css')
    <link href="{{ asset('assets/css/placeholder-loading.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @inject('thaiDateHelper', '\App\Services\ThaiDateHelperService')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">repair</a></li>
                            <li class="breadcrumb-item active">ใบเบิกอุปกรณ์</li>
                        </ol>
                    </div>
                    <h5 class="page-title">รายการอุปกรณ์-รหัสใบแจ้งซ่อม : {{ $oid }}</h5>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="float-end">
                        <a href="{{ url('/repair/withdraw-create', $oid) }}" class="btn btn-blue m-2" title="ACTION">
                            <i class="fe-list me-1"></i> เพิ่มรายการอุปกรณ์
                        </a>
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                            data-bs-target="#printwithdrawModal" {{ $count_data > 0 ? '' : 'disabled' }}>
                            <i class="mdi mdi-printer me-1"></i> Print
                        </button>
                    </div>
                    <table class="table table-striped text-nowrap" id="table" data-toggle="table"
                        data-loading-template="loadingTemplate" data-buttons-class="btn btn-sm btn-secondary"
                        data-ajax="ajaxRequest" data-query-params="queryParams" data-undefined-text=""
                        data-search="true" data-search-align="left" data-pagination="true" data-page-size="10">
                        <thead>
                            <tr>
                                <th data-field="products_name" data-sortable="true">ชื่ออุปกรณ์</th>
                                <th data-field="status_inventory" data-sortable="true">เบิกอุปกรณ์</th>
                                {{-- <th data-field="prices" data-sortable="true">ราคาต่อหน่วย</th> --}}
                                <th data-field="qty" data-sortable="true">จำนวน</th>
                                <th data-field="total_prices" data-sortable="true">รวมราคา</th>
                                <th data-field="emp_id" data-sortable="true">ผู้เบิก</th>
                                <th data-field="withdraw_date" data-sortable="true">วันที่</th>
                                <th data-field="comment" data-sortable="true">หมายเหตุ</th>
                                <th data-field="action" data-sortable="false" data-width="150">จัดการ</th>
                            </tr>
                        </thead>
                    </table>
                    
                    @if ($request->a == 1)
                        <a class="btn btn-secondary mt-3" href="{{ url('/repair/withdraw') }}" >
                            <i class="fe-arrow-left"></i> Back
                        </a>
                    @else
                        <a class="btn btn-secondary mt-3" href="{{ url('/repair/action') }}" >
                            <i class="fe-arrow-left"></i> Back
                        </a>
                    @endif
                </div>
            </div>
        </div>
        {{-- printwithdrawModal --}}
        <div id="printwithdrawModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" role="dialog" aria-labelledby="printwithdrawModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="printwithdrawModalLabel">พิมพ์ใบเบิกอุปกรณ์</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <embed src="{{ url('repair/withdraw-print-pdf', $oid) }}" frameborder="0" width="100%"
                            height="700px">
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/bootstrap-tables.init.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-table-style.js') }}"></script>
    <script type="text/javascript">
        function queryParams(params) {
            setTimeout(() => {
                params.oid = "{{ $oid }}";
                params.ostatus = "{{ $repairs->status }}";
            }, 200);
            return params;
        }

        function ajaxRequest(params) {
            setTimeout(() => {
                var url = "{{ route('withdraw.searchWithdraw') }}";
                $.get(url + '?' + $.param(params.data)).then(function(res) {
                    params.success(res)
                });
            }, 200);
        }

        function deleteConfirmation(id) {
            let url = "{{ url('repair/withdraw-delete') }}" + '/' + id;
            Swal.fire({
                icon: "warning",
                title: "คุณต้องการลบข้อมูล ใช่ไหม?",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ดำเนินการ!",
                cancelButtonText: "ยกเลิก",
                showLoaderOnConfirm: true,
                stopKeydownPropagation: false,
                preConfirm: () => {
                    return fetch(url)
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
                    }, 1500);
                }
            });
        }
    </script>
@endsection
