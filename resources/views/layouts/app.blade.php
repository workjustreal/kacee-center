<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8" />
    <title>Kacee Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Kacee Application" name="description" />
    <meta content="Kacee Application" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" type="image/x-icon" href="{{ url('assets/kacee.ico') }}">
    @include('layouts.shared/head-css')
</head>
<body class="loading">
    <div id="wrapper">
        @include('layouts.shared/topbar')
        <div class="content-page">
            <div class="content">
                @yield('content')
            </div>
        </div>
        @include('layouts.shared/footer')
    </div>
    @include('layouts.shared/footer-script')
</body>

</html>
