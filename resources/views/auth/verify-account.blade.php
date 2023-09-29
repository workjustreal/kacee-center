@extends('layouts.master-layout', ['page_title' => 'ยืนยันตัวตน'])
@section('content')
    <div class="container">
        <div class="account-pages mt-5 mb-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-6">
                        <div class="card bg-pattern">
                            <div class="card-body p-4">
                                <div class="text-center w-75 m-auto">
                                    <div class="auth-logo">
                                        <img src="{{ URL::asset('assets/images/logo-web.png') }}" alt="logo" class="h40">
                                    </div>
                                    <p class="text-muted mb-4 mt-3">ยินดีต้อนรับ KACEE Application</p>
                                    <i class="mdi mdi-account-check mdi-48px text-danger"></i>
                                    <h3 class="text-dark mb-4 mt-1">ยืนยันตัวตน</h3>
                                </div>
                                <form name="verified-form" autocomplete="off" method="POST"
                                    action="{{ route('account.verified') }}" onsubmit="return SubmitForm(this);">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="personal_id" class="form-label">เลขบัตรประจำตัวประชาชน</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="password"
                                                    class="form-control @if (session('error_pid')) is-invalid @endif"
                                                    id="personal_id" name="personal_id"
                                                    placeholder="Personal ID" autocomplete="off" onfocus="removeError('personal_id')" onkeypress="return onlyNumberKey(event)"
                                                    value="{{ old('personal_id') }}" minlength="13" maxlength="13" required>
                                                    <div class="input-group-text" data-password="false">
                                                        <span class="password-eye"></span>
                                                    </div>
                                                    @if (session('error_pid'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ session('error_pid') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="new_password" class="form-label">รหัสผ่านใหม่</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="password"
                                                        class="form-control @error('new_password') is-invalid @enderror"
                                                        id="new_password" name="new_password" placeholder="New Password"
                                                        autocomplete="off" value="{{ old('new_password') }}" onfocus="removeError('new_password')" required>
                                                    <div class="input-group-text" data-password="false">
                                                        <span class="password-eye"></span>
                                                    </div>
                                                    @error('new_password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <i id="8char" class="mdi mdi-close-thick" style="color:#FF0004;"></i>
                                                รหัสผ่าน 8-20 หลัก<br>
                                                <i id="ucase" class="mdi mdi-close-thick" style="color:#FF0004;"></i>
                                                ตัวอักษรพิมพ์ใหญ่<br>
                                                <i id="lcase" class="mdi mdi-close-thick" style="color:#FF0004;"></i>
                                                ตัวอักษรพิมพ์เล็ก<br>
                                                <i id="num" class="mdi mdi-close-thick" style="color:#FF0004;"></i>
                                                ตัวเลข
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="confirm_new_password" class="form-label">ยืนยัน รหัสผ่านใหม่</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="password"
                                                        class="form-control @error('confirm_new_password') is-invalid @enderror"
                                                        id="confirm_new_password" name="confirm_new_password"
                                                        placeholder="Confirm New Password" autocomplete="off"
                                                        value="{{ old('confirm_new_password') }}" onfocus="removeError('new_password')" required>
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
                                                <i id="pwmatch" class="mdi mdi-close-thick" style="color:#FF0004;"></i>
                                                รหัสผ่านตรงกัน
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <small class="text-pink">
                                                * รหัสผ่าน 8-20 หลัก ( a-z, A-Z, 0-9 ) อักขระพิเศษ ( @, #, -, _ )<br>
                                                * ต้องมีตัวอักษรพิมพ์ใหญ่ อย่างน้อย 1 ตัวอักษร<br>
                                                * ต้องมีตัวอักษรพิมพ์เล็ก อย่างน้อย 1 ตัวอักษร<br>
                                                * ต้องมีตัวเลข อย่างน้อย 1 ตัว
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-center d-grid mt-5">
                                        <button class="btn btn-danger" type="submit"> <i class="mdi mdi-account-check"></i>
                                            ยืนยันตัวตน </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/ajax/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/password-validation.js') }}"></script>
    <script type="text/javascript">
        function onlyNumberKey(evt) {
            // Only ASCII character in that range allowed
            var ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
                return false;
            return true;
        }
        function SubmitForm(form) {
            var error = 0;
            let current_password = $('#current_password').val();
            let password = $('#new_password').val();
            if (current_password.length <= 0) {
                Swal.fire({
                    icon: "warning",
                    title: "โปรดโปรดตรวจสอบข้อมูลให้ถูกต้อง",
                    html: '<span class="text-danger">กรุณาระบุรหัสผ่านปัจจุบัน</span>',
                    timer: 3000,
                    showConfirmButton: false,
                });
                error++;
            } else {
                if (validatePassword() === false) {
                    var msg =
                        '<span class="text-danger">รหัสผ่าน 8-20 หลัก ใช้ได้เฉพาะตัวเลข 0-9 ตัวอักษร a-z, A-Z, และอักขระพิเศษเฉพาะ @ # _ และ (-) ขีด เท่านั้น และห้ามมี "ค่าว่าง" !</span>';
                    var msg2 = '<br><small class="text-pink">\
                                        * รหัสผ่าน 8-20 หลัก ( a-z, A-Z, 0-9 ) อักขระพิเศษ ( @, #, -, _ )<br>\
                                        * ต้องมีตัวอักษรพิมพ์ใหญ่ อย่างน้อย 1 ตัวอักษร<br>\
                                        * ต้องมีตัวอักษรพิมพ์เล็ก อย่างน้อย 1 ตัวอักษร<br>\
                                        * ต้องมีตัวเลข อย่างน้อย 1 ตัว\
                                    </small>';
                    Swal.fire({
                        icon: "warning",
                        title: "โปรดโปรดตรวจสอบข้อมูลให้ถูกต้อง",
                        html: msg + msg2,
                        showConfirmButton: true,
                    });
                    error++;
                }
            }
            if (error > 0) {
                return false;
            }
        }
    </script>
@endsection
