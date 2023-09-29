@extends('layouts.masterpreloader-layout', ['page_title' => "Home"])
@section('css')
<link href="{{ asset('assets/css/calendar/fullcalendar.min.css') }} " rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/calendar/toastr.min.css') }} " rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/datatables/datatables.min.css') }} " rel="stylesheet" type="text/css" />
<style>
    .dataTables_wrapper .myfilter .dataTables_filter{float:left}
    .dataTables_wrapper .mylength .dataTables_length{float:right}
    .dataTables_wrapper .dataTables_length {margin-bottom: 4px;}
    .dataTables_wrapper .dataTables_filter {margin-bottom: 4px;}
    #modal-description img {
        max-width: 100%;
        height: auto;
    }
    .fc-list-item-title a {
        color: #333b46 !important;
    }
    .font-info * {
        color: #c08127 !important;
    }
    .font-info a {
        background: transparent !important;
        color: #0764ce !important;
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
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Dashboard</h4>
                </div>
            </div>
        </div>
        @php
            $info1 = $info;
            $info2 = $info;
        @endphp
        @if ($info1->isNotEmpty())
        <div class="row">
            <div class="col-12 mb-1">
                <div class="alert alert-warning border border-warning pb-0" role="alert">
                    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                        <ol class="carousel-indicators m-0">
                            @foreach ($info1 as $list)
                            <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="{{ $loop->index }}" class="@if($loop->index==0) active @endif"></li>
                            @endforeach
                        </ol>
                        <div class="carousel-inner pb-3" role="listbox">
                            @foreach ($info2 as $list)
                            <div class="carousel-item @if($loop->index==0) active @endif" data-bs-interval="7000">
                                <a href="javascript:void(0)" onclick="$('.info-content').toggleClass('text-nowrap')" class="font-info float-end text-decoration-underline d-block d-sm-none"><small>See more</small><i class="mdi mdi-chevron-down"></i></a>
                                <a href="{{url('/events/show', $list->id)}}" class="">
                                    <div class="d-block font-info" role="alert">
                                        <i class="mdi mdi-alert-circle-outline me-2"></i> <strong>{{ $list->title }}</strong><br><span class="info-content text-nowrap">@php echo strip_tags($list->description, ["a"]); @endphp</span>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-md-6 col-xl-3 hvr-bob">
                <div class="widget-rounded-circle card bg-emp">
                    <a href="{{url('/admin/application')}}" class="">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="avatar-lg rounded-circle bg-soft-primary border-white border">
                                        <i class="fe-layers font-22 avatar-title text-white"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="text-end">
                                        <h3 class="text-white mt-3">จัดการระบบงาน</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 hvr-bob">
                <div class="widget-rounded-circle card bg-product">
                    <a href="{{url('/admin/user-manage')}}" class="">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="avatar-lg rounded-circle bg-soft-success border-white border">
                                        <i class="fe-user-plus font-22 avatar-title text-white"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="text-end">
                                        <h3 class="text-white mt-3">จัดการผู้ใช้งาน</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 hvr-bob">
                <div class="widget-rounded-circle card bg-stock">
                    <a href="{{url('/organization/employees')}}" class="">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="avatar-lg rounded-circle bg-soft-warning border-white border">
                                        <i class="fe-users font-22 avatar-title text-white"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="text-end">
                                        <h3 class="text-white mt-3">จัดการพนักงาน</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 hvr-bob">
                <div class="widget-rounded-circle card bg-Leave">
                    <a href="{{url('/admin/roles')}}" class="">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="avatar-lg rounded-circle bg-soft-danger border-white border">
                                        <i class="fe-user-check font-22 avatar-title text-white"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="text-end">
                                        <h3 class="text-white mt-3">บทบาทและสิทธิ์</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card ribbon-box">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-2">
                                <div class="ribbon ribbon-primary float-start">ค้นหาเบอร์ภายใน</div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="tel-page-datatable" class="table nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="w100">รายละเอียด</th>
                                        <th>ชื่อ</th>
                                        <th>เบอร์โทร</th>
                                        <th>มือถือ</th>
                                        <th>อีเมล์</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($emp as $emps)
                                        @php
                                            $detail = App\Http\Controllers\DashboardController::callDepartment($emps, $depts);
                                        @endphp
                                        <tr>
                                            <td>{!! $detail !!}</td>
                                            <td>{{ $emps->name }} {{ $emps->surname }} @if ($emps->nickname != "") <span class="text-blue"><i>({{ $emps->nickname }})</i></span> @endif</td>
                                            <td>
                                                @if ($emps->tel != "" && $emps->tel2 != "")
                                                    {{ $emps->tel . " , " . $emps->tel2 }}
                                                @else
                                                    @if ($emps->tel != "")
                                                        {{ $emps->tel }}
                                                    @else
                                                        {{ $emps->tel2 }}
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if ($emps->phone != "" && $emps->phone2 != "")
                                                    {{ $emps->phone . " , " . $emps->phone2 }}
                                                @else
                                                    @if ($emps->phone != "")
                                                        {{ $emps->phone }}
                                                    @else
                                                        {{ $emps->phone2 }}
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $emps->email }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card ribbon-box">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-12 col-12 mt-3 mb-3">
                                <h5>สีประเภทของประกาศ</h5>
                                <ul class="list-group">
                                    <li class="list-group-item text-white border border-white bg-holiday">วันหยุด</li>
                                    <li class="list-group-item text-white border border-white bg-hr">ฝ่ายบุคคล</li>
                                    <li class="list-group-item text-white border border-white bg-sales">ฝ่ายขาย</li>
                                    <li class="list-group-item text-white border border-white bg-secretary">เลขา</li>
                                    <li class="list-group-item text-white border border-white bg-it">ไอที</li>
                                    <li class="list-group-item text-white border border-white bg-admin">แอดมิน</li>
                                </ul>
                            </div>
                            <div class="col-lg-9 col-md-12 col-12">
                                <div id="calendar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body ribbon-box">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="ribbon ribbon-primary float-start">ประกาศวันหยุดประจำปี</div>
                                {{-- <a href="{{ url('holidays/print') }}" type="button" target="_blank" class="btn btn-soft-primary waves-effect waves-light float-end">พิมพ์</a> --}}
                                <div class="input-group input-group-sm float-end" style="width: 150px;">
                                    <select class="form-select" id="holiday_year" name="holiday_year">
                                        <option value="{{ date('Y')-1 }}">{{ (date('Y')+543)-1 }}</option>
                                        <option value="{{ date('Y') }}" selected>{{ (date('Y')+543) }}</option>
                                        <option value="{{ date('Y')+1 }}">{{ (date('Y')+543)+1 }}</option>
                                    </select>
                                    <span class="input-group-text bg-secondary border-secondary text-white">
                                        <i class="mdi mdi-calendar-range"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <table id="holiday-page-datatable" class="table table-striped nowrap w-100">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">หัวข้อ</th>
                                    <th scope="col">เริ่มต้น</th>
                                    <th scope="col">สิ้นสุด</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body ribbon-box">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="ribbon ribbon-primary float-start">ประกาศบริษัท</div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="event-page-datatable" class="table table-striped nowrap w-100">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">ประกาศ</th>
                                        <th scope="col">วัน/เดือน/ปี ประกาศ</th>
                                        <th scope="col">หัวข้อ</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="fullWidthModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-w-100">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h4 class="modal-title" id="modal-title"><b></b></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex align-items-start mb-3 mt-1">
                                    <img class="d-flex me-2 rounded-circle" src="{{asset('assets/images/users/user-1.jpg')}}" alt="placeholder image" width="32" height="32">
                                    <div class="w-100">
                                        <h6 class="m-0 font-14" id="modal-author"></h6>
                                        <small class="text-muted" id="modal-dept"></small>
                                        <small class="float-end" id="modal-update"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div id="modal-description"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/calendar/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/locale/th.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/custom.js') }}"></script>
    <script src="{{ asset('assets/js/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#tel-page-datatable').DataTable({
                // dom:"<'myfilter'f><'mylength'l>t",
                // "pagingType": "full_numbers",
                "order": [[1, 'asc']],
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'All'],
                ],
                "drawCallback": function () {
                    $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                },
            });
            $("#holiday_year").change(function (){
                get_holidays();
            });
            get_holidays();
            get_events();
        });
        function get_holidays() {
            $('#holiday-page-datatable').DataTable({
                "destroy": true,
                "searching": false,
                "lengthChange": false,
                "paging": false,
                "scrollX": true,
                "order": [[0, 'asc']],
                "info": false,
                "ajax": {
                    "url": '{{ url("get-holidays") }}',
                    "data": {
                        "holiday_year": $("#holiday_year").val()
                    }
                },
                columns: [
                    { data: 'sort', "visible": false },
                    { data: 'title' },
                    { data: 'start' },
                    { data: 'end' },
                ],
            });
        }
        function get_events() {
            $('#event-page-datatable').DataTable({
                "destroy": true,
                "searching": false,
                "lengthChange": false,
                "scrollX": true,
                "order": [[0, 'desc']],
                "drawCallback": function () {
                    $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                },
                "ajax": {
                    "url": '{{ url("get-events") }}'
                },
                columns: [
                    { data: 'sort', "visible": false },
                    { data: 'role' },
                    { data: 'date' },
                    { data: 'title' },
                ],
            });
        }
    </script>
@endsection
