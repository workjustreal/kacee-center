@extends('layouts.master-layout', ['page_title' => "NocNoc Token"])
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
                        <li class="breadcrumb-item active">NocNoc</li>
                    </ol>
                </div>
                <h4 class="page-title">NocNoc Token</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-0 text-left mt-1 mb-3">
                        <a href="{{url('/admin/token/nocnoc/access-token')}}" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-plus-circle me-1"></i> Generate Access Token </a>
                    </div>
                    <table data-toggle="table" data-page-size="10" data-buttons-class="xs btn-light"
                        data-pagination="true" class="table-bordered" data-search="false">
                        <thead class="table-light">
                            <tr>
                                <th data-field="no" data-sortable="false">No.</th>
                                <th data-field="short_code" data-sortable="false">Short Code</th>
                                <th data-field="platform" data-sortable="true">Account Platform</th>
                                <th data-field="account" data-sortable="false">Account</th>
                                <th data-field="token_expires" data-sortable="true">Token Expires (24 Hr.)</th>
                                <th data-field="last_update" data-sortable="true">Last Update</th>
                                <th data-field="mode"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $day_second = 86400;
                            $current_date = date('Y-m-d');
                            @endphp
                            @foreach ($list as $list)
                            <tr>
                                <td class="lh35">{{ $loop->index + 1 }}</td>
                                <td class="lh35">{{ $list->short_code }}</td>
                                <td class="lh35">{{ $list->account_platform }}</td>
                                <td class="lh35">{{ $list->account }}</td>
                                <td class="lh35">
                                    @php
                                    $expires_in = $list->expires_in;
                                    $current_time = date('Y-m-d H:i:s');
                                    $updated_at = $list->updated_at;
                                    $diff_date = (strtotime($current_time) - strtotime($updated_at));
                                    $expire_time = ($expires_in - $diff_date);
                                    $expire_hours = number_format($expire_time / 3600, 2);
                                    @endphp
                                    @if ((int)$expire_time > 600)
                                    <span class="text-blue">{{ $expire_hours }} Hours</span>
                                    @else
                                    <span class="text-danger">{{ $expire_hours }} Hours</span>
                                    @endif
                                </td>
                                <td class="lh35">{{ $list->updated_at }}</td>
                                <td>
                                    <a href="/admin/token/nocnoc/check-token/{{ $list->id }}" class="btn btn-warning btn-sm waves-effect waves-light">
                                        <i class="far fa-check-circle"></i> Check Token</a>
                                    {{-- <a href="/admin/token/nocnoc/refresh-token/{{ $list->id }}" class="btn btn-info btn-sm waves-effect waves-light">
                                        <i class="far fa-edit"></i> Refresh Token</a> --}}
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