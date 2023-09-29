@extends('layouts.master-layout', ['page_title' => "ประวัติใบปะหน้าพัสดุ"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/inputdate/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">ใบปะหน้าพัสดุ</li>
                    </ol>
                </div>
                <h4 class="page-title">ประวัติใบปะหน้าพัสดุ</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form id="search-form" action="{{ route('shipping.search_history') }}" class="mb-3" method="GET"
                        enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="eplatform" class="form-label">ร้านค้า</label>
                                <select class="form-select" id="eplatform" name="eplatform">
                                    <option value="" selected>ทั้งหมด</option>
                                    @foreach ($eplatform as $list)
                                    <option value="{{ $list->id }}" {{ (old('eplatform')==$list->id) ? 'selected' : ''
                                        }}>{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="so" class="form-label">หมายเลขขนส่ง</label>
                                <input class="form-control" type="text" placeholder="TRACKINGNUMBER" id="trackingnumber"
                                    name="trackingnumber" value="{{ old('trackingnumber') }}">
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="so" class="form-label">หมายเลขออเดอร์</label>
                                <input class="form-control" type="text" placeholder="ORDERNUMBER" id="ordernumber"
                                    name="ordernumber" value="{{ old('ordernumber') }}">
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="shelflayer" class="form-label">SO</label>
                                <input class="form-control" type="text" placeholder="SALEORDER" id="so" name="so"
                                    value="{{ old('so') }}">
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="order_date" class="form-label">วันที่ออเดอร์ (เริ่มต้น)</label>
                                <input type="text" class="form-control custom-datepicker" placeholder="ORDER DATE"
                                    id="order_date_start" name="order_date_start"
                                    value="{{ (isset($current_date)) ? $current_date : old('order_date_start') }}">
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="order_date" class="form-label">วันที่ออเดอร์ (สิ้นสุด)</label>
                                <input type="text" class="form-control custom-datepicker" placeholder="ORDER DATE"
                                    id="order_date_end" name="order_date_end"
                                    value="{{ (isset($current_date)) ? $current_date : old('order_date_end') }}">
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="search" class="form-label">&nbsp;</label><br>
                                <button type="submit" id="search" name="search"
                                    class="btn btn-dark w-100">ค้นหา</button>
                            </div>
                        </div>
                        <hr>
                        <input type="hidden" class="form-control" id="action" name="action" value="">
                        <button type="submit" class="btn btn-sm btn-light float-end" onclick="document.getElementById('action').value='export';setTimeout(() => {document.getElementById('action').value='';}, 500);">Excel</button>
                        <br>
                    </form>
                    <table data-toggle="table" class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th data-field="no" data-sortable="true">ลำดับ</th>
                                <th data-field="trackingnumber" data-sortable="true">หมายเลขขนส่ง</th>
                                <th data-field="delivery_date" data-sortable="true">วันที่ขนส่ง</th>
                                <th data-field="ordernumber" data-sortable="true">หมายเลขออเดอร์</th>
                                <th data-field="order_date" data-sortable="true">วันที่ออเดอร์</th>
                                <th data-field="so" data-sortable="true">หมายเลข SO</th>
                                <th data-field="qty" data-sortable="true">จำนวน SO</th>
                                <th data-field="packages" data-sortable="true">แพ็คเกจ</th>
                                <th data-field="eplatform" data-sortable="true">ร้านค้า</th>
                                <th data-field="user" data-sortable="true">ผู้พิมพ์</th>
                                <th data-field="updated_at" data-sortable="true">วันที่พิมพ์</th>
                                <th data-field="print" data-sortable="true">ครั้งพิมพ์</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shippingHistory as $list)
                            <tr>
                                <td class="lh35">{{$i+1}}</td>
                                <td class="lh35">{{$list->trackingnumber}} <a
                                        href="/shipping/trackingnumber/{{$list->trackingnumber}}" target="_blink"><i
                                            class="fas fa-file-pdf text-danger"></i></a></td>
                                <td class="lh35">{{\Carbon\Carbon::parse($list->delivery_date)->format('d/m/Y')}}</td>
                                <td class="lh35">{{$list->ordernumber}}</td>
                                <td class="lh35">{{\Carbon\Carbon::parse($list->order_date)->format('d/m/Y')}}</td>
                                <td class="lh35">
                                    {!! str_replace(",", "<br>", $list->so_list); !!}
                                </td>
                                <td class="lh35">{{$list->so_count}}</td>
                                <td class="lh35">{{$list->packages}}</td>
                                <td class="lh35">{{$list->eplatform_name}}</td>
                                <td class="lh35">{{$list->username}}</td>
                                <td class="lh35">{{\Carbon\Carbon::parse($list->updated_at)->format('d/m/Y H:i:s')}}</td>
                                <td class="lh35">
                                    {{$list->print_count}}
                                    <a class="action-icon" href="javascript:void(0);" onclick="infoLog('{{$list->trackingnumber}}');"><i class="mdi mdi-clipboard-list-outline"></i></a>
                                </td>
                            </tr>
                            @php $i++ @endphp
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex mt-3 overflow-auto">
                        {!! $shippingHistory->withQueryString()->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Info Alert Modal -->
    <div id="info-log-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-information h1 text-info"></i>
                        <h4 class="mt-2">จำนวนครั้งที่พิมพ์</h4>
                        <p class="mt-3 info-log"></p>
                        <button type="button" class="btn btn-info my-2" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
                @if (Auth::User()->manageShipping())
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" onclick="clearShippingHistory();">ล้างประวัติใบปะหน้า</button>
                </div>
                @endif
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
{{-- inputdate --}}
<script src="{{ asset('assets/js/inputdate/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/dist/l10n/th.js') }}"></script>
<script src="{{ asset('assets/js/inputdate/form-pickers.init.js') }}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    var myLogModal = new bootstrap.Modal(document.getElementById('info-log-modal'));
    function infoLog(trackingnumber) {
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });
            $.ajax({
                url: "{{ url('shipping/print-history-log') }}",
                method: 'get',
                data: {
                    trackingnumber: trackingnumber
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success == true) {
                        var detail = 'หมายเลขขนส่ง: '+trackingnumber+'<hr>';
                        for (var i=0; i<res.data.length; i++) {
                            if (i == 0) {
                                detail += 'หมายเลข SO: '+res.data[i].so+'<br>';
                            }
                            if (i > 0 && res.data[i].so != res.data[(i-1)].so) {
                                detail += '<hr>';
                                detail += 'หมายเลข SO: '+res.data[i].so+'<br>';
                            }
                            var date = new Date(res.data[i].updated_at);
                            var create_time = date.getDate().toString().padStart(2, "0") + '/' + (date.getMonth() + 1).toString().padStart(2, "0") +
                            '/' + date.getFullYear() + ' ' + date.getHours().toString().padStart(2, "0") + ':' + date.getMinutes().toString().padStart(2, "0") + ':' + date.getSeconds().toString().padStart(2, "0");
                            detail += '<br>ครั้งที่: <u>'+(i+1)+'</u> ผู้พิมพ์: <u>'+res.data[i].username+'</u> เวลา: <u>'+create_time+'</u>';
                        }
                        detail += '<input type="hidden" id="clear_trackingnumber" value="'+trackingnumber+'">';
                        $(".info-log").html(detail);
                        myLogModal.show();
                    }
                }
            });
    }
    function clearShippingHistory() {
        var clear_trackingnumber = document.getElementById("clear_trackingnumber").value;
        Swal.fire({
            icon: "warning",
            title: "คุณต้องการล้างประวัติใบปะหน้าใช่ไหม?",
            text: "หมายเลขขนส่ง: " + clear_trackingnumber,
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการ!",
            cancelButtonText: "ยกเลิก",
            showLoaderOnConfirm: true,
            stopKeydownPropagation: false,
            preConfirm: () => {
                return fetch(`/shipping/print-history-clear/` + clear_trackingnumber)
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .catch((error) => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: "success",
                    title: "ล้างประวัติเรียบร้อย!",
                    timer: 2000,
                });
                setTimeout(() => {
                    myLogModal.hide();
                    location.reload();
                }, 2000);
            }
        });
    }
</script>
@endsection