@extends('layouts.master-layout', ['page_title' => "จัดการระบบงาน"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">ระบบงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">จัดการระบบงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-0 text-left mt-1 mb-3">
                        <a href="{{url('/admin/application/add')}}" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-plus-circle me-1"></i> เพิ่มระบบงาน </a>
                    </div>
                    <table data-toggle="table" data-page-size="10" data-buttons-class="xs btn-light" data-pagination="true"
                        class="table-bordered" data-search="false">
                        <thead class="table-light">
                            <tr>
                                <th data-field="no" data-sortable="false" data-width="80">ลำดับ</th>
                                <th data-field="app_id" data-sortable="false">APP ID</th>
                                <th data-field="icon" data-sortable="false">ไอคอน</th>
                                <th data-field="name" data-sortable="true">ชื่อระบบงาน</th>
                                <th data-field="note" data-sortable="false">รายละเอียดเพิ่มเติม</th>
                                <th data-field="status" data-sortable="true">สถานะ (Top Menu)</th>
                                <th data-field="mode">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($application as $list)
                            <tr>
                                <td class="lh35">{{ $loop->index + 1 }}</td>
                                <td class="lh35"><b>#{{ $list->id }}</b></td>
                                {{-- <td class="lh35">
                                    @if ($list->image == 'noimage.jpg')
                                    <img src="{{ url('assets/images/noimage.jpg') }}" alt="" width="45">
                                    @else
                                    <img src="{{ url('assets/images/application/'.$list->image) }}" alt="" width="45">
                                    @endif
                                </td> --}}
                                <td class="lh35"><i class="{{ $list->icon }} fs-1" style="color: {{ $list->color }}"></i>
                                </td>
                                <td class="lh35">{{ $list->name }}</td>
                                <td class="lh35">{{ $list->detail }}</td>
                                <td class="lh35">
                                    @if ($list->status=="1")
                                    <span class="badge bg-success">แสดง</span>
                                    @else
                                    <span class="badge bg-secondary">ซ่อน</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="action-icon" href="/admin/application/edit/{{ $list->id }}" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                                    {{-- <a class="action-icon" href="javascript:void(0);" onclick="deleteConfirmationApplication({{ $list->id }})" title="ลบ"><i class="mdi mdi-delete"></i></a> --}}
                                </td>
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
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
<!-- third party js ends -->
@endsection