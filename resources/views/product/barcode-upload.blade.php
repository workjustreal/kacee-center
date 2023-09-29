@extends('layouts.master-layout', ['page_title' => "อัปโหลดไฟล์บาร์โค้ด"])
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
                        <li class="breadcrumb-item active">บาร์โค้ดสินค้า</li>
                    </ol>
                </div>
                <h4 class="page-title">อัปโหลดไฟล์บาร์โค้ดสินค้า</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" id="upload-form" action="{{ route('barcode.upload-print') }}"
                            method="POST" enctype="multipart/form-data" target="_blank"
                            onsubmit="return SubmitForm(this);">
                            {{ csrf_field() }}
                            <div class="mb-3">
                                <div class="fallback">
                                    <label for="file" class="form-label">ไฟล์บาร์โค้ด (.txt) </label><br>
                                    <input id="file" name="file" class="form-control" type="file" title="Upload File"
                                        accept=".txt" />
                                    {!! $errors->first('file', '<span class="text-danger">:message</span>') !!}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="detail" class="form-label">Ex. Format</label>
                                <table width="300">
                                    <tr>
                                        <th>BARCODE</th>
                                        <th>SKU</th>
                                        <th>QTY</th>
                                    </tr>
                                    <tr>
                                        <td>3180000364046</td>
                                        <td>ZBY9A041GRF</td>
                                        <td>3</td>
                                    </tr>
                                    <tr>
                                        <td>3180000364057</td>
                                        <td>ZBY8C010CRB</td>
                                        <td>2</td>
                                    </tr>
                                    <tr>
                                        <td>3180000364068</td>
                                        <td>ZBY8C010WHB</td>
                                        <td>2</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="mb-3">
                                <button type="submit" name="submit" class="btn btn-primary mt-3" title="UPLOAD">
                                    อัปโหลด</button>
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
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    function SubmitForm(form){
        if (document.getElementById('file').value == "") {
            Swal.fire({
                icon: "warning",
                title: "ยังไม่ได้เลือกไฟล์",
                showConfirmButton: false,
                timer: 2000,
            });
            return false;
        }
    }
</script>
@endsection