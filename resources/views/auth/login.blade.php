<!DOCTYPE html>
<html lang="en">

<head>

    @include('layouts.shared/title-meta', ['title' => "Login"])

    @include('layouts.shared/head-css')

    <style>
        body.authentication-bg {
            background-color: #005ce6;
            background-size: cover;
            background-position: center;
        }
        .card{
            border-radius: 2em;
        }
        .btn-login {
            color: #fff;
            background-color: #005ce6;
            border-color: #005ce6;
        }
        .btn-check:focus + .btn-login,
        .btn-login:focus,
        .btn-login:hover {
            color: #fff;
            background-color: #1a75ff;
            border-color: #0066ff;
        }
        .btn-check:focus + .btn-login,
        .btn-login:focus {
            box-shadow: 0 0 0 0.15rem rgba(0, 92, 230, 0.5);
        }
        .btn-check:active + .btn-login,
        .btn-check:checked + .btn-login,
        .btn-login.active,
        .btn-login:active,
        .show > .btn-login.dropdown-toggle {
            color: #fff;
            background-color: #0066ff;
            border-color: #005ce6;
        }
        .btn-check:active + .btn-login:focus,
        .btn-check:checked + .btn-login:focus,
        .btn-login.active:focus,
        .btn-login:active:focus,
        .show > .btn-login.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.15rem rgba(0, 92, 230, 0.5);
        }
        .btn-login.disabled,
        .btn-login:disabled {
            color: #fff;
            background-color: #005ce6;
            border-color: #005ce6;
        }
    </style>

</head>

<body class="loading authentication-bg authentication-bg-pattern vh-100">

    <div class="account-pages">
        <div class="container">
            <div class="row d-flex justify-content-center align-items-center vh-100">
                <div class="col-md-8 col-lg-6 col-xl-4 py-2">
                    <div class="card bg-pattern my-auto">
                        <div class="card-body p-4">
                            <div class="text-center w-75 m-auto">
                                <div class="auth-logo">
                                    <a href="{{ url('/') }}"><img src="{{URL::asset('assets/images/logo-web-lg.png')}}" alt="logo" class="h40"></a>
                                </div>
                                <p class="text-muted mb-4 mt-3">ยินดีต้อนรับ Kacee Application</p>
                            </div>
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div><br>
                            @endif
                            @if (session('success'))
                                <div class=" alert alert-success">{{ session('success') }}</div><br>
                            @endif

                            @if (sizeof($errors) > 0)
                            <ul>
                                @foreach ($errors->all() as $error)
                                <div class="alert alert-danger">รหัสพนักงาน หรือ รหัสผ่าน ไม่ถูกต้อง</div><br>
                                @endforeach
                            </ul>
                            @endif
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="emailaddress" class="form-label fw-normal">รหัสพนักงาน</label>
                                    <input class="form-control rounded-pill" type="text" name="email" id="email" required="" maxlength="6"
                                        placeholder="ใส่รหัสพนักงาน" inputmode="numeric" pattern="[0-9]*">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-normal">รหัสผ่าน</label>
                                    <div class="input-group input-group-merge">
                                        <input class="form-control rounded-pill rounded-end border-end-0" type="password" name="password" id="password"
                                            placeholder="ใส่รหัสผ่าน">
                                        <div class="input-group-text rounded-pill rounded-start bg-white border-start-0" data-password="false" type="button">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center d-grid mt-5">
                                    <button class="btn btn-login btn-rounded" type="submit"> LOGIN </button>
                                </div>
                                <div class="mt-4 text-center">
                                    <a href="javascript:void(0);" class="text-primary" data-bs-toggle="modal" data-bs-target="#helpModal">
                                        <i class="fe-headphones" title="HELP & SUPPORT"></i>
                                        <span>ช่วยเหลือ</span>
                                    </a><br>
                                    <small class="text-muted">
                                        2022 - <script>
                                            document.write(new Date().getFullYear())
                                        </script> &copy; <a href="{{ url('/') }}" class="text-muted">Kacee Application</a>
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h4 class="modal-title" id="helpModalLabel"><i class="fe-phone-call me-1"></i> เบอร์ติดต่อ</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex align-items-start mb-3 mt-1 border-bottom border-light">
                                <img class="d-flex me-2 rounded-circle" src="{{asset('assets/images/users/user-1.jpg')}}" alt="placeholder image" height="32">
                                <div class="w-100">
                                    <h6 class="font-14">7880 <small class="text-muted">(ปุ๊ก) ผู้จัดการฝ่ายไอทีและโปรแกรมเมอร์</small></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex align-items-start mb-3 mt-1 border-bottom border-light">
                                <img class="d-flex me-2 rounded-circle" src="{{asset('assets/images/users/user-1.jpg')}}" alt="placeholder image" height="32">
                                <div class="w-100">
                                    <h6 class="font-14">7887 <small class="text-muted">(เอ็ม) โปรแกรมเมอร์</small></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex align-items-start mb-3 mt-1 border-bottom border-light">
                                <img class="d-flex me-2 rounded-circle" src="{{asset('assets/images/users/user-1.jpg')}}" alt="placeholder image" height="32">
                                <div class="w-100">
                                    <h6 class="font-14">7885 <small class="text-muted">(เรียว) โปรแกรมเมอร์</small></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex align-items-start mb-3 mt-1 border-bottom border-light">
                                <img class="d-flex me-2 rounded-circle" src="{{asset('assets/images/users/user-1.jpg')}}" alt="placeholder image" height="32">
                                <div class="w-100">
                                    <h6 class="font-14">7886 <small class="text-muted">(มอส) ผู้ดูแลระบบ</small></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.shared/footer-script')

</body>

</html>