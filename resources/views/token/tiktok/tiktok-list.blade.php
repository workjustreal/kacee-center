@extends('layouts.master-layout', ['page_title' => "TikTok Token"])
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
                        <li class="breadcrumb-item active">TikTok</li>
                    </ol>
                </div>
                <h4 class="page-title">TikTok Token</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-0 text-left mt-1 mb-3">
                        <a href="{{url('/admin/token/tiktok/access-token')}}" class="btn btn-primary waves-effect waves-light">
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
                                <th data-field="access_token_expires" data-sortable="true">Access Token Expires (7 Days.)</th>
                                <th data-field="refresh_token_expires" data-sortable="true">Refresh Token Expires (365 Days.)</th>
                                <th data-field="last_update" data-sortable="true">Last Update</th>
                                <th data-field="mode"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
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
                                    $dateS = date_create($current_date);
                                    $dateE = date_create(date("Y-m-d H:i:s", $list->expires_in));
                                    $diff = date_diff($dateS, $dateE);
                                    $balance_date = (int)$diff->format("%R%a");
                                    @endphp
                                    @if ($balance_date > 2)
                                    <span class="text-blue">{{ $balance_date }} Days</span>
                                    @else
                                    <span class="text-danger">{{ $balance_date }} Days</span>
                                    @endif
                                </td>
                                <td class="lh35">
                                    @php
                                    $dateS = date_create($current_date);
                                    $dateE = date_create(date("Y-m-d H:i:s", $list->refresh_expires_in));
                                    $diff = date_diff($dateS, $dateE);
                                    $balance_date = (int)$diff->format("%R%a");
                                    @endphp
                                    @if ($balance_date > 7)
                                    <span class="text-blue">{{ $balance_date }} Days</span>
                                    @else
                                    <span class="text-danger">{{ $balance_date }} Days</span>
                                    @endif
                                </td>
                                <td class="lh35">{{ $list->updated_at }}</td>
                                <td>
                                    <a href="/admin/token/tiktok/check-token/{{ $list->id }}" class="btn btn-warning btn-sm waves-effect waves-light">
                                        <i class="far fa-check-circle"></i> Check Token</a>
                                    <a href="/admin/token/tiktok/refresh-token/{{ $list->id }}" class="btn btn-info btn-sm waves-effect waves-light">
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
                                        <h4 class="mt-0">เกี่ยวกับ TikTok Token</h4>
                                        <p>API Call Limit: -</p>
                                        <p>ระยะเวลา Access Token: 7 วัน</p>
                                        <p>ระยะเวลา Refresh Token: 365 วัน</p>
                                        <p><a href="https://developers.tiktok-shops.com/documents/document/234120" target="_blank">ดูขั้นตอน</a></p>
                                    </div>
                                    <div class="mt-3">
                                        <h5>Access Token/Refresh Token</h5>
                                        <p>1. 24 ชั่วโมงก่อน <code class="text-primary">access_token</code> จะหมดอายุ ใช้ api <code>/api/v2/token/refresh</code> เพื่อรับ <code class="text-primary">access_token</code> ใหม่ <code class="text-primary">access_token</code> เก่าจะยังใช้งานได้จนถึงวันหมดอายุ หากรีเฟรชเร็วเกินไป <code class="text-primary">access_token</code> จะไม่เปลี่ยน</p>
                                        <p>2. หลังจาก <code class="text-primary">access_token</code> หมดอายุ และ ก่อน <code class="text-primary">refresh_token</code> หมดอายุ ใช้ api <code>/api/v2/token/refresh</code> เพื่อรับ <code class="text-primary">access_token</code> ใหม่</p>
                                        <p>3. ถ้า <code class="text-primary">refresh_token</code> หมดอายุแล้ว หากใช้ api <code>/api/v2/token/refresh</code> จะได้ error code ส่งกลับมา</p>
                                        <p>4. เวลาของ <code class="text-primary">refresh_token</code> จะไม่ถูกขยายหลังจากรีเฟรช แต่จะมีการสร้าง <code class="text-primary">access_token</code> ที่มีอายุการใช้งาน 7 วันทุกครั้งที่รีเฟรช <code class="text-primary">refresh_token</code> จะถูกแทนที่ด้วยอันใหม่ในทุกๆ 6 วัน โดยจะมีเวลาหมดอายุสุดท้ายเหมือนกับของเดิม</p>
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