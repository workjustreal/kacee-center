@extends('layouts.master-layout', ['page_title' => "Generate Access Token"])
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
                        <li class="breadcrumb-item active">Lazada Api</li>
                    </ol>
                </div>
                <h4 class="page-title">Generate Access Token</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" action="{{ route('token.lazada.generate_access_token') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="mb-3">
                                <a class="btn btn-warning" href="https://auth.lazada.com/oauth/authorize?response_type=code&force_auth=true&redirect_uri=https://shop.kaceebest.com/&client_id=104968" target="_blank">Sign in And Authorize Permission</a>
                            </div>
                            <div class="mb-3">
                                <label for="code" class="form-label">Code</label>
                                <input id="code" name="code" type="text" class="form-control" value="{{ old('code') }}"
                                    placeholder="Code" autocomplete="off" required />
                            </div>
                            <div class="mb-3">
                                <button type="submit" name="submit" class="btn btn-primary mt-3" title="SAVE"> Generate</button>
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