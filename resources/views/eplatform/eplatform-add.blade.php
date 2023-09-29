@extends('layouts.master-layout', ['page_title' => "Create New E-Platform"])
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
                        <li class="breadcrumb-item active">E-Platform</li>
                    </ol>
                </div>
                <h4 class="page-title">Create New E-Platform</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" action="{{ route('eplatform.create') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}"
                                    placeholder="Name" autocomplete="off" required />
                            </div>
                            <div class="mb-3">
                                <label for="app_key" class="form-label">App Key</label>
                                <input id="app_key" name="app_key" type="text" class="form-control" value="{{ old('app_key') }}"
                                    placeholder="App Key" autocomplete="off" required />
                            </div>
                            <div class="mb-3">
                                <label for="app_secret" class="form-label">App Secret</label>
                                <input id="app_secret" name="app_secret" type="text" class="form-control" value="{{ old('app_secret') }}"
                                    placeholder="App Secret" autocomplete="off" required />
                            </div>
                            <div class="mb-3">
                                <label for="api_url" class="form-label">API URL</label>
                                <input id="api_url" name="api_url" type="text" class="form-control" value="{{ old('api_url') }}"
                                    placeholder="API URL" autocomplete="off" required />
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label><br>
                                <div class="radio radio-success form-check-inline ml-2">
                                    <input type="radio" id="inlineRadio1" value="1" name="status" title="ACTIVE" checked {{ (old('status')==1) ? 'checked' : '' }}>
                                    <label for="inlineRadio1">Active </label>
                                </div>
                                <div class="radio form-check-inline">
                                    <input type="radio" id="inlineRadio2" value="0" name="status" title="NOT ACTIVE" {{ (old('status')==0) ? 'checked' : '' }}>
                                    <label for="inlineRadio2">Not Active </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button type="submit" name="submit" class="btn btn-primary mt-3" title="SAVE"> Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection