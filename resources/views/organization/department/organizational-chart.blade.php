@extends('layouts.master-layout', ['page_title' => "แผนผังองค์กร"])
@section('css')
<!-- third party css -->
<link href="{{ asset('assets/css/organizational-chart.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Organization</a></li>
                        <li class="breadcrumb-item active">แผนผังองค์กร</li>
                    </ol>
                </div>
                <h4 class="page-title">แผนผังองค์กร</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body body-chart">
                    <button type="button" class="btn btn-soft-secondary waves-effect waves-light float-end" id="btnExport" onclick="exportData();">Export</button>
                    <div id="exportForm" class="d-none"></div>
                    <div class="container-chart">
                        <h3 class="level-0 rectangle text-white py-3">{{ $site["name"] }}</h3>
                        <ol class="level-1-wrapper">
                            @for ($l1=0; $l1<count($site["list"]); $l1++)
                            <li>
                                <a href="javascript: void(0);"><h4 class="level-1 rectangle @if (auth()->user()->dept_id==$site["list"][$l1]["id"]) new-items text-white @endif">{{ $site["list"][$l1]["name"] }}<br><small>({{ $site["list"][$l1]["id"] }})</small> <small>({{ $site["list"][$l1]["emp_count"] }})</small></h4></a>
                                @if (count($site["list"][$l1]["list"]) > 0)
                                <ol class="level-2-wrapper">
                                    @for ($l2=0; $l2<count($site["list"][$l1]["list"]); $l2++)
                                    <li>
                                        <a href="javascript: void(0);"><h5 class="level-2 rectangle @if (auth()->user()->dept_id==$site["list"][$l1]["list"][$l2]["id"]) new-items text-white @endif">{{ $site["list"][$l1]["list"][$l2]["name"] }}<br><small>({{ $site["list"][$l1]["list"][$l2]["id"] }})</small> <small>({{ $site["list"][$l1]["list"][$l2]["emp_count"] }})</small></h5></a>
                                        @if (count($site["list"][$l1]["list"][$l2]["list"]) > 0)
                                        <ol class="level-3-wrapper">
                                            @for ($l3=0; $l3<count($site["list"][$l1]["list"][$l2]["list"]); $l3++)
                                            <li>
                                                <a href="javascript: void(0);"><h6 class="level-3 rectangle @if (auth()->user()->dept_id==$site["list"][$l1]["list"][$l2]["list"][$l3]["id"]) new-items text-white @endif">{{ $site["list"][$l1]["list"][$l2]["list"][$l3]["name"] }}<br><small>({{ $site["list"][$l1]["list"][$l2]["list"][$l3]["id"] }})</small> <small>({{ $site["list"][$l1]["list"][$l2]["list"][$l3]["emp_count"] }})</small></h6></a>
                                                @if (count($site["list"][$l1]["list"][$l2]["list"][$l3]["list"]) > 0)
                                                    <ol class="level-4-wrapper">
                                                    @for ($l4=0; $l4<count($site["list"][$l1]["list"][$l2]["list"][$l3]["list"]); $l4++)
                                                    <li>
                                                        <a href="javascript: void(0);"><h6 class="level-4 rectangle @if (auth()->user()->dept_id==$site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["id"]) new-items text-white @endif">{{ $site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["name"] }}<br><small>({{ $site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["id"] }})</small> <small>({{ $site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["emp_count"] }})</small></h6></a>
                                                    </li>
                                                    @endfor
                                                    </ol>
                                                @endif
                                            </li>
                                            @endfor
                                        </ol>
                                        @endif
                                    </li>
                                    @endfor
                                </ol>
                                @endif
                            </li>
                            @endfor
                        </ol>
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
    function exportData() {
        var url = "{{ url('organization/organizational-chart/export') }}";
        $("#exportForm").append('<form action="' + url + '" method="GET">');
        $("#exportForm form").append(
            '<input type="text" name="download-dept" value="' + $("#dept").val() + '"/>'
        );
        $("#exportForm form").append(
            '<input type="text" name="download-month" value="' + $("#month").val() + '"/>'
        );
        $("#exportForm form").submit();
        $("#exportForm").html("");
    }
</script>
@endsection