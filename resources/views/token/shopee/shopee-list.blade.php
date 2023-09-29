@extends('layouts.master-layout', ['page_title' => "Shopee Token"])
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
                        <li class="breadcrumb-item active">Shopee</li>
                    </ol>
                </div>
                <h4 class="page-title">Shopee Token</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-0 text-left mt-1 mb-3">
                        <a href="{{url('/admin/token/shopee/access-token')}}" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-plus-circle me-1"></i> Generate Access Token </a>
                            <button type="button" class="btn btn-secondary float-end" data-bs-toggle="modal" data-bs-target="#right-modal">
                                <i class="mdi mdi-more me-1"></i> Details</button>
                    </div>
                    <table data-toggle="table" data-page-size="10" data-buttons-class="xs btn-light"
                        data-pagination="true" class="table-bordered" data-search="false">
                        <thead class="table-light">
                            <tr>
                                <th data-field="no" data-sortable="false">No.</th>
                                <th data-field="short_code" data-sortable="false">Short Code</th>
                                <th data-field="platform" data-sortable="true">Account Platform</th>
                                <th data-field="account" data-sortable="false">Account</th>
                                <th data-field="token_expires" data-sortable="true">Token Expires (4 Hr.)</th>
                                <th data-field="refresh_expires" data-sortable="true">Refresh Token Expires (30 Days.)</th>
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
                                <td class="lh35">
                                    @php
                                    $updated_at = substr($list->updated_at, 0, 10);
                                    $diff_date = (strtotime($current_date) - strtotime($updated_at));
                                    $balance_date = (2592000 - $diff_date) / $day_second;
                                    @endphp
                                    @if ($balance_date > 7)
                                    <span class="text-blue">{{ $balance_date }} Days</span>
                                    @else
                                    <span class="text-danger">{{ $balance_date }} Days</span>
                                    @endif
                                </td>
                                <td class="lh35">{{ $list->updated_at }}</td>
                                <td>
                                    <a href="/admin/token/shopee/check-token/{{ $list->id }}" class="btn btn-warning btn-sm waves-effect waves-light">
                                        <i class="far fa-check-circle"></i> Check Token</a>
                                    <a href="/admin/token/shopee/refresh-token/{{ $list->id }}" class="btn btn-info btn-sm waves-effect waves-light">
                                        <i class="far fa-edit"></i> Refresh Token</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Right modal content -->
                    <div id="right-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-sm modal-right">
                            <div class="modal-content">
                                <div class="modal-header border-0">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center">
                                        <h4 class="mt-0">เกี่ยวกับ Shopee Token</h4>
                                        <p>API Call Limit: -</p>
                                        <p>ระยะเวลา Access Token: 4 ชั่วโมง</p>
                                        <p>ระยะเวลา Refresh Token: 30 วัน</p>
                                        <p><a href="https://open.shopee.com/developer-guide/20" target="_blank">ดูขั้นตอน</a></p>
                                    </div>
                                    <div class="mt-3">
                                        <h5>Access Token/Refresh Token</h5>
                                        <p>- <code class="text-primary">access_token</code> จะมีอายุแค่ 4 ชั่วโมง ต้องใช้ <code class="text-primary">refresh_token</code> เพื่อรับ <code class="text-primary">access_token</code> ใหม่</p>
                                        <p>- <code class="text-primary">refresh_token</code> มีอายุการใช้งาน 30 วัน, การรีเฟรชโทเค็น <code class="text-primary">refresh_token</code> จะถูกรีเซ็ตกลับเป็น 30 วัน</p>
                                    </div>
                                    <div class="mt-3">
                                        <h5>บันทึกการใช้งาน</h5>
                                        <small>- <code class="text-primary">refresh_token</code> มีอายุการใช้งาน 30 วัน</small><br>
                                        <small>- ถ้า <code class="text-primary">refresh_token</code> หมดอายุ จะไม่สามารถรีเฟรชได้ ต้อง Authorization and Authentication ใหม่ เพื่อขอ <code class="text-primary">access_token</code> และ <code class="text-primary">refresh_token</code></small><br>
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
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