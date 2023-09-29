<!-- bundle -->
<!-- Vendor js -->
<script src="{{asset('assets/js/vendor.min.js')}}"></script>
@yield('script')
<!-- App js -->
<script src="{{asset('assets/js/app.min.js')}}"></script>
<!-- Sweet Alert -->
<script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
<script src="{{asset('assets/js/confirm-delete.js')}}"></script>
@if (Auth::check())
<script src="{{asset('assets/js/notifications.js')}}"></script>
@endif
<script src="{{asset('assets/js/beforeinstallprompt.js')}}"></script>
@yield('script-bottom')