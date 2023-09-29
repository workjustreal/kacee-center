@extends('layouts.master-layout', ['page_title' => 'Chatbot'])
@section('css')
<link href="{{ asset('assets/css/chatbot.css') }}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                            <li class="breadcrumb-item active">Chatbot</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Chatbot</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body py-2 px-3 border-bottom border-light">
                        <div class="row justify-content-between py-1">
                            <div class="col-sm-7">
                                <div class="d-flex align-items-start">
                                    <img src="{{asset('assets/images/users/chatbot.png')}}" class="me-2 rounded-circle" height="36" alt="Brandon Smith">
                                    <div>
                                        <h5 class="mt-0 mb-0 font-15">
                                            <a href="#" class="text-reset">KaceeBot <span class="badge badge-outline-success fw-normal">Beta</span></a>
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted font-12">
                                            <small class="mdi mdi-circle text-success"></small> Online
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div id="tooltips-container">
                                    <p class="font-12 fw-normal text-primary"><i class="mdi mdi-information-outline"></i> แชทบอทตอบคำถามเกี่ยวกับ KACEE</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="conversation-list" id="chat-messages">
                        </ul>
                        <div class="row">
                            <div class="col">
                                <div class="mt-2 bg-light p-3 rounded">
                                    <div class="d-flex justify-content-between">
                                        <input type="hidden" id="emp_id" class="form-control" value="{{ auth()->user()->emp_id }}" required="" />
                                        <input type="text" id="query-input" class="form-control border-0 me-3" placeholder="พิมพ์ข้อความ" required="" />
                                        <button type="button" id="send-button" class="btn btn-success chat-send"><i class="fe-send"></i></button>
                                    </div>
                                </div>
                            </div>
                            <!-- end col-->
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end card-body -->
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>
    <script src="{{ asset('assets/js/pages/chatbot.js') }}"></script>
    <!-- third party js ends -->
@endsection
