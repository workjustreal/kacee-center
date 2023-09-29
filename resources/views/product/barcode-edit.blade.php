@extends('layouts.master-layout', ['page_title' => 'แก้ไขบาร์โค้ดสินค้าที่สร้าง'])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item active">บาร์โค้ดสินค้า</li>
                        </ol>
                    </div>
                    <h4 class="page-title">แก้ไขบาร์โค้ดสินค้าที่สร้าง</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 mb-2">
                                <a href="/product/barcode-export/{{ $header->generate_id }}" type="button"
                                    class="btn btn-success btn-sm waves-effect waves-light float-end"><i
                                        class="fas fa-file-excel"></i> ดาวน์โหลดไฟล์</a>
                                <label
                                    class="form-label">รหัสการสร้าง&nbsp;:&nbsp;</label>{{ $header->generate_id }}<br>
                                <label class="form-label">หมายเหตุ&nbsp;:&nbsp;</label>{{ $header->remark }}<br>
                                <label
                                    class="form-label">ผู้สร้าง&nbsp;:&nbsp;</label>{{ $header->fname }}&nbsp;{{ $header->lname }}
                            </div>
                        </div>
                        <hr>
                        <table id="btn-editable" data-toggle="table" data-buttons-class="xs btn-light"
                            class="table-bordered" data-search="false">
                            <thead class="table-light">
                                <tr>
                                    <th data-field="no" data-sortable="false">ลำดับ</th>
                                    <th data-field="id" data-sortable="false" class="hidd">ID</th>
                                    <th data-field="sku" data-sortable="false">รหัสสินค้า</th>
                                    <th data-field="description" data-sortable="false">รายละเอียด</th>
                                    <th data-field="barcode" data-sortable="false">บาร์โค้ด</th>
                                    <th data-field="status" data-sortable="false">สถานะ</th>
                                    <th data-field="created_at" data-sortable="false">วันที่สร้าง</th>
                                    {{-- @if (auth()->user()->isAdmin())
                                    <th data-field="manage" data-sortable="false">จัดการ</th>
                                    @endif --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detail as $list)
                                    <tr>
                                        <td class="lh35">{{ $loop->index + 1 }}</td>
                                        <td class="lh35 hidd">{{ $list->id }}</td>
                                        <td class="lh35">{{ $list->sku }}</td>
                                        <td class="lh35">{{ $list->description }}</td>
                                        <td class="lh35">{{ $list->barcode }}</td>
                                        <td class="lh35">
                                            @if ($list->status == '1')
                                                <span class="badge bg-success">ปกติ</span>
                                            @else
                                                <span class="badge bg-danger">ยกเลิก</span>
                                            @endif
                                        </td>
                                        <td class="lh35">
                                            {{ \Carbon\Carbon::parse($list->created_at)->format('d/m/Y H:i:s') }}</td>
                                        {{-- @if (auth()->user()->isAdmin())
                                        <td class="lh35">
                                            <a href="#" onclick="cancelConfirmationBarcode({{ $list->id }})"
                                                    class="btn btn-danger btn-sm waves-effect waves-light">
                                                    <i class="fas fa-minus-circle"></i> ยกเลิก</a>
                                        </td>
                                        @endif --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
    <script src="{{ asset('assets/libs/jquery-tabledit/jquery-tabledit.min.js') }}"></script>
    <!-- third party js ends -->
    <script type="text/javascript">
        $(document).ready(function() {
            var isAdmin = "{{ auth()->user()->isAdmin() }}";
            var isUser = "{{ auth()->user()->id }}";
            var isCreateUser = "{{ $header->userid }}";
            if (isAdmin || isUser == isCreateUser) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    }
                });
                $("#btn-editable").Tabledit({
                    url: "{{ Route('barcode.action') }}",
                    buttons: {
                        edit: {
                            class: "btn btn-info mb-1",
                            html: '<span class="mdi mdi-pencil"></span>',
                            action: "edit"
                        },
                        save: {
                            class: "btn btn-sm btn-success mb-1",
                            html: '<span class="mdi mdi-content-save-edit"></span>',
                            action: "save"
                        },
                        delete: {
                            class: "btn btn-sm btn-danger mb-1",
                            html: '<span class="mdi mdi-delete"></span>',
                            action: "delete"
                        },
                        confirm: {
                            class: 'btn btn-sm btn-danger',
                            html: 'Confirm'
                        }
                    },
                    inputClass: "form-control form-control-sm",
                    deleteButton: (isAdmin) ? 1 : !1,
                    saveButton: 1,
                    autoFocus: 1,
                    columns: {
                        identifier: [1, "id"],
                        editable: [
                            [3, "description"]
                        ]
                    },
                    onDraw: function() {
                        console.log('onDraw()');
                    },
                    onSuccess: function(data, textStatus, jqXHR) {
                        console.log('onSuccess(data, textStatus, jqXHR)');
                        console.log(data);
                        console.log(textStatus);
                        console.log(jqXHR);
                    },
                    onFail: function(jqXHR, textStatus, errorThrown) {
                        console.log('onFail(jqXHR, textStatus, errorThrown)');
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    },
                    onAlways: function() {
                        console.log('onAlways()');
                    },
                    onAjax: function(action, serialize) {
                        console.log('onAjax(action, serialize)');
                        console.log(action);
                        console.log(serialize);
                    }
                });
            }
        });
    </script>
@endsection
