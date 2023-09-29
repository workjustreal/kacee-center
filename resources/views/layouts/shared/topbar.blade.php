<div class="navbar-custom bg-gradient">
    <div class="container-fluid">
        <ul class="list-unstyled topnav-menu float-end mb-0">
            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" href="javascript:void(0)"
                    id="btnInstallApp" style="display: none">
                    <i class="fe-download noti-icon"></i>
                </a>
            </li>
            {{-- <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="fullscreen" href="#">
                    <i class="fe-maximize noti-icon"></i>
                </a>
            </li> --}}
            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" href="#">
                    <i class="fe-alert-circle noti-icon"></i> Development
                </a>
            </li>
            @if (auth()->check())
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light"
                        href="{{ url('/chatbot') }}">
                        <i class="fe-message-square noti-icon"></i>
                    </a>
                </li>
                <li class="dropdown topbar-dropdown">
                    <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="fe-grid noti-icon"></i>
                    </a>
                    <div class="dropdown-menu dropdown-lg dropdown-menu-end">
                        <div class="p-lg-1">
                            <div class="row g-0">
                                @foreach ($app_list as $app)
                                    <div class="col-4">
                                        <a class="dropdown-icon-item" href="{{ $app['url'] }}"
                                            title="{{ $app['name'] }}">
                                            <i class="{{ $app['icon'] }} fs-2" style="color: {{ $app['color'] }};"></i>
                                            <span>{{ $app['name'] }}</span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </li>
                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="fe-bell noti-icon"></i>
                        <span class="badge rounded-circle noti-icon-badge fs-6"
                            style="display: none;font-family: 'Cerebri Sans,sans-serif';background-color:#b9f55a;color:#000;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-lg">

                        <!-- item-->
                        <div class="dropdown-item noti-title">
                            <h5 class="m-0">
                                <span class="float-end">
                                    <a href="" class="text-dark">
                                        <small class="noti-count">Clear All</small>
                                    </a>
                                </span>แจ้งเตือน
                            </h5>
                        </div>

                        <div class="noti-topbar"></div>

                        <!-- All-->
                        {{-- <a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item notify-all">
                        View all
                        <i class="fe-arrow-right"></i>
                    </a> --}}

                    </div>
                </li>
            @endif
            <li class="dropdown notification-list topbar-dropdown">
                <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown"
                    href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    @if (auth()->check())
                        <img src="{{ url('assets/images/users/' . auth()->user()->image) }}"
                            onerror="this.onerror=null;this.src='{{ url('assets/images/users/thumbnail/user-1.jpg') }}'"
                            alt="user-image" class="rounded-circle">
                        <span class="pro-user-name ms-1">
                            {{ auth()->user()->name . ' ' . auth()->user()->surname }} <i
                                class="mdi mdi-chevron-down"></i>
                        </span>
                    @else
                        <img src="{{ url('assets/images/users/user-1.jpg') }}" alt="user-image" class="rounded-circle">
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                    @if (auth()->check())
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome !</h6>
                        </div>
                        @if (auth()->user()->isProfile())
                            <a href="{{ route('profile') }}" class="dropdown-item notify-item">
                                <i class="fe-user" title="PROFILE"></i>
                                <span>โปรไฟล์</span>
                            </a>
                        @else
                            <a href="{{ route('account.change.password') }}" class="dropdown-item notify-item">
                                <i class="fe-lock" title="CHANGE PASSWORD"></i>
                                <span>เปลี่ยนรหัสผ่าน</span>
                            </a>
                        @endif
                    @endif
                    <a href="javascript:void(0);" class="dropdown-item notify-item" data-bs-toggle="modal"
                        data-bs-target="#helpModal">
                        <i class="fe-headphones" title="HELP & SUPPORT"></i>
                        <span>ช่วยเหลือ</span>
                    </a>
                    @if (auth()->check())
                        <div class="dropdown-divider"></div>
                        <a href=""
                            onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                            class="dropdown-item notify-item">
                            <i class="fe-log-out" title="LOGOUT"></i>
                            <span>ออกจากระบบ</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="dropdown-item notify-item">
                            <i class="fe-log-in" title="LOGIN"></i>
                            <span>เข้าสู่ระบบ</span>
                        </a>
                    @endif
                </div>
            </li>

        </ul>

        <div class="logo-box">
            <a href="{{ route('home') }}" class="logo logo-light text-center">
                <span class="logo-sm">
                    <img src="{{ URL::asset('assets/images/logo-sm.png') }}" alt="" id="logo"
                        class="h24">
                </span>
                <span class="logo-lg">
                    <img src="{{ URL::asset('assets/images/logo-web.png') }}" alt="" id="logo"
                        class="h40">
                </span>
            </a>
        </div>

        <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
            <li>
                <button class="button-menu-mobile waves-effect waves-light">
                    <i class="fe-menu"></i>
                </button>
            </li>

            <li>
                <a class="navbar-toggle nav-link" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <div class="lines">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
</div>
<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel"
    aria-hidden="true">
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
                            <img class="d-flex me-2 rounded-circle"
                                src="{{ asset('assets/images/users/user-1.jpg') }}" alt="placeholder image"
                                height="32">
                            <div class="w-100">
                                <h6 class="font-14">7880 <small class="text-muted">(ปุ๊ก)
                                        ผู้จัดการฝ่ายไอทีและโปรแกรมเมอร์</small></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex align-items-start mb-3 mt-1 border-bottom border-light">
                            <img class="d-flex me-2 rounded-circle"
                                src="{{ asset('assets/images/users/user-1.jpg') }}" alt="placeholder image"
                                height="32">
                            <div class="w-100">
                                <h6 class="font-14">7887 <small class="text-muted">(เอ็ม) โปรแกรมเมอร์</small></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex align-items-start mb-3 mt-1 border-bottom border-light">
                            <img class="d-flex me-2 rounded-circle"
                                src="{{ asset('assets/images/users/user-1.jpg') }}" alt="placeholder image"
                                height="32">
                            <div class="w-100">
                                <h6 class="font-14">7885 <small class="text-muted">(เรียว) โปรแกรมเมอร์</small></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex align-items-start mb-3 mt-1 border-bottom border-light">
                            <img class="d-flex me-2 rounded-circle"
                                src="{{ asset('assets/images/users/user-1.jpg') }}" alt="placeholder image"
                                height="32">
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
