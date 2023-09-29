@extends('layouts.masterpreloader-layout', ['page_title' => 'Import Stock'])
@section('css')
    <link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .loading-spinner {
            width: 30px;
            height: 30px;
            border: 2px solid indigo;
            border-radius: 50%;
            border-top-color: #0001;
            display: inline-block;
            animation: loadingspinner .7s linear infinite;
        }

        @keyframes loadingspinner {
            0% {
                transform: rotate(0deg)
            }

            100% {
                transform: rotate(360deg)
            }
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                            <li class="breadcrumb-item active">Import Stock</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Import Stock</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}
                                    @endforeach
                                </div>
                            @endif
                            @if ($message = Session::get('success'))
                                <div class="alert alert-success" role="alert">
                                    อัพโหลดไฟล์เรียบร้อย
                                </div>
                            @endif
                            <form class="form-horizontal" method="POST" enctype="multipart/form-data"
                                action="{{ route('od.stock.uplaod') }}">
                                {{ csrf_field() }}
                                <div class="mt-3">
                                    <input type="file" data-plugins="dropify" data-height="300" name="file" />
                                    <p class="text-muted text-center mt-2 mb-0">คลิก หรือ วางไฟล์เพื่ออัพโหลด</p>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary mt-2" id="upload">
                                        อัพโหลดไฟล์
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="loading-spinner mb-2"></div>
                        <div>Loading...</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="modal" id="modal-loading" data-backdrop="static">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="loading-spinner mb-2"></div>
                        <div>Loading</div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropify/dropify.min.js') }}"></script>

    <script src="{{ asset('assets/js/pages/form-fileuploads.init.js') }}"></script>

    <script>
        $("#upload").click(function() {
            $('#staticBackdrop').modal('show');
        });
    </script>
@endsection
