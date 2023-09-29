@extends('layouts.master-layout', ['page_title' => "Refresh Access Token"])
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
                        <li class="breadcrumb-item active">TikTok Api</li>
                    </ol>
                </div>
                <h4 class="page-title">Refresh Access Token</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" action="/admin/token/tiktok/refresh-access-token/{{$api->id}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{method_field('PUT')}}
                            <div class="mb-2">
                                <label class="form-label">Shop Name : </label>
                                <label class="form-label text-blue">{{ $eshop->name }}</label>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Short Code : </label>
                                <label class="form-label text-blue">{{ $api->short_code }}</label>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Account Platform : </label>
                                <label class="form-label text-blue">{{ $api->account_platform }}</label>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Account : </label>
                                <label class="form-label text-blue">{{ $api->account }}</label>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Access Token Expires : </label>
                                @php
                                $day_second = 86400;
                                $current_date = date('Y-m-d');
                                $updated_at = substr($api->updated_at, 0, 10);
                                $diff_date = (strtotime($current_date) - strtotime($updated_at));
                                $balance_date = ($api->expires_in - $diff_date) / $day_second;
                                @endphp
                                @if ($balance_date > 7)
                                    <label class="form-label text-blue">{{ $balance_date }} Days</label>
                                @else
                                    <label class="form-label text-danger">{{ $balance_date }} Days</label>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="refresh_token" class="form-label">Refresh Token</label>
                                <input id="refresh_token" name="refresh_token" type="text" class="form-control" value="{{ $api->refresh_token }}"
                                    placeholder="refresh_token" autocomplete="off" readonly required />
                            </div>
                            <div class="mb-3">
                                <button type="submit" name="submit" class="btn btn-primary mt-3" title="SAVE"> Refresh Token</button>
                            </div>
                            <div class="mb-3">
                                @if (Session::has('errors'))
                                @php
                                    $error = Session::get('errors');
                                @endphp
                                @if ($error)
                                    @foreach ($error as $key => $error)
                                        <p class="text-danger">{{ $key }} : {{ $error }}</p>
                                    @endforeach
                                @endif
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection