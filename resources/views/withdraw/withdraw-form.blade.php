@extends('layouts.master-nopreloader-layout', ['page_title' => 'ใบเบิกอุปกรณ์'])
@section('css')
    <link href="{{ asset('assets/css/placeholder-loading.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/bootstrap-table-style.css') }}" rel="stylesheet" type="text/css" />
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
                    <h5 class="page-title">ใบเบิกอุปกรณ์-รหัสใบแจ้งซ่อม : {{ $oid }}</h5>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="col-12">
            <div class="card">
                <div class="card-header bg-soft-warning">ส่วนที่ 1 เพิ่มรายการอุปกรณ์ กรอกรายละเอียดให้ครบถ้วนชัดเจน
                </div>
                <div class="card-body">
                    <form id="soform" class="wow fadeInLeft" method="post">
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
                            {{-- <input type="hidden" name="status" value="INS"> --}}
                            <input type="hidden" name="repair_id" value="{{ $oid }}">

                            <a class="btn btn-secondary" href="{{ url('/repair/action') }}">
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

        <div class="col-12">
            <div class="card">
                <div class="card-header bg-soft-info">
                    <span>ส่วนที่ 2 รายการอุปกรณ์</span>
                </div>
                <div class="card-body">

                    <div class="float-end">
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                            data-bs-target="#printwithdrawModal" {{ $count_data > 0 ? '' : 'disabled' }}>
                            <i class="mdi mdi-printer me-1"></i> Print
                        </button>
                    </div>
                    <table class="table table-striped text-nowrap" id="table" data-toggle="table"
                        data-loading-template="loadingTemplate" data-buttons-class="btn btn-sm btn-secondary"
                        data-ajax="ajaxRequest" data-query-params="queryParams" data-undefined-text=""
                        data-search="true" data-search-align="left" data-pagination="true" data-page-size="10">
                        <thead>
                            <tr>
                                <th data-field="products_name" data-sortable="true">ชื่ออุปกรณ์</th>
                                <th data-field="status_inventory" data-sortable="true">เบิกอุปกรณ์</th>
                                {{-- <th data-field="prices" data-sortable="true">ราคาต่อหน่วย</th> --}}
                                <th data-field="qty" data-sortable="true">จำนวน</th>
                                <th data-field="total_prices" data-sortable="true">รวมราคา</th>
                                <th data-field="emp_id" data-sortable="true">ผู้เบิก</th>
                                <th data-field="withdraw_date" data-sortable="true">วันที่</th>
                                <th data-field="comment" data-sortable="true">หมายเหตุ</th>
                                <th data-field="action" data-sortable="false" data-width="150">จัดการ</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        {{-- printwithdrawModal --}}
        <div id="printwithdrawModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" role="dialog" aria-labelledby="printwithdrawModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="printwithdrawModalLabel">พิมพ์ใบเบิกอุปกรณ์</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <embed src="{{ url('withdraw/print-pdf', $oid) }}" frameborder="0" width="100%"
                            height="700px">
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-table/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/bootstrap-tables.init.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-table-style.js') }}"></script>
    <script type="text/javascript">
        function queryParams(params) {
            setTimeout(() => {
                params.oid = "{{ $oid }}";
                params.status_oid = "{{ $status }}";
            }, 200);
            return params;
        }

        function ajaxRequest(params) {
            setTimeout(() => {
                var url = "{{ route('withdraw.searchWithdraw') }}";
                $.get(url + '?' + $.param(params.data)).then(function(res) {
                    params.success(res)
                });
            }, 200);
        }

        function calculateTotal() {
            var price = document.getElementById('prices').value;
            var qty = document.getElementById('qty').value;
            if (price != '' && qty != '') {
                var total_price = price * qty;
                // Display the total_price in the total_price input field
                document.getElementById('total_prices').value = total_price;
            }
        }

        // function validateAndConfirm() {
        //     var form = document.getElementById("soform");
        //     if (form.checkValidity()) {
        //         btnConfirm();
        //     } else {
        //         form.reportValidity(); // This line will show the browser's default validation message
        //     }
        // }

        function validateAndConfirm(mode) {
            let form = document.getElementById('soform');

            if (form.checkValidity() === false) {
                form.reportValidity();
            } else {
                if (mode === 'INS') {
                    btnConfirmSave();
                }
                if (mode === 'UPD') {
                    btnConfirmUpdate();
                }
            }
        }

        function editProduct(id) {
            let _url = "{{ url('withdraw/edit') }}"
            $.ajax({
                url: _url + '/' + id,
                type: 'GET',
                success: function(response) {
                    var item = response.item;

                    $('#withdraw_date').val(item.withdraw_date);
                    $('#prices').val(item.prices);
                    $('#total_prices').val(item.total_prices);
                    $('#comment').val(item.comment);
                    $('#products_name').val(item.products_name);
                    $('#qty').val(item.qty);
                    if (item.status_inventory == 0) {
                        $('#level1').prop('checked', true);
                    } else {
                        $('#level2').prop('checked', true);
                    }
                    $('#btn-submit').attr('onclick', 'btnConfirm(' + id + ')');
                    console.log("edit");
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // handle error
                    console.log(textStatus);
                }
            });
        }

        function clearValue() {
            document.getElementById('soform').reset();
        }

        function createProduct() {
            var _data = $('#soform').serialize(); // assuming that soform is the id of the form
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: "{{ route('withdraw.store') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        data: _data,
                        status: "INS"
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            resolve(response);
                        } else {
                            reject(new Error('Response status is not "success"'));
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        reject(new Error(textStatus));
                    }
                });
            });
        }

        function updateProduct(id) {
            // var _data = $('#soform').serialize(); // assuming that soform is the id of the form
            let _url = "{{ url('withdraw/withdraw-update') }}";
            let _data = {
                'withdraw_date': $('#withdraw_date').val(),
                'prices': $('#prices').val(),
                'total_prices': $('#total_prices').val(),
                'comment': $('#comment').val(),
                'products_name': $('#products_name').val(),
                'qty': $('#qty').val(),
                'status_inventory': $('input[name="status_inventory"]:checked').val(),
            };
            console.log("update");
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: _url + '/' + id,
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        _token: '{{ csrf_token() }}',
                        data: _data,
                        id: id
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status == 'success') {
                            resolve(response);
                        } else {
                            reject(new Error('Response status is not "success"'));
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        reject(new Error(textStatus));
                    }
                });
            });
        }

        function btnConfirm(id) {
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
                preConfirm: id ? function() {
                    return updateProduct(id)
                } : createProduct // If id is provided, call updateProduct, otherwise call createProduct
            }).then((willDelete) => {
                if (willDelete.isConfirmed) {
                    window.location.reload();
                }
            });
        }

        function deleteConfirmation(id) {
            let url = "{{ url('withdraw/withdraw-delete') }}" + '/' + id;
            Swal.fire({
                icon: "warning",
                title: "คุณต้องการลบข้อมูล ใช่ไหม?",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ดำเนินการ!",
                cancelButtonText: "ยกเลิก",
                showLoaderOnConfirm: true,
                stopKeydownPropagation: false,
                preConfirm: () => {
                    return fetch(url)
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
                        title: "ลบข้อมูลเรียบร้อย!",
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            });
        }
    </script>
@endsection
