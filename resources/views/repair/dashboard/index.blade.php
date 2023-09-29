@extends('layouts.master-nopreloader-layout', ['page_title' => 'Dashboard'])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/monthSelect/style.css') }}" rel="stylesheet" />
    <!-- third party css end -->
    <style>
        #container-charts {
            height: 400px;
        }

        .highcharts-figure,
        .highcharts-data-table table {
            min-width: 310px;
            max-width: 800px;
            margin: 1em auto;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #ebebeb;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }

        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }

        .highcharts-data-table td,
        .highcharts-data-table th,
        .highcharts-data-table caption {
            padding: 0.5em;
        }

        .highcharts-data-table thead tr,
        .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }

        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right d-block">
                        <form class="d-flex align-items-center mb-1">
                            <div class="col-lg-auto col-md-auto col-sm-12 mb-2">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control month-datepicker" id="dash_year"
                                        name="dash_year" placeholder="เลือกเดือน-ปี" />
                                    <span class="input-group-text bg-secondary border-secondary text-white">
                                        <i class="mdi mdi-calendar-range"></i>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <h4 class="page-title">Dashboard แจ้งซ่อม </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        {{-- <div class="row">
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-0">Clients Browser</h4>
                        <div id="cardCollpase3" class="collapse pt-3 show">
                            <div class="text-center">
                                <div class="row mt-2">
                                    <div class="col-1"></div>
                                    <div class="col-2">
                                        <h3 data-plugin="counterup"></h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Chrome</p>
                                    </div>
                                    <div class="col-2">
                                        <h3 data-plugin="counterup"></h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Edge</p>
                                    </div>
                                    <div class="col-2">
                                        <h3 data-plugin="counterup"></h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Safari</p>
                                    </div>
                                    <div class="col-2">
                                        <h3 data-plugin="counterup"></h3>
                                        <p class="text-muted font-13 mb-0 text-truncate">Firefox</p>
                                    </div>
                                    <div class="col-2">
                                        <h3 data-plugin="counterup"></h3>
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
        </div> --}}

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div id="container-charts"></div>
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div>

        <div class="row">
            <div class="col--12">
                <div id="container-card-detail"></div>
            </div> <!-- end col -->
        </div>
    </div>
@endsection
@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/highcharts/highcharts.js') }}"></script>
    <script src="{{ asset('assets/libs/highcharts/exporting.js') }}"></script>
    <script src="{{ asset('assets/libs/highcharts/export-data.js') }}"></script>
    <script src="{{ asset('assets/libs/highcharts/accessibility.js') }}"></script>

    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/4.6.13/dist/plugins/monthSelect/index.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/moment-with-locales.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            charts();
            dashboardDetail();
            moment.locale("th-TH");
            flatpickr.localize(flatpickr.l10ns.th);
            $(".month-datepicker").flatpickr({
                disableMobile: "true",
                dateFormat: "m/Y",
                defaultDate: moment().format('MM/YYYY'),
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: "m/Y",
                        altFormat: "F Y",
                        theme: "light"
                    })
                ],
                onReady: function(dateObj, dateStr, instance) {
                    const $clear = $(
                            '<div class="flatpickr-clear"><button class="btn btn-sm btn-link">Clear</button></div>'
                        )
                        .on("click", () => {
                            instance.clear();
                            instance.close();
                        })
                        .appendTo($(instance.calendarContainer));
                },
                onClose: function(selectedDates, dateStr, instance) {
                    $(instance.input).blur();
                }
            });

            $("#dash_year").change(function() {
                var url = "{{ route('repair.dashboard.year') }}";
                $.get(url, {
                    dash_year: $(this).val()
                }).then(function(res) {
                    charts(res._chart);
                    dashboardDetail(res._month_year);
                });
            });
        });

        function dashboardDetail(datas) {
            var _detail = (datas) ? datas : {!! json_encode($_detail) !!};
            let val = '';
            let status = '';
            let icon = '';
            let bg = '';
            let text = '';
            let row = '<div class="row">';
            let rows = '</div>';

            for (const key in _detail.data) {
                if (_detail.data.hasOwnProperty.call(_detail.data, key)) {
                    const element = _detail.data[key];
                    switch (key) {
                        case 'A03050100':
                            bg = 'bg-info';
                            text = 'แผนกไฟฟ้าและสุขาภิบาล';
                            break;
                        case 'A03050200':
                            bg = 'bg-danger';
                            text = 'แผนกยานยนต์';
                            break;
                        case 'A03060100':
                            bg = 'bg-success';
                            text = 'แผนกซ่อมบำรุง';
                            break;
                        case 'A01100100':
                            bg = 'bg-warning';
                            text = 'แผนกไอที';
                            break;
                    }

                    for (const keys in element) {
                        if (Object.hasOwnProperty.call(element, keys)) {
                            const detail = element[keys];
                            switch (keys) {
                                case 'wait':
                                    icon = '<div class="avatar-lg rounded-circle bg-soft-info border-info border">\
                                                            <i class="fe-clock font-24 avatar-title text-info"></i></div>';
                                    status = 'รอรับงานทั้งหมด';
                                    break;
                                case 'process':
                                    icon =
                                        '<div class="avatar-lg rounded-circle bg-soft-warning border-warning border">\
                                                            <i class="fe-loader font-24 avatar-title text-warning"></i></div>';
                                    status = 'กำลังดำเนินการ';
                                    break;
                                case 'check':
                                    icon =
                                        '<div class="avatar-lg rounded-circle bg-soft-primary border-primary border">\
                                                            <i class="fe-user-check font-24 avatar-title text-primary"></i></div>';
                                    status = 'รายการรอตรวจสอบ';
                                    break;
                                case 'success':
                                    icon =
                                        '<div class="avatar-lg rounded-circle bg-soft-success border-success border">\
                                                            <i class="fe-thumbs-up font-24 avatar-title text-success"></i></div>';
                                    status = 'งานที่เสร็จเรียบร้อย';
                                    break;
                            }

                            val += '<div class="col-md-6 col-xl-3">\
                                        <div class="card ' + bg + '">\
                                            <div class="card-body p-0">\
                                                <div class="card ">\
                                                    <div class="card-body">\
                                                        <div class="row">\
                                                            <div class="col-6">' + icon + '</div>\
                                                            <div class="col-6">\
                                                                <div class="text-end">\
                                                                    <h3 class="text-dark mt-1"><span>' + detail + '</span></h3>\
                                                                    <p class="text-muted mb-1 text-truncate">' + status + '</p>\
                                                                    <p class="text-muted mb-0 text-truncate">' + text + '</p>\
                                                                </div>\
                                                            </div>\
                                                        </div> \
                                                    </div>\
                                                </div> \
                                            </div>\
                                        </div> \
                                </div>';
                        }
                    }
                }
            }
            document.getElementById("container-card-detail").innerHTML = row + val + rows;
        }

        async function charts(datas) {
            var _chart = (datas) ? datas : {!! json_encode($_chart) !!};
            var dash_year = $("#dash_year").val();

            var dash_years = dash_year.split('/')[1];
            var _year = parseInt(new Date().getFullYear()) + 543;

            var year = (dash_year) ? parseInt(dash_years) + 543 : _year
            await Highcharts.chart('container-charts', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Dashboard แจ้งซ่อมประจำปี ' + year,
                    align: 'center'
                },
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ]
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'จำนวนงาน'
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            fontWeight: 'bold',
                            color: ( // theme
                                Highcharts.defaultOptions.title.style &&
                                Highcharts.defaultOptions.title.style.color
                            ) || 'gray',
                            textOutline: 'none'
                        }
                    }
                },
                legend: {
                    align: 'right',
                    x: -10,
                    verticalAlign: 'top',
                    y: 50,
                    floating: true,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || 'white',
                    borderColor: '#CCC',
                    borderWidth: 1,
                    shadow: false
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    pointFormat: '{series.name}: {point.y}'
                    // pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                },
                plotOptions: {
                    column: {
                        // dashStyle: 'ShortDot',
                        borderRadius: 3,
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                series: [{
                    name: 'แผนกไฟฟ้าและสุขาภิบาล',
                    data: _chart[0],
                    color: '#5DADE2',
                }, {
                    name: 'แผนกยานยนต์',
                    data: _chart[1],
                    color: '#EC7063',
                }, {
                    name: 'แผนกซ่อมบำรุง',
                    data: _chart[2],
                    color: '#82E0AA',
                }, {
                    name: 'แผนกไอที',
                    data: _chart[3],
                    color: '#F7DC6F',
                }]
            });
        }
    </script>
@endsection
