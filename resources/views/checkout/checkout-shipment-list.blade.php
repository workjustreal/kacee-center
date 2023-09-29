@extends('layouts.master-layout', ['page_title' => "ประวัติเช็คเอาท์การจัดส่ง"])
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
                        <li class="breadcrumb-item active">เช็คเอาท์</li>
                    </ol>
                </div>
                <h4 class="page-title">ประวัติเช็คเอาท์การจัดส่ง</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form id="search-form" action="{{ route('checkout-shipment.search_history') }}" class="mb-3" method="GET"
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
                                <label for="ship_com" class="form-label">ขนส่ง</label>
                                <select class="form-select" id="ship_com" name="ship_com">
                                    <option value="" selected>ทั้งหมด</option>
                                    @foreach ($ship_com as $list)
                                    <option value="{{ $list->id }}" {{ (old('ship_com')==$list->id) ? 'selected' : ''
                                        }}>{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="vehicle_registration" class="form-label">ทะเบียนรถ</label>
                                <input class="form-control" type="text" placeholder="VEHICLE REGISTRATION" id="vehicle_registration"
                                    name="vehicle_registration" autocomplete="off" value="{{ old('vehicle_registration') }}">
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="trackingnumber" class="form-label">หมายเลขขนส่ง</label>
                                <input class="form-control" type="text" placeholder="TRACKINGNUMBER" id="trackingnumber"
                                    name="trackingnumber" autocomplete="off" value="{{ old('trackingnumber') }}">
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="ordernumber" class="form-label">หมายเลขออเดอร์</label>
                                <input class="form-control" type="text" placeholder="ORDERNUMBER" id="ordernumber"
                                    name="ordernumber" autocomplete="off" value="{{ old('ordernumber') }}">
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="so" class="form-label">หมายเลข SO</label>
                                <input class="form-control" type="text" placeholder="SALEORDER" id="so" name="so"
                                autocomplete="off" value="{{ old('so') }}">
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="checkout_date_start" class="form-label">วันที่เช็คเอาท์ (เริ่มต้น)</label>
                                <input type="text" class="form-control custom-datepicker" placeholder="CHECKOUT DATE"
                                    id="checkout_date_start" name="checkout_date_start" autocomplete="off"
                                    value="{{ (isset($current_date)) ? $current_date : old('checkout_date_start') }}">
                            </div>
                            <div class="col-md-auto col-sm-12 mb-2">
                                <label for="checkout_date_end" class="form-label">วันที่เช็คเอาท์ (สิ้นสุด)</label>
                                <input type="text" class="form-control custom-datepicker" placeholder="CHECKOUT DATE"
                                    id="checkout_date_end" name="checkout_date_end" autocomplete="off"
                                    value="{{ (isset($current_date)) ? $current_date : old('checkout_date_end') }}">
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
                    </form>
                    <table data-toggle="table" data-page-size="10" data-buttons-class="xs btn-light"
                        data-pagination="true" class="table-bordered" data-search="false">
                        <thead class="table-light">
                            <tr>
                                <th data-field="no" data-sortable="true" width="10">ลำดับ</th>
                                <th data-field="running" data-sortable="true">เลขเอกสาร</th>
                                <th data-field="checkout_date" data-sortable="true">วันที่เช็คเอาท์</th>
                                <th data-field="vehicle_registration" data-sortable="true">ทะเบียนรถ</th>
                                <th data-field="tracking" data-sortable="true">Tracking</th>
                                <th data-field="order" data-sortable="true">Order</th>
                                <th data-field="so" data-sortable="true">SO</th>
                                <th data-field="packaging" data-sortable="true">แพ็คเกจ</th>
                                <th data-field="eplatform" data-sortable="true">ร้านค้า</th>
                                <th data-field="ship_com" data-sortable="true">ขนส่ง</th>
                                <th data-field="remark" data-sortable="true">หมายเหตุ</th>
                                <th data-field="user" data-sortable="true">ผู้บันทึก</th>
                                <th data-field="manage" data-sortable="false">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($checkout as $list)
                            <tr>
                                <td class="lh35">{{$loop->index+1}}</td>
                                <td class="lh35"><a href="/checkout/shipment-detail/{{ $list->running }}">{{$list->running}}</a></td>
                                <td class="lh35">{{\Carbon\Carbon::parse($list->checkout_date)->format('d/m/Y H:i:s')}}</td>
                                <td class="lh35">{{$list->vehicle_registration}}</td>
                                <td class="lh35">{{number_format($list->tracking_count)}}</td>
                                <td class="lh35">{{number_format($list->order_count)}}</td>
                                <td class="lh35">{{number_format($list->so_count)}}</td>
                                <td class="lh35">{{number_format($list->packaging_total)}}</td>
                                <td class="lh35">{!! str_replace(",", "<br>", $list->eplatform_list); !!}</td>
                                <td class="lh35">{{$list->ship_com_name}}</td>
                                <td class="lh35">{{$list->remark}}</td>
                                <td class="lh35">{{$list->username}}</td>
                                <td class="lh35">
                                    @if (auth()->user()->isAdmin())
                                        <a class="action-icon" href="/checkout/shipment-tracking/{{ $list->running }}" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                                        <a class="action-icon" href="javascript:void(0);" onclick="delConfirmationCheckoutShipment('{{ $list->running }}')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                                    @else
                                        @if (Auth::User()->manageShipping() || auth()->user()->id == $list->userid)
                                            @if (date('Y-m-d') == \Carbon\Carbon::parse($list->checkout_date)->format('Y-m-d'))
                                            <a class="action-icon" href="/checkout/shipment-tracking/{{ $list->running }}" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                                            @endif
                                            <a class="action-icon" href="javascript:void(0);" onclick="delConfirmationCheckoutShipment('{{ $list->running }}')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
    function delConfirmationCheckoutShipment(id) {
        Swal.fire({
            title: "คุณต้องการลบ ใช่ไหม?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ดำเนินการลบ!",
            cancelButtonText: "ยกเลิก",
        }).then((willDelete) => {
            if (willDelete.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    }
                });
                $.ajax({
                    url: "{{ url('checkout/shipment-del') }}/"+id,
                    method: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        if (res.success == true) {
                            Swal.fire({
                                icon: "success",
                                title: res.message,
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            Swal.fire({
                                icon: "warning",
                                title: res.message,
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        }
                    }
                });
            }
        });
    }
</script>
@endsection