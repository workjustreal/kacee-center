@extends('layouts.master-layout', ['page_title' => 'บันทึกวันทำงานฝ่ายขาย'])
@section('css')
    <link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .form-switch .form-check-input {
            height: 24px !important;
            width: 45px !important;
            background-color: #fd5d49 !important;
            border-color: #ffffff !important;
        }
        .form-switch .form-check-input:checked {
            background-color: #26b99a !important;
            border-color: #ffffff !important;
        }
        .form-switch .form-check-input {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='white'/%3e%3c/svg%3e") !important;
        }
        .form-switch .form-check-input:focus {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='white'/%3e%3c/svg%3e") !important;
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Kacee</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Leave</a></li>
                            <li class="breadcrumb-item active">บันทึกวันทำงานฝ่ายขาย</li>
                        </ol>
                    </div>
                    <h4 class="page-title">บันทึกวันทำงานฝ่ายขาย</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" id="dept" name="dept" value="{{ $header->dept_id }}">
                        <input type="hidden" id="month" name="month" value="{{ $header->month.'/'.$header->year }}">
                        <input type="hidden" id="id" name="id" value="{{ $header->lr_id }}">
                        <div id="divTable" class="table-responsive"></div>
                        <div class="mt-3 mb-3 text-center">
                            <a href="{{ url('leave/leave-record') }}" class="btn btn-soft-secondary waves-effect waves-light me-3"><i class="mdi mdi-keyboard-backspace me-1"></i>ย้อนกลับ</a>
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
    <script src="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/bootstrap-tables.init.js') }}"></script>
    <!-- third party js ends -->
    <script type="text/javascript">
        $(document).ready(function() {
            setTimeout(() => {
                getData();
            }, 500);
        });

        function getData() {
            var dept = $("#dept").val();
            var month = $("#month").val();
            var id = $("#id").val();
            if ((dept != "" && dept != null) && (month != "" && month != null) && id > 0) {
                $.ajax({
                    url: "{{ url('leave/leave-record-view/search') }}",
                    method: 'GET',
                    data: {
                        dept: dept,
                        month: month,
                        id: id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#divTable').html(data.table_data);
                    }
                });
            }
        }
    </script>
@endsection
