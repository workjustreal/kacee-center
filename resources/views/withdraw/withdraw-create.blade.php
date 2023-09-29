@extends('layouts.master-nopreloader-layout', ['page_title' => 'ใบเบิกอุปกรณ์'])
@section('css')
@endsection

@section('content')
    @inject('thaiDateHelper', '\App\Services\ThaiDateHelperService')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Withdraw</a></li>
                            <li class="breadcrumb-item active">ใบเบิกอุปกรณ์</li>
                        </ol>
                    </div>
                    <h5 class="page-title">เพิ่มรายการอุปกรณ์-รหัสใบแจ้งซ่อม : {{ $oid }}</h5>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="col-12">
            <div class="card">
                <div class="card-header bg-soft-warning">ส่วนที่ 1 เพิ่มรายการอุปกรณ์ กรอกรายละเอียดให้ครบถ้วนชัดเจน
                </div>
                <div class="card-body">
                    <form action="{{ route('withdraw.store') }}" id="soform" class="wow fadeInLeft" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group mb-1">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3">
                                            <label class="control-label">วันที่ :</label>
                                        </div>
                                        <div class="col-lg-8 col-md-8">
                                            <input type="date" class="form-control form-control-md form-control-required"
                                                id="withdraw_date" name="withdraw_date"
                                                value="@php echo date("Y-m-d"); @endphp" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-1">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3">
                                            <label class="control-label">ชื่ออุปกรณ์ :</label>
                                        </div>
                                        <div class="col-lg-8 col-md-8">
                                            <input type="text" class="form-control form-control-md form-control-required"
                                                id="products_name" name="products_name" placeholder="กรุณากรอกชื่ออุปกรณ์"
                                                value="" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-2 mb-1">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3">
                                            <label class="control-label">เบิกอุปกรณ์ :</label>
                                        </div>
                                        <div class="col-lg-8 col-md-8">
                                            <div class="form-check mb-1">
                                                <input class="form-check-input" type="radio" name="status_inventory"
                                                    id="level1" value="0" required>
                                                <label class="form-check-label" for="level1">
                                                    อุปกรณ์จากคลังสินค้า
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status_inventory"
                                                    id="level2" value="1" required>
                                                <label class="form-check-label" for="level2">
                                                    อุปกรณ์สั่งซื้อใหม่
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group mb-1">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3">
                                            <label class="control-label">จำนวน :</label>
                                        </div>
                                        <div class="col-lg-8 col-md-8">
                                            <input type="number" class="form-control form-control-md form-control-required"
                                                id="qty" name="qty" placeholder="กรุณากรอกจำนวนอุปกรณ์"
                                                min="1" value="" onchange="calculateTotal()" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-1">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3">
                                            <label class="control-label">ราคาต่อหน่วย :</label>
                                        </div>
                                        <div class="col-lg-8 col-md-8">
                                            <input type="number" class="form-control form-control-md form-control-required"
                                                id="prices" name="prices" placeholder="กรุณากรอกราคา" min="0"
                                                value="0" onchange="calculateTotal()" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-1">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3">
                                            <label class="control-label">รวมราคา :</label>
                                        </div>
                                        <div class="col-lg-8 col-md-8">
                                            <input type="number" class="form-control form-control-md" id="total_prices"
                                                name="total_prices" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group mt-2 mb-1">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3">
                                            <label class="control-label">หมายเหตุ :</label>
                                        </div>
                                        <div class="col-lg-8 col-md-8">
                                            <textarea class="form-control form-control-md" id="comment" name="comment" rows="3" value=""></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="col-lg-12 col-md-12">
                        <div class="text-center">
                            <input type="hidden" name="repair_id" value="{{ $oid }}">

                            <a class="btn btn-secondary" href="{{ url('/repair/withdraw-list', $oid) }}">
                                <i class="fe-arrow-left"></i> Back
                            </a>
                            <button type="button" class="btn btn-light mx-2" onclick="clearValue()">
                                <i class="fe-rotate-ccw me-1"></i> Clear
                            </button>
                            <button type="button" class="btn btn-success" id="btn-submit"
                                onclick="validateAndConfirm()">
                                <i class="fe-save me-1"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- @if ( count($items) > 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-soft-warning">รายการอุปกรณ์
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <th>ลำดับ</th>
                                <th>ชื่ออุปกรณ์</th>
                                <th>เบิกอุปกรณ์</th>
                                <th>จำนวน</th>
                                <th>ราคาต่อหน่วย</th>
                                <th>รวมราคา</th>
                                <th>วันที่</th>
                                <th>หมายเหตุ</th>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $item->products_name }}</td>
                                        <td>{{ $item->status_inventory }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>{{ $item->prices }}</td>
                                        <td>{{ $item->total_prices }}</td>
                                        <td>{{ $item->withdraw_date }}</td>
                                        <td>{{ $item->comment }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif --}}
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script type="text/javascript">
        function calculateTotal() {
            var price = document.getElementById('prices').value;
            var qty = document.getElementById('qty').value;
            if (price != '' && qty != '') {
                var total_price = price * qty;
                // Display the total_price in the total_price input field
                document.getElementById('total_prices').value = total_price;
            }
        }

        function validateAndConfirm() {
            var form = document.getElementById("soform");
            if (form.checkValidity()) {
                btnConfirm();
            } else {
                form.reportValidity(); // This line will show the browser's default validation message
            }
        }

        function btnConfirm() {
            Swal.fire({
                title: "คุณต้องการดำเนินการต่อ ใช่ไหม?",
                icon: "warning",
                showCancelButton: true,
                showLoaderOnConfirm: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ใช่",
                cancelButtonText: "ยกเลิก",
                allowOutsideClick: () => !Swal.isLoading(),
            }).then((willDelete) => {
                if (willDelete.isConfirmed) {
                    document.getElementById("soform").submit();
                }
            });
        }

        function clearValue() {
            document.getElementById('soform').reset();
        }
    </script>
@endsection
