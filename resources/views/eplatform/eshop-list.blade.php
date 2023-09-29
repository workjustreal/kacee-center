@extends('layouts.master-layout', ['page_title' => "E-Shop List"])
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
                        <li class="breadcrumb-item active">E-Shop</li>
                    </ol>
                </div>
                <h4 class="page-title">E-Shop List</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-0 text-left mt-1 mb-3">
                        <a href="{{url('/admin/eplatform/eshop-add')}}" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-plus-circle me-1"></i> Create New E-Shop </a>
                    </div>
                    <table data-toggle="table" data-page-size="10" data-buttons-class="xs btn-light"
                        data-pagination="true" class="table-bordered" data-search="false">
                        <thead class="table-light">
                            <tr>
                                <th data-field="id" data-sortable="false">Id</th>
                                <th data-field="name" data-sortable="true">Name</th>
                                <th data-field="seller_id" data-sortable="false">Seller Id</th>
                                <th data-field="platform_id" data-sortable="true">Platform Id</th>
                                <th data-field="platform_name" data-sortable="true">Platform Name</th>
                                <th data-field="api" data-sortable="true">API Version</th>
                                <th data-field="status" data-sortable="true">Status</th>
                                <th data-field="mode"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($list as $list)
                            <tr>
                                <td class="lh35">{{ $list->id }}</td>
                                <td class="lh35">{{ $list->name }}</td>
                                <td class="lh35">{{ $list->seller_id }}</td>
                                <td class="lh35">{{ $list->platform_id }}</td>
                                <td class="lh35">{{ $list->platform_name }}</td>
                                <td class="lh35">V{{ $list->api_version }}</td>
                                <td class="lh35">
                                    @if ($list->status==1)
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-secondary">Not Active</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="action-icon" href="/admin/eplatform/eshop-edit/{{ $list->id }}" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
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