@extends('layouts.master-layout', ['page_title' => "ดูข้อมูลหน่วยงาน"])
@section('css')
<style>
    a:link.blink_me {
        color: #ff0000 !important;
        animation: blinker 1s linear infinite;
        text-decoration: underline;
    }
    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }
</style>
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Organization</a></li>
                        <li class="breadcrumb-item active">ดูข้อมูลหน่วยงาน</li>
                    </ol>
                </div>
                <h4 class="page-title">ดูข้อมูลหน่วยงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row" style="overflow: scroll;">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <ul class="sitemap">
                            <li><a href="javascript: void(0);" class="text-uppercase fw-bold {{ $site["color"] }} @if ($id==$site["id"]) blink_me @endif" id='{{ $site["id"] }}'><i class="mdi mdi-adjust me-1"></i>{{ $site["name"] }}</a>
                                <ul>
                                @for ($l1=0; $l1<count($site["list"]); $l1++)
                                    <li><a href="javascript: void(0);" class="text-uppercase {{ $site["list"][$l1]["color"] }} @if ($id==$site["list"][$l1]["id"]) blink_me @endif" id='{{ $site["list"][$l1]["id"] }}'><b>{{ $site["list"][$l1]["name"] }}</b> <small>({{ $site["list"][$l1]["id"] }})</small> <small>({{ $site["list"][$l1]["emp_count"] }})</small></a>
                                        @if (count($site["list"][$l1]["list"]) > 0)
                                            <ul>
                                                @for ($l2=0; $l2<count($site["list"][$l1]["list"]); $l2++)
                                                <li><a href="javascript: void(0);" class="{{ $site["list"][$l1]["color"] }} @if ($id==$site["list"][$l1]["list"][$l2]["id"]) blink_me @endif" id='{{ $site["list"][$l1]["list"][$l2]["id"] }}'>{{ $site["list"][$l1]["list"][$l2]["name"] }} <small>({{ $site["list"][$l1]["list"][$l2]["id"] }})</small> <small>({{ $site["list"][$l1]["list"][$l2]["emp_count"] }})</small></a></li>
                                                    @if (count($site["list"][$l1]["list"][$l2]["list"]) > 0)
                                                    <ul>
                                                        @for ($l3=0; $l3<count($site["list"][$l1]["list"][$l2]["list"]); $l3++)
                                                        <li><a href="javascript: void(0);" class=" @if ($id==$site["list"][$l1]["list"][$l2]["list"][$l3]["id"]) blink_me @endif" id='{{ $site["list"][$l1]["list"][$l2]["list"][$l3]["id"] }}'>{{ $site["list"][$l1]["list"][$l2]["list"][$l3]["name"] }} <small>({{ $site["list"][$l1]["list"][$l2]["list"][$l3]["id"] }})</small> <small>({{ $site["list"][$l1]["list"][$l2]["list"][$l3]["emp_count"] }})</small></a></li>
                                                            @if (count($site["list"][$l1]["list"][$l2]["list"][$l3]["list"]) > 0)
                                                            <ul>
                                                                @for ($l4=0; $l4<count($site["list"][$l1]["list"][$l2]["list"][$l3]["list"]); $l4++)
                                                                <li><a href="javascript: void(0);" class=" @if ($id==$site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["id"]) blink_me @endif" id='{{ $site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["id"] }}'><small>{{ $site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["name"] }}</small> <small>({{ $site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["id"] }})</small> <small>({{ $site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["emp_count"] }})</small></a></li>
                                                                @endfor
                                                            </ul>
                                                            @endif
                                                        @endfor
                                                    </ul>
                                                    @endif
                                                @endfor
                                            </ul>
                                        @endif
                                    </li>
                                @endfor
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    $(document).ready(function() {
        setTimeout(() => {
            const dept_id = window.location.pathname.split("/").pop();
            $('html, body').animate({
                scrollTop: ($("#"+dept_id).offset().top - 300)
            }, 100);
        }, 200);
    });
</script>
@endsection
