@extends('layouts.master-layout', ['page_title' => "Create New E-Shop"])
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
                <h4 class="page-title">Create New E-Shop</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" action="{{ route('eshop.create') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}"
                                    placeholder="Name" autocomplete="off" required />
                            </div>
                            <div class="mb-3">
                                <label for="seller_id" class="form-label">Seller Id</label>
                                <input id="seller_id" name="seller_id" type="text" class="form-control" value="{{ old('seller_id') }}"
                                    placeholder="Seller Id" autocomplete="off" required />
                            </div>
                            <div class="mb-3">
                                <label for="platform" class="form-label">Platform</label>
                                <select id="select-code-location" class="form-select" id="platform" name="platform" required>
                                    <option value="" selected>Select Platform</option>
                                    @foreach($eplatform as $list)
                                        <option value="{{$list->id}}">{{$list->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="api_version" class="form-label">API Version</label>
                                <select id="select-code-location" class="form-select" id="api_version" name="api_version" required>
                                    <option value="" selected>Select API Version</option>
                                    <option value="1">V1</option>
                                    <option value="2">V2</option>
                                </select>
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