@extends('layouts.master-layout', ['page_title' => "เปลี่ยนรหัสผ่าน"])
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">เปลี่ยนรหัสผ่าน</li>
                    </ol>
                </div>
                <h4 class="page-title">เปลี่ยนรหัสผ่าน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form name="password-form" method="POST" action="{{ route('profile.change.password') }}" onsubmit="return SubmitForm(this);">
                            {{ csrf_field() }}
                            <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-key me-1"></i>
                                เปลี่ยนรหัสผ่าน</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="firstname" class="form-label">รหัสผ่านปัจจุบัน</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password"
                                                class="form-control @error('current_password') is-invalid @enderror"
                                                id="current_password" name="current_password"
                                                placeholder="Current Password" autocomplete="off"
                                                value="{{ old('current_password') }}" required>
                                            <div class="input-group-text" data-password="false">
                                                <span class="password-eye"></span>
                                            </div>
                                            @error('current_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="lastname" class="form-label">รหัสผ่านใหม่</label>
                                        <span class="text-pink"> 8-12 หลัก ( a-z, A-Z, 0-9 ) อักขระพิเศษ ( @, #, -, _ )</span>
                                        <div class="input-group input-group-merge">
                                            <input type="password"
                                                class="form-control @error('new_password') is-invalid @enderror"
                                                id="new_password" name="new_password" placeholder="New Password"
                                                autocomplete="off" value="{{ old('new_password') }}" required>
                                            <div class="input-group-text" data-password="false">
                                                <span class="password-eye"></span>
                                            </div>
                                            @error('new_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="lastname" class="form-label">ยืนยัน รหัสผ่านใหม่</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password"
                                                class="form-control @error('confirm_new_password') is-invalid @enderror"
                                                id="confirm_new_password" name="confirm_new_password"
                                                placeholder="Confirm New Password" autocomplete="off"
                                                value="{{ old('confirm_new_password') }}" required>
                                            <div class="input-group-text" data-password="false">
                                                <span class="password-eye"></span>
                                            </div>
                                            @error('confirm_new_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <strong class="form-text confirm-message"></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success waves-effect waves-light mt-2"><i
                                        class="mdi mdi-content-save"></i> เปลี่ยนรหัสผ่าน</button>
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
<script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
            var success = "{{ session('success') ? session('success') : '' }}";
            if (success == 'password_changed') {
                Swal.fire({
                    icon: "success",
                    title: "เปลี่ยนรหัสผ่านเรียบร้อย! กรุณาเข้าสู่ระบบด้วยรหัสผ่านใหม่",
                    showConfirmButton: false,
                    timer: 3000,
                }).then(function() {
                    document.getElementById('logout-form').submit();
                });
            }
            $('#new_password, #confirm_new_password').on('keyup', function() {
                $('.confirm-message').text('').removeClass('text-success').removeClass('text-danger');
                let password=$('#new_password').val();
                let confirm_password=$('#confirm_new_password').val();
                if (password != "" && confirm_password != "") {
                    if(confirm_password===password){
                        $('.confirm-message').text('รหัสผ่านตรงกันแล้ว!').addClass('text-success');
                    }else{
                        $('.confirm-message').text("รหัสผ่านไม่ตรงกัน").addClass('text-danger');
                    }
                }
            });
        });
        function SubmitForm(form){
            var error = 0;
            let password=$('#new_password').val();
            if (password.search(/^[a-zA-Z0-9-@#_]+$/) == -1) {
                var msg = "รหัสผ่าน 8-12 หลัก ใช้ได้เฉพาะตัวเลข 0-9 ตัวอักษร a-z, A-Z, และอักขระพิเศษเฉพาะ @ # _ และ (-) ขีด เท่านั้น และห้ามมี 'ค่าว่าง' !";
                Swal.fire({
                    icon: "warning",
                    title: "โปรดโปรดตรวจสอบข้อมูลให้ถูกต้อง",
                    html: '<span class="text-danger">'+msg+'</span>',
                    timer: 3000,
                    showConfirmButton: false,
                });
                error++;
            }
            if (error > 0) {
                return false;
            }
        }
</script>
@endsection