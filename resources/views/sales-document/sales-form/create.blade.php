@extends('layouts.master-layout', ['page_title' => 'การลงสินค้าให้ลูกค้า'])
@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/selectize/selectize.min.css') }}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                            <li class="breadcrumb-item active">Sales Form</li>
                        </ol>
                    </div>
                    <h4 class="page-title">เพิ่มรายการ</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <!-- start form -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card border">
                            <div class="card-header"><b>เพิ่มรายการ</b></div>
                            <div class="card-body">
                                <form action="{{ route('sd.sales_form.store') }}" class="wow fadeInLeft" method="post">
                                    @csrf
                                    <div class="col-12">
                                        <div class="col-md-9 col-lg-5 pt-2">
                                            <div class="form-group ">
                                                <label class="control-label">รหัสลูกค้า</label>
                                                <input type="text" name="customer_code" id="customer_code"
                                                    class="form-control form-control-md form-control-required"
                                                    autocomplete="off" required="" placeholder="กรุณากรอกรหัสลูกค้า" />
                                                <div id="suggesstion-box"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-9 col-lg-5 pt-2">
                                            <div class="form-group ">
                                                <label class="control-label">ชื่อร้าน</label>
                                                <input type="text"
                                                    class="form-control form-control-md form-control-required"
                                                    id="customer_name" name="customer_name" placeholder="กรุณากรอกชื่อร้าน"
                                                    value="" required="">
                                            </div>
                                        </div>

                                        <div class="col-md-9 col-lg-5 pt-2">
                                            <div class="form-group ">
                                                <label class="control-label">เลขที่ IV</label>
                                                <input type="text"
                                                    class="form-control form-control-md form-control-required"
                                                    id="invoice" name="invoice" placeholder="กรุณากรอกเลขที่ IV"
                                                    value="" required="">
                                            </div>
                                        </div>

                                        <div class="col-md-9 col-lg-5 pt-2">
                                            <div class="form-group ">
                                                <label class="control-label">ยอดเงิน</label>
                                                <input type="number"
                                                    class="form-control form-control-md form-control-required"
                                                    id="pay" name="pay" placeholder="กรุณากรอกยอดเงิน"
                                                    value="" required="">
                                            </div>
                                        </div>

                                        <div class="col-md-9 col-lg-5 pt-2">
                                            <div class="fom-group ">
                                                <label class="control-label">หมายเหตุ :</label>
                                                <textarea class="form-control form-control-md form-control-required" id="comment" name="comment" placeholder="..."
                                                    rows="2" value=""></textarea>
                                            </div>
                                        </div>


                                        <div class="col-lg-12 col-md-12 col-sm-12 pt-3">
                                            <input type="hidden" name="SQL" value="INS">
                                            <a class="btn btn-white" href="{{ url('sales-document/sales-form') }}"><i
                                                    class="fe-arrow-left"></i> ย้อนกลับ</a>
                                            <button type="submit" class="btn btn-primary mx-2" id="btn-submit"><i
                                                    class="fe-save"></i> บันทึก</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end form -->
    </div>
@endsection
@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap3-typeahead.js') }}"></script>
    <!-- third party js ends -->

    <script type="text/javascript">
        var route = "{{ route('sd.sales_form.Auto') }}";
        $("#customer_code").typeahead({
            minLength: 1,
            items: 10,
            showHintOnFocus: "all",
            selectOnBlur: false,
            autoSelect: true,
            displayText: function(item) {
                return item.cuscod + ' : ' + item.prenam + " " + item.cusnam;
            },
            afterSelect: function(item) {
                this.$element[0].value = item.cuscod;
                console.log(item);
                if (item.prenam) {
                    $("#customer_name").val(item.prenam + " " + item.cusnam);
                } else {
                    $("#customer_name").val(item.cusnam);
                }
            },
            source: function(search, process) {
                return $.get(
                    route, {
                        search: search
                    },
                    function(data) {
                        $("#customer_name").val("");
                        return process(data);
                    }
                );
            },
        });


        // function clean(params) {
        //     params.status_category = $("#status_category").val();
        //     params.doc_date = $("#doc_date").val();
        //     return params;
        // }
    </script>
@endsection
