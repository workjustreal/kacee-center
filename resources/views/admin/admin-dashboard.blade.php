@extends('layouts.masterpreloader-layout', ['page_title' => "Admin Dashboard"])
@section('css')
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item active">Admin Dashboard</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Admin Dashboard</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xl-3">
                <div class="widget-rounded-circle card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                    <i class="fe-activity font-22 avatar-title text-success"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $usersActive->convert }}</span>{{ $usersActive->unit }}</h3>
                                    <p class="text-muted mb-1 text-truncate">ออนไลน์</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </div>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->
    
            <div class="col-md-6 col-xl-3">
                <div class="widget-rounded-circle card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-secondary border-secondary border">
                                    <i class="fe-user font-22 avatar-title text-secondary"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $usersNonActive->convert }}</span>{{ $usersNonActive->unit }}</h3>
                                    <p class="text-muted mb-1 text-truncate">ออฟไลน์</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </div>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->
    
            <div class="col-md-6 col-xl-3">
                <div class="widget-rounded-circle card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                                    <i class="fe-percent font-22 avatar-title text-info"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $usersActivePercentile->convert }}</span>%</h3>
                                    <p class="text-muted mb-1 text-truncate">เปอร์เซ็นต์ที่ยังออนไลน์</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </div>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->
    
            <div class="col-md-6 col-xl-3">
                <div class="widget-rounded-circle card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                                    <i class="fe-users font-22 avatar-title text-primary"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $usersTotal->convert }}</span>{{ $usersTotal->unit }}</h3>
                                    <p class="text-muted mb-1 text-truncate">ผู้ใช้งานทั้งหมด</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </div>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->
        </div>
        <!-- end row-->
        <div class="row">
            <div class="col-xl-6 col-md-6">
                <!-- Portlet card -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-0">Clients Device</h4>
                        <div class="collapse pt-3 show">
                            <div class="text-center">
                                <div class="row mt-2">
                                    <div class="col-4">
                                        <h3 data-plugin="counterup">{{ $client_device->desktop }}</h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Desktop</p>
                                    </div>
                                    <div class="col-4">
                                        <h3 data-plugin="counterup">{{ $client_device->tablet }}</h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Tablet</p>
                                    </div>
                                    <div class="col-4">
                                        <h3 data-plugin="counterup">{{ $client_device->mobile }}</h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Mobile</p>
                                    </div>
                                </div> <!-- end row -->
                                <div dir="ltr">
                                    <div id="clients-device" data-colors="#0067b8,#2ac0a3,#ebeff2" style="height: 270px;" class="morris-chart mt-3"></div>
                                </div>
                            </div>
                        </div> <!-- end collapse-->
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
            <div class="col-xl-6 col-md-6">
                <!-- Portlet card -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-0">Clients OS</h4>
                        <div class="collapse pt-3 show">
                            <div class="text-center">
                                <div class="row mt-2">
                                    <div class="col-4">
                                        <h3 data-plugin="counterup">{{ $client_os->windows }}</h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Windows</p>
                                    </div>
                                    <div class="col-4">
                                        <h3 data-plugin="counterup">{{ $client_os->android }}</h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Android</p>
                                    </div>
                                    <div class="col-4">
                                        <h3 data-plugin="counterup">{{ $client_os->ios }}</h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">iOS</p>
                                    </div>
                                </div> <!-- end row -->
                                <div dir="ltr">
                                    <div id="clients-os" data-colors="#0067b8,#2ac0a3,#ebeff2" style="height: 270px;" class="morris-chart mt-3"></div>
                                </div>
                            </div>
                        </div> <!-- end collapse-->
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->
        <div class="row">
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-0">Clients Browser</h4>
                        <div id="cardCollpase3" class="collapse pt-3 show">
                            <div class="text-center">
                                <div class="row mt-2">
                                    <div class="col-1"></div>
                                    <div class="col-2">
                                        <h3 data-plugin="counterup">{{ $client_browser->chrome }}</h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Chrome</p>
                                    </div>
                                    <div class="col-2">
                                        <h3 data-plugin="counterup">{{ $client_browser->edge }}</h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Edge</p>
                                    </div>
                                    <div class="col-2">
                                        <h3 data-plugin="counterup">{{ $client_browser->safari }}</h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Safari</p>
                                    </div>
                                    <div class="col-2">
                                        <h3 data-plugin="counterup">{{ $client_browser->firefox }}</h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Firefox</p>
                                    </div>
                                    <div class="col-2">
                                        <h3 data-plugin="counterup">{{ $client_browser->other }}</h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Other</p>
                                    </div>
                                    <div class="col-1"></div>
                                </div> <!-- end row -->
                                <div dir="ltr">
                                    <div id="browsers-chart" data-colors="#02c0ce" style="height: 270px;" class="morris-chart mt-3"></div>
                                </div>
                            </div>
                        </div> <!-- end collapse-->
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">ผู้ใช้งานที่ยังอยู่ในระบบ</h4>
                        <table id="tableUsersActive" data-toggle="table" data-pagination="true" data-ajax="ajaxRequestUsersActive" class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th data-field="emp_id" data-sortable="true">รหัสพนักงาน</th>
                                    <th data-field="name" data-sortable="true">ชื่อ - นามสกุล</th>
                                    <th data-field="dept" data-sortable="true">แผนก / หน่วยงาน</th>
                                    <th data-field="device" data-sortable="true">อุปกรณ์</th>
                                    <th data-field="os" data-sortable="true">ระบบปฏิบัติการ</th>
                                    <th data-field="browser" data-sortable="true">เบราว์เซอร์</th>
                                    <th data-field="ip_address" data-sortable="true">ไอพี</th>
                                    <th data-field="active" data-sortable="true">ใช้งานล่าสุด</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{asset('assets/libs/morris.js06/morris.js06.min.js')}}"></script>
    <script src="{{asset('assets/libs/raphael/raphael.min.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
    <script src="{{ asset('assets/js/bootstrap-table-style.js') }}"></script>
    <script type="text/javascript">
        var desktop = "{{ $client_device->desktop }}";
        var tablet = "{{ $client_device->tablet }}";
        var mobile = "{{ $client_device->mobile }}";
        var windows = "{{ $client_os->windows }}";
        var android = "{{ $client_os->android }}";
        var ios = "{{ $client_os->ios }}";
        var chrome = "{{ $client_browser->chrome }}";
        var edge = "{{ $client_browser->edge }}";
        var safari = "{{ $client_browser->safari }}";
        var firefox = "{{ $client_browser->firefox }}";
        var other = "{{ $client_browser->other }}";
        !function(a){
            "use strict";
            var t=function(){};
            t.prototype.createBarChart=function(a,t,e,o,r,i){
                Morris.Bar({element:a,data:t,xkey:e,ykeys:o,labels:r,dataLabels:!1,hideHover:"auto",resize:!0,gridLineColor:"rgba(65, 80, 95, 0.07)",barSizeRatio:.2,barColors:i})
            },
            t.prototype.createAreaChartDotted=function(a,t,e,o,r,i,s,l,n,c){
                Morris.Area({element:a,pointSize:3,lineWidth:1,data:o,xkey:r,ykeys:i,labels:s,dataLabels:!1,hideHover:"auto",pointFillColors:l,pointStrokeColors:n,resize:!0,smooth:!1,gridLineColor:"rgba(65, 80, 95, 0.07)",lineColors:c})
            },
            t.prototype.createDonutChart=function(a,t,e){Morris.Donut({element:a,data:t,barSize:.2,resize:!0,colors:e,backgroundColor:"transparent"})},
            t.prototype.init=function(){
                var t,e=["#02c0ce"];
                (t=a("#browsers-chart").data("colors"))&&(e=t.split(",")),
                this.createBarChart("browsers-chart",[{y:"Chrome",a:chrome},{y:"Edge",a:edge},{y:"Safari",a:safari},{y:"Firefox",a:firefox},{y:"Other",a:other}],"y",["a"],["Statistics"],e),
                // e=["#4a81d4","#e3eaef"],(t=a("#income-amounts").data("colors"))&&(e=t.split(",")),
                // this.createAreaChartDotted("income-amounts",0,0,[{y:"2012",a:10,b:20},{y:"2013",a:75,b:65},{y:"2014",a:50,b:40},{y:"2015",a:75,b:65},{y:"2016",a:50,b:40},{y:"2017",a:75,b:65},{y:"2018",a:90,b:60}],"y",["a","b"],["Bitcoin","Litecoin"],["#ffffff"],["#999999"],e),
                (t=a("#clients-device").data("colors"))&&(e=t.split(",")),
                this.createDonutChart("clients-device",[{label:" Desktop ",value:desktop},{label:" Tablet ",value:tablet},{label:" Mobile ",value:mobile}],e),
                (t=a("#clients-os").data("colors"))&&(e=t.split(",")),
                this.createDonutChart("clients-os",[{label:" Windows ",value:windows},{label:" Android ",value:android},{label:" iOS ",value:ios}],e)
            },
            a.Dashboard4=new t,
            a.Dashboard4.Constructor=t
        }(window.jQuery),function(a){"use strict";window.jQuery.Dashboard4.init()}();

        function ajaxRequestUsersActive(params) {
            var url = "{{ url('admin/users-active') }}";
            $.get(url + '?' + $.param(params.data)).then(function (res) {
                params.success(res)
            });
        }
    </script>
@endsection
