@extends('layouts.master-layout', ['page_title' => 'คู่มือระบบ'])
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                            <li class="breadcrumb-item active">คู่มือระบบ</li>
                        </ol>
                    </div>
                    <h4 class="page-title">คู่มือระบบ</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between p-0">
                        <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                            <label for="search">ค้นหา</label>
                            <input type="text" class="form-control" placeholder="ค้นหา" name="search" id="search"
                                autocomplete="off">
                        </div>
                        @if (Auth::User()->roleAdmin())
                            <div class="col-lg-auto col-md-auto col-sm-12 float-end">
                                <a class="btn btn-primary waves-effect waves-light" href="#" data-bs-toggle="modal"
                                    data-bs-target="#addManualModal" title="ดูรายละเอียด">
                                    <i class="mdi mdi-clipboard-list-outline"></i> เพิ่มคู่มือระบบ
                                </a>
                            </div>
                        @endif
                        
                    </div>
                    <table class="table table-striped text-nowrap" id="table" data-toggle="table"
                        data-loading-template="loadingTemplate" data-buttons-class="btn btn-sm btn-secondary"
                        data-ajax="ajaxRequest" data-query-params="queryParams" data-undefined-text="" data-search="true"
                        data-search-align="left" data-pagination="true" data-search-selector="#search" data-page-size="10">
                        <thead>
                            <tr>
                                <th data-field="manual_id" data-visible="true">ลำดับ</th>
                                <th data-field="manual_name" data-visible="true">ชื่อคู่มือ</th>
                                <th data-field="manual_file" data-sortable="true">ไฟล์</th>
                                <th data-field="created_at" data-sortable="true">วันที่สร้าง</th>
                                <th data-field="action" data-sortable="false">จัดการ</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addManualModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    
                    <div class="modal-header">
                        <h4>เพิ่มคู่มือระบบ</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-0">
                        {{-- form --}}
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('manual.store') }}" class="wow fadeInLeft" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="file_names" name="file_name" required>
                                            <label for="file_names">ชื่อคู่มือ</label>
                                        </div>

                                        <div class="form-group mb-3">
                                            <input type="file" class="form-control" name="pdf_file" accept="application/pdf, application/vnd.ms-excel" required>
                                        </div>

                                        <div class="float-end">
                                            <button type="submit" class="btn btn-primary mx-2" id="btn-submit">
                                                <i class="fe-save"></i> บันทึก
                                            </button>
                                        </div>
                                        
                                    </div>
                                    
                                </form>
                            </div>
                        </div>
                        {{-- End form --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/bootstrap-tables.init.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-table-style.js') }}"></script>

    <script type="text/javascript">
        function ajaxRequest(params) {
            var url = "{{ route('manual.search') }}";
            $.get(url + '?' + $.param(params.data)).then(function(res) {
                params.success(res)
            });
        }

        function deleteConfirmation(id) {
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
                    let url = '/manual/manual-del/' + id;
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
                    }, 2000);
                }
            });
        }
    </script>
@endsection
