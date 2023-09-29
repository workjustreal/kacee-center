@extends('layouts.master-layout', ['page_title' => "ค้นหาสินค้า"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">สินค้า</li>
                    </ol>
                </div>
                <h4 class="page-title">ค้นหาสินค้า</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div id="toolbar" class="row">
                        <div class="col-lg-auto col-md-3 col-sm-12 mb-2">
                            <select class="form-select" name="group" id="group">
                                <option value="all" selected>กลุ่มสินค้าทั้งหมด</option>
                                @foreach ($groups as $group)
                                <option value="{{ $group->typcod }}">
                                    {{ $group->typdes }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-auto col-md-4 col-sm-12 mb-2">
                            <input type="text" class="form-control" placeholder="ค้นหาสินค้า" name="search" id="search">
                        </div>
                    </div>
                    <table id="table"
                        data-toggle="table"
                        data-buttons-class="btn btn-sm btn-secondary"
                        data-toolbar="#toolbar"
                        data-ajax="ajaxRequest"
                        data-query-params="queryParams"
                        data-undefined-text=""
                        data-search="true"
                        data-search-align="left"
                        data-search-selector="#search"
                        data-pagination="true"
                        data-page-size="25"
                        data-show-columns="true"
                        data-show-print="true"
                        data-show-export="true"
                        data-export-data-type="all"
                        data-export-types='["excel"]'
                        data-export-options='{
                            "fileName": "ค้นหาสินค้า",
                            "mso": {
                                "fileFormat": "xlsx",
                                "worksheetName": ["Sheet1"],
                                "xlsx": {
                                    "formatId": {
                                        "numbers": 1
                                    }
                                }
                            }
                        }'
                        class="table table-striped text-nowrap">
                        <thead>
                            <tr class="text-center invisible">
                                <th colspan="8">ค้นหาสินค้า</th>
                            </tr>
                            <tr>
                                <th data-field="no" data-sortable="true">ลำดับ</th>
                                <th data-field="stkcod" data-sortable="true" data-formatter="nameFormatterSKU">รหัสสินค้า</th>
                                <th data-field="barcod" data-sortable="true" data-formatter="nameFormatterBarcode">บาร์โค้ด</th>
                                <th data-field="stkdes" data-sortable="true">รายละเอียด</th>
                                <th data-field="typdes" data-sortable="true">กลุ่ม</th>
                                <th data-field="series" data-sortable="true">ซีรีย์</th>
                                <th data-field="product_type" data-sortable="true">ประเภท</th>
                                <th data-field="detail" data-sortable="true">เพิ่มเติม</th>
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
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table-style.js')}}"></script>
<script src="{{asset('assets/js/barcodes/index.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/xlsx.core.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/tableExport.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table-export.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table-print.min.js')}}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    var $table = $('#table');
    $(document).ready(function() {
        $("#group").change(function() {
            $table.bootstrapTable('refreshOptions', {
                group: $("#group").val()
            });
        });
    });
    function nameFormatterSKU(value, row) {
        return `<div class="d-flex justify-content-between align-items-center"><span class="text-line me-2">`+row.stkcod+`</span><i class="fas fa-copy copy-button" role="button" onclick="copy('` + row.stkcod + `','#copy_button_` + (row.no-1) + `')" id="copy_button_` + (row.no-1) + `" title="Copy"></i></div>`;
    }
    function nameFormatterBarcode(value, row) {
        return `<div class="d-flex justify-content-between align-items-center"><span class="text-line me-2">`+row.barcod+`</span><i class="fas fa-copy copy-button" role="button" onclick="copy('` + row.barcod + `','#copy2_button_` + (row.no-1) + `')" id="copy2_button_` + (row.no-1) + `" title="Copy"></i></div>`;
    }
    function queryParams(params) {
        params.group = $("#group").val();
        $('button[name="print"]').html('<i class="dripicons-print mt-1"></i>');
        $('div.export > button').html('<i class="dripicons-download mt-1"></i>');
        $('div.keep-open > button').html('<i class="dripicons-checklist mt-1"></i>');
        return params;
    }
    function ajaxRequest(params) {
        var url = "{{ Route('pd.search2') }}";
        $.get(url + '?' + $.param(params.data)).then(function (res) {
            params.success(res)
        });
    }
    // function ExportToExcel(type, fn, dl) {
    //     var elt = document.getElementById('table');
    //     var wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet1", raw: true });
    //     var ws = wb.Sheets["Sheet1"];
    //     ws['!cols'] = [{'wch': 5},{'wch': 15},{'wch': 15},{'wch': 50},{'wch': 20},{'wch': 20},{'wch': 20},{'wch': 20}];
    //     return dl ?
    //         XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }):
    //         XLSX.writeFile(wb, fn || ('ค้นหาสินค้า.' + (type || 'xlsx')));
    // }
</script>
@endsection
