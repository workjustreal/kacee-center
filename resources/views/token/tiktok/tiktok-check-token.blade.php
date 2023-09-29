@extends('layouts.master-layout', ['page_title' => "Check Connection Token"])
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
                <h4 class="page-title">Check Connection Token</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-12">
                                @if (isset($seller->code))
                                    @if ($seller->code == 0)
                                    <div class="mb-3">
                                        <span class="badge rounded-pill bg-success fs-4">Connected.</span><hr>
                                        @if ($seller->data)
                                            @for ($i=0; $i<count($seller->data->active_shops); $i++)
                                                <p class="text-dark">shop_id : {{ $seller->data->active_shops[$i]->shop_id }}</p>
                                                <p class="text-dark">shop_region : {{ $seller->data->active_shops[$i]->shop_region }}</p>
                                            @endfor
                                        @endif
                                    </div>
                                    @else
                                    <div class="mb-3">
                                        <span class="badge rounded-pill bg-danger fs-4">Not Connect</span><hr>
                                        @if ($seller)
                                            @foreach ($seller as $key => $value)
                                            <p class="text-danger">{{ $key }} : {{ $value }}</p>
                                            @endforeach
                                        @endif
                                    </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection