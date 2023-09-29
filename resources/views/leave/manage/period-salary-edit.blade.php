@extends('layouts.master-layout', ['page_title' => "แก้ไขงวดค่าแรง"])
@section('css')
<!-- third party css -->
<link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Kacee</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Leave</a></li>
                        <li class="breadcrumb-item active">แก้ไขงวดค่าแรง</li>
                    </ol>
                </div>
                <h4 class="page-title">แก้ไขงวดค่าแรง</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form action="{{ route('period-salary.update') }}" class="wow fadeInLeft" method="POST"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <input type="hidden" class="form-control" id="id" name="id" value="{{ $period->id }}">
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label class="form-label">ปี</label>
                                    <div class="form-group">
                                        <select class="form-select form-control-required" id="year" name="year" required>
                                            <option value="{{ date('Y')-1 }}" @if ($period->year==(date('Y')-1)) selected @endif>{{ (date('Y')+543)-1 }}</option>
                                            <option value="{{ date('Y') }}" @if ($period->year==date('Y')) selected @endif>{{ (date('Y')+543) }}</option>
                                            <option value="{{ date('Y')+1 }}" @if ($period->year==(date('Y')+1)) selected @endif>{{ (date('Y')+543)+1 }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label class="form-label">เดือน</label>
                                    <div class="form-group">
                                        <select class="form-select form-control-required" id="month" name="month" required>
                                            <option value="">-</option>
                                            @foreach ($month as $list)
                                            <option value="{{ $list["id"] }}" @if ($period->month==$list["id"]) selected @endif>{{ $list["th"] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label class="form-label">เริ่ม</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-required custom-datepicker" id="start" name="start" value="{{ \Carbon\Carbon::parse($period->start)->format('d/m/Y') }}" required>
                                        <span class="input-group-text"><i class="fe-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label class="form-label">สิ้นสุด</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-required custom-datepicker" id="end" name="end" value="{{ \Carbon\Carbon::parse($period->end)->format('d/m/Y') }}" required>
                                        <span class="input-group-text"><i class="fe-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label class="form-label">วันสุดท้ายของการลางาน</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control custom-datepicker" id="last" name="last" value="{{ ($period->last!='') ? \Carbon\Carbon::parse($period->last)->format('d/m/Y') : '' }}">
                                        <span class="input-group-text"><i class="fe-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-2">
                                    <label class="form-label">หมายเหตุ</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="remark" name="remark" value="{{ $period->remark }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-12 col-sm-12 pt-3 mt-3 mb-5">
                                    @if ($errors->any())
                                    <a class="btn btn-secondary" href="{{ url('/leave/manage/period-salary') }}">ย้อนกลับ</a>
                                    @else
                                    <button type="button" class="btn btn-secondary" onclick="history.back()">ย้อนกลับ</button>
                                    @endif
                                    <button type="submit" class="btn btn-primary mx-2">อัปเดต</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    $(document).ready(function() {
        $(".custom-datepicker").flatpickr({
            locale: "th",
            dateFormat: "d/m/Y",
            onOpen: function(selectedDates, dateStr, instance){
                var from_year = parseInt(document.getElementById('year').value);
                var from_month = parseInt(document.getElementById('month').value) - 1;
                if (from_month >= 0) {
                    if (parseInt(from_month) == 11) {
                        var to_year = parseInt(from_year) + 1;
                        var to_month = 0;
                    } else {
                        var to_year = from_year;
                        var to_month = parseInt(from_month) + 1;
                    }
                    var minDate = new Date(parseInt(from_year), parseInt(from_month), 1);
                    var maxDate = new Date(parseInt(to_year), parseInt(to_month), 0);
                    if ($("#start").val() != "" && $("#end").val() != "") {
                        maxDate.setDate(maxDate.getDate() + 5);
                        instance.set('minDate', formatDate(minDate));
                        instance.set('maxDate', formatDate(maxDate));
                    } else {
                        instance.set('minDate', formatDate(minDate));
                        instance.set('maxDate', formatDate(maxDate));
                    }
                }
            },
            onReady: function ( dateObj, dateStr, instance ) {
                const $clear = $('<div class="flatpickr-clear"><button class="btn btn-sm btn-link">Clear</button></div>')
                .on('click', () => {
                    instance.clear();
                    instance.close();
                })
                .appendTo( $( instance.calendarContainer ) );
            },
        });
    });
    function padTo2Digits(num) {
        return num.toString().padStart(2, '0');
    }
    function formatDate(date = new Date()) {
        return [
            padTo2Digits(date.getDate()),
            padTo2Digits(date.getMonth() + 1),
            date.getFullYear(),
        ].join('/');
    }
</script>
@endsection
