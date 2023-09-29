@extends('layouts.master-layout', ['page_title' => "เอกสารการจัดส่ง"])
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
                <h4 class="page-title">เอกสารการจัดส่ง</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="text-center d-print-none">เอกสารการจัดส่ง</h4>
                    <div class="text-center d-print-none">
                        <span class="text-center">เลขเอกสาร <b>{{ $header->running }}</b></span>&nbsp;&nbsp;
                        <span class="text-center">ทะเบียนรถ <b>{{ $header->vehicle_registration }}</b></span>&nbsp;&nbsp;
                        @if ($header->ship_com != 1)
                        <span class="text-center">ขนส่ง <b>{{ $header->ship_com_name }}</b></span>&nbsp;&nbsp;
                        @endif
                        <span class="text-center">วันที่เช็คเอาท์ <b>{{ \Carbon\Carbon::parse($header->checkout_date)->format('d/m/Y') }}</b></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ลำดับ</th>
                                    <th>หมายเลขขนส่ง</th>
                                    <th>หมายเลขออเดอร์</th>
                                    <th>หมายเลข SO</th>
                                    <th>แพ็คเกจ</th>
                                    <th>ร้านค้า</th>
                                    <th>เวลา</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $active = 1;
                                    $tracking = "";
                                    $bg = "table-secondary text-secondary";
                                @endphp
                                @foreach ($detail as $list)
                                @php
                                    if ($loop->index == 0) {
                                        $tracking = $list->trackingnumber;
                                    } else {
                                        if ($list->trackingnumber != $tracking) {
                                            $tracking = $list->trackingnumber;
                                            $bg = ($bg=="") ? "table-secondary text-secondary" : "";
                                        }
                                        $active = 1;
                                    }
                                @endphp
                                    <tr class="{{ $bg }}">
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $list->trackingnumber }}</td>
                                        <td>{{ $list->ordernumber }}</td>
                                        <td>{{ $list->so }}</td>
                                        <td>{{ $list->packaging_total }}</td>
                                        <td>{{ $list->eplatform_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($list->updated_at)->format('H:i:s') }}</td>
                                    </tr>
                                @endforeach
                                @if ($dataSumTotal->tracking_total <= 0)
                                <tr>
                                    <td colspan="7" class="text-center">ไม่พบข้อมูล</td>
                                </tr>
                                @endif
                            </tbody>
                            @if ($dataSumTotal->tracking_total > 0)
                            <tfoot>
                                @foreach ($dataSumShop as $list)
                                <tr>
                                    <th>{{ $list->eplatform_name }}</th>
                                    <th>{{ number_format($list->tracking_total) }}</th>
                                    <th>{{ number_format($list->order_total) }}</th>
                                    <th>{{ number_format($list->so_total) }}</th>
                                    <th>{{ number_format($list->packaging_total) }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                @endforeach
                                <tr>
                                    <th>รวมทั้งหมด</th>
                                    <th>{{ number_format($dataSumTotal->tracking_total) }}</th>
                                    <th>{{ number_format($dataSumTotal->order_total) }}</th>
                                    <th>{{ number_format($dataSumTotal->so_total) }}</th>
                                    <th>{{ number_format($dataSumTotal->packaging_total) }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                        @if($header->remark!='')
                        <p>{{ $header->remark }}</p>
                        @endif
                    </div>
                    <div class="mt-4 mb-1">
                        <div class="d-flex justify-content-between d-print-none">
                            <a href="javascript:history.back()" class="btn btn-secondary waves-effect waves-light"><i class="mdi mdi-keyboard-backspace me-1"></i> ย้อนกลับ</a>
                            {{-- <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-printer me-1"></i> พิมพ์</a> --}}
                            <div>
                                <a href="{{ url('checkout/shipment2-print/'.$header->running) }}" target="_blank" class="btn btn-blue waves-effect waves-light"><i class="mdi mdi-printer me-1"></i> พิมพ์ (สำหรับขนส่ง)</a>
                                <a href="{{ url('checkout/shipment-print/'.$header->running) }}" target="_blank" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-printer me-1"></i> พิมพ์</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection