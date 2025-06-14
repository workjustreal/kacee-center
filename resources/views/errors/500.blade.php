<!DOCTYPE html>
<html lang="en">

<head>

    @include('layouts.shared/title-meta', ['title' => "Error Page | 500 | Internal Server Error"])

    @include('layouts.shared/head-css')

</head>

<body class="loading authentication-bg authentication-bg-pattern">

    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-4">
                    <div class="card bg-pattern">

                        <div class="card-body p-4">

                            <div class="auth-logo">
                                <a href="{{route('home')}}" class="logo logo-light text-center">
                                    <span class="logo-lg">
                                        <img src="{{asset('assets/images/logo-web.png')}}" alt="" height="22">
                                    </span>
                                </a>
                            </div>

                            <div class="text-center mt-4">
                                <h1 class="text-error">500</h1>
                                <h3 class="mt-3 mb-2">Internal Server Error</h3>
                                @if ($errors->any())
                                    @foreach ($errors->all() as $error)
                                        <p class="text-danger">{{ $error }}</p>
                                    @endforeach
                                @endif
                                <h6 class="font-14 text-dark">ติดต่อ</h6>
                                <h6 class="font-14">7880 <small class="text-muted">(ปุ๊ก) ผู้จัดการฝ่ายไอทีและโปรแกรมเมอร์</small></h6>
                                <h6 class="font-14">7887 <small class="text-muted">(เอ็ม) โปรแกรมเมอร์</small></h6>
                                <h6 class="font-14">7885 <small class="text-muted">(เรียว) โปรแกรมเมอร์</small></h6>
                                <h6 class="font-14 mb-3">7886 <small class="text-muted">(มอส) ผู้ดูแลระบบ</small></h6>

                                <a href="{{route('home')}}" class="btn btn-success waves-effect waves-light">Back to Home</a>
                            </div>

                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->


    <footer class="footer footer-alt">
        2022 - <script>
            document.write(new Date().getFullYear())
        </script> &copy; Kacee Application <a href="{{ route('home') }}" class="text-white-50">Home</a>
    </footer>

    @include('layouts.shared/footer-script')

</body>

</html>