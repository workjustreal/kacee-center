@extends('layouts.master-layout', ['page_title' => "จัดการขนส่ง"])
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                        <li class="breadcrumb-item active">ขนส่งสินค้า</li>
                    </ol>
                </div>
                <h4 class="page-title">จัดการขนส่ง</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-0 text-left mt-1 mb-3">
                        <a href="{{url('/checkout/ship-com-add')}}" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-plus-circle me-1"></i> เพิ่มขนส่ง </a>
                    </div>
                    <table data-toggle="table" data-page-size="10" data-buttons-class="xs btn-light" data-pagination="true"
                        class="table-bordered" data-search="false">
                        <thead class="table-light">
                            <tr>
                                <th data-field="no" data-sortable="false">ลำดับ</th>
                                <th data-field="name" data-sortable="true">ชื่อขนส่ง</th>
                                <th data-field="check" data-sortable="true">ตรวจสอบจาก</th>
                                <th data-field="status" data-sortable="true">สถานะ</th>
                                <th data-field="mode">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ship_com as $list)
                            <tr>
                                <td class="lh35">{{ $loop->index + 1 }}</td>
                                <td class="lh35">{{ $list->name }}</td>
                                <td class="lh35">{{ $list->check }}</td>
                                <td class="lh35">
                                    @if ($list->status=="1")
                                    <span class="badge bg-success">ใช้งาน</span>
                                    @else
                                    <span class="badge bg-secondary">ไม่ใช้งาน</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="action-icon" href="/checkout/ship-com-edit/{{ $list->id }}" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
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