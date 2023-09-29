<!DOCTYPE html>
<html lang="th">

<head>
    @include('layouts.shared/title-meta', ['title' => $page_title ?? ''])
    @include('layouts.shared/head-css')
</head>

<body class="loading"
    data-layout='{"mode": "{{$theme ?? "light" }}", "width": "fluid", "menuPosition": "fixed", "sidebar": { "color": "{{$theme ?? "red" }}", "size": "default", "showuser": false}, "topbar": {"color": "red"}, "showRightSidebarOnPageLoad": true}'>
    @include('sweetalert::alert')
    <!-- Pre-loader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner">Loading...</div>
        </div>
    </div>
    <!-- End Preloader-->
    <!-- Begin page -->
    <div id="wrapper">
        @include('layouts.shared/topbar')
        @include('layouts.shared/left-menu')

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        @show
        <div @if((new \Jenssegers\Agent\Agent())->isPhone()) class="content-page px-0" @else class="content-page" @endif>
            <div class="content">
                @yield('content')
            </div>
            <!-- content -->
            @include('layouts.shared/footer')
        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->
    @include('layouts.shared/footer-script')
</body>

</html>