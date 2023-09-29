<!DOCTYPE html>
<html lang="th">

<head>
    @include('layouts.shared/title-meta', ['title' => $page_title ?? ''])
    @include('layouts.shared/head-css')
</head>

<body class="loading">
    @include('sweetalert::alert')
    <!-- Begin page -->
    <div id="wrapper">
        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        @show
        <div class="report-page mx-auto" style="max-width: 900px;">
            <div class="content">
                @yield('content')
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->
    @include('layouts.shared/footer-script')
</body>

</html>