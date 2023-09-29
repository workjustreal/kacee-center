<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">

    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul id="side-menu">

                @if (Auth::check())
                    <li class="menu-title">KACEE APPLICATION</li>
                    @if (Auth::user()->isAccountVerified())
                        @php $public_route = ['', 'home', 'events', 'manual']; @endphp
                        @if (in_array(request()->segment(1), $public_route))
                            <li class="@if (request()->segment(1) == 'home' || request()->segment(1) == 'events') menuitem-active @endif">
                                <a href="#sidebarDashboards" data-bs-toggle="collapse">
                                    <i class="fe-airplay"></i>
                                    <span> Dashboards </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse @if (request()->segment(1) == 'home' || request()->segment(1) == 'events') show @endif"
                                    id="sidebarDashboards">
                                    <ul class="nav-second-level">
                                        <li class="@if (request()->segment(1) == 'home') menuitem-active @endif">
                                            <a href="{{ url('/home') }}">Home</a>
                                        </li>
                                        @if (Auth::User()->manageEvent())
                                            <li class="@if (request()->segment(1) == 'events') menuitem-active @endif">
                                                <a href="{{ url('/events') }}">ประกาศบริษัท</a>
                                            </li>
                                        @endif
                                        <li class="@if (request()->segment(1) == 'manual') menuitem-active @endif">
                                            <a href="{{ url('/manual') }}">คู่มือระบบ</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif

                        @if (request()->segment(1) != '' &&
                                request()->segment(1) != 'home' &&
                                request()->segment(1) != 'login' &&
                                request()->segment(1) != 'events' &&
                                request()->segment(1) != 'manual')
                            <li>
                                <a href="{{ url('/home') }}">
                                    <i class="fe-arrow-left"></i>
                                    <span> Back to Home </span>
                                </a>
                            </li>
                        @endif

                        @if (request()->segment(1) != 'admin')
                            <li class="menu-title">APPS</li>
                        @endif
                        @if (Auth::User()->appLeave())
                            @if (in_array(request()->segment(1), array_merge($public_route, ['leave'])))
                                <li class="@if (request()->segment(1) == 'leave') menuitem-active @endif">
                                    <a href="#sidebarLeave" data-bs-toggle="collapse">
                                        <i class="fe-clock"></i>
                                        <span> Leave <span class="leave-noti fs-6"></span></span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse @if (request()->segment(1) == 'leave') show @endif"
                                        id="sidebarLeave">
                                        <ul class="nav-second-level">
                                            <li>
                                                <a href="{{ url('/leave/dashboard') }}"
                                                    title="LEAVE DASHBOARD">แดชบอร์ดลางาน <span
                                                        class="leave-dashboard-noti fs-6"></span></a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/leave/form') }}" title="LEAVE FORM">ลางาน</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/leave/record-working-form') }}"
                                                    title="LEAVE FORM">บันทึกวันทำงาน</a>
                                            </li>
                                            <li class="@if (request()->segment(2) == 'leave-record-form') menuitem-active @endif">
                                                <a href="{{ url('/leave/leave-record') }}"
                                                    title="LEAVE FORM">บันทึกวันทำงานฝ่ายขาย</a>
                                            </li>
                                            @if (Auth::User()->approveLeave())
                                                <li class="@if (request()->segment(1) == 'leave' && request()->segment(2) == 'approve') menuitem-active @endif">
                                                    <a href="#sidebarLeave-1" data-bs-toggle="collapse">
                                                        อนุมัติลางาน <span class="leave-approve-noti fs-6"></span> <span
                                                            class="menu-arrow"></span>
                                                    </a>
                                                    <div class="collapse @if (request()->segment(1) == 'leave' && request()->segment(2) == 'approve') show @endif"
                                                        id="sidebarLeave-1">
                                                        <ul class="nav-second-level">
                                                            <li>
                                                                <a href="{{ url('/leave/approve/dashboard') }}"
                                                                    title="APPROVE">อนุมัติลางาน <span
                                                                        class="leave-approve-noti fs-6"></span></a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('/leave/approve/emp-leave-history') }}"
                                                                    title="HISTORY">ประวัติการลางานของพนักงาน</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('/leave/approve/emp-record-working-history') }}"
                                                                    title="HISTORY">ประวัติบันทึกวันทำงานของพนักงาน</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('/leave/approve/emp-attendance') }}"
                                                                    title="HISTORY">ประวัติการมาทำงานของพนักงาน</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            @endif
                                            @if (Auth::User()->manageLeave())
                                                <li class="@if (request()->segment(1) == 'leave' && request()->segment(2) == 'approve-hr') menuitem-active @endif">
                                                    <a href="#sidebarLeave-2" data-bs-toggle="collapse">
                                                        อนุมัติลางาน (บุคคล) <span
                                                            class="leave-approve-hr-noti fs-6"></span> <span
                                                            class="menu-arrow"></span>
                                                    </a>
                                                    <div class="collapse @if (request()->segment(1) == 'leave' && request()->segment(2) == 'approve-hr') show @endif"
                                                        id="sidebarLeave-2">
                                                        <ul class="nav-second-level">
                                                            <li>
                                                                <a href="{{ url('/leave/approve-hr/dashboard') }}"
                                                                    title="CALENDAR APPROVE EMPLOYEE">ปฏิทิน <span
                                                                        class="leave-approve-hr-noti1 fs-6"></span></a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('/leave/approve-hr/leave-approve') }}"
                                                                    title="LEAVE APPROVE EMPLOYEE">อนุมัติลางาน <span
                                                                        class="leave-approve-hr-noti1 fs-6"></span></a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('/leave/approve-hr/record-working-approve') }}"
                                                                    title="LEAVE APPROVE EMPLOYEE">อนุมัติบันทึกวันทำงาน
                                                                    <span
                                                                        class="leave-approve-hr-noti2 fs-6"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                                <li class="@if (request()->segment(1) == 'leave' && request()->segment(2) == 'manage') menuitem-active @endif">
                                                    <a href="#sidebarLeave-3" data-bs-toggle="collapse">
                                                        จัดการข้อมูล <span class="menu-arrow"></span>
                                                    </a>
                                                    <div class="collapse @if (request()->segment(1) == 'leave' && request()->segment(2) == 'manage') show @endif"
                                                        id="sidebarLeave-3">
                                                        <ul class="nav-second-level">
                                                            <li>
                                                                <a href="{{ url('/leave/manage/emp-leave-history') }}"
                                                                    title="HISTORY">ประวัติการลางานของพนักงาน</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('/leave/manage/emp-record-working-history') }}"
                                                                    title="HISTORY">ประวัติบันทึกวันทำงานของพนักงาน</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('/leave/manage/emp-attendance') }}"
                                                                    title="HISTORY">ประวัติการมาทำงานของพนักงาน</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('/leave/manage/leave-type') }}"
                                                                    title="LEAVE TYPE">ประเภทการลางาน</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('/leave/manage/period-salary') }}"
                                                                    title="PERIOD SALARY">งวดค่าแรงประจำปี</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('/leave/manage/leave-type-property') }}"
                                                                    title="LEAVE TYPE PROPERTY">จำนวนวันหยุดประจำปี</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('/leave/manage/fingerprint') }}"
                                                                    title="FINGERPRINT">ข้อมูลเข้า-ออกงาน</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        @if (Auth::User()->appSalesReport())
                            @if (in_array(request()->segment(1), array_merge($public_route, ['sales-report'])))
                                <li class="@if (request()->segment(1) == 'sales-report') menuitem-active @endif">
                                    <a href="#sidebarSalesReport" data-bs-toggle="collapse">
                                        <i class="fe-pie-chart"></i>
                                        <span> Sales Report </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse @if (request()->segment(1) == 'sales-report') show @endif"
                                        id="sidebarSalesReport">
                                        <ul class="nav-second-level">
                                            <li>
                                                <a href="{{ url('/sales-report/daily-sales') }}"
                                                    title="DAILY SALES">ยอดขายรายวัน</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/sales-report/daily-sales-customer') }}"
                                                    title="DAILY SALES">ยอดขายรายวัน (แยกกลุ่มลูกค้า)</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/sales-report/daily-sales-top10') }}"
                                                    title="DAILY SALES">ยอดขายรายวัน (10 อันดับแรก)</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/sales-report/monthly-sales') }}"
                                                    title="MONTHLY SALES">ยอดขายรายเดือน</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/sales-report/monthly-sales-customer') }}"
                                                    title="MONTHLY SALES">ยอดขายรายเดือน (แยกกลุ่มลูกค้า)</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        @if (Auth::User()->appSalesDocument())
                            @if (in_array(request()->segment(1), array_merge($public_route, ['sales-document'])))
                                <li class="@if (request()->segment(1) == 'sales-document') menuitem-active @endif">
                                    <a href="#sidebarSalesDocument" data-bs-toggle="collapse">
                                        <i class="fe-layers"></i>
                                        <span> Sales Doc.</span>
                                        <span class="sd-m-noti fs-6"></span>
                                        <span class="sd-m2-noti fs-6"></span>
                                        <span class="sd-m3-noti fs-6"></span>
                                        <span class="sd-m4-noti fs-6"></span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse @if (request()->segment(1) == 'sales-document') show @endif"
                                        id="sidebarSalesDocument">
                                        <ul class="nav-second-level">
                                            @if (in_array(request()->segment(1), array_merge($public_route, ['sales-document'])))
                                                <li class="@if (request()->segment(2) == 'sales-form') menuitem-active @endif">
                                                    @if (Auth::User()->appSalesDocumentMenu1() || Auth::User()->appSalesDocumentMenu3())
                                                        <a href="#sidebarsalesForm" data-bs-toggle="collapse">
                                                            ระบบการลงสินค้า
                                                            <span class="sd-noti fs-6"></span>
                                                            <span class="menu-arrow"></span>
                                                        </a>
                                                    @endif
                                                    <div class="collapse @if (request()->segment(2) == 'sales-form') ) show @endif"
                                                        id="sidebarsalesForm">
                                                        <ul class="nav-second-level">
                                                            @if (Auth::User()->appSalesDocumentMenu1())
                                                                <li><a href="{{ url('/sales-document/sales-form') }}"
                                                                        title="SALES FORM">ลงสินค้าส่วนบุคคล</a></li>
                                                            @endif
                                                            @if (Auth::User()->appSalesDocumentMenu2())
                                                                <li><a href="{{ url('/sales-document/sales-approve') }}"
                                                                        title="APPROVE SALES FORM">ลงสินค้า (ฝ่ายขาย)
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @if (Auth::User()->appSalesDocumentMenu3())
                                                                <li>
                                                                    <a href="{{ url('/sales-document/sales-list') }}"
                                                                        title="LIST ALL">
                                                                        ลงสินค้า (ขนส่ง)
                                                                        <span class="sd-noti fs-6"></span>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </li>
                                            @endif
                                            @if (Auth::User()->checkMar() || Auth::User()->checkSecretary() || Auth::User()->accounting())
                                                <li>
                                                    <a href="#sidebarproductdiscount" data-bs-toggle="collapse">
                                                        ส่วนลดงานผิดพลาด
                                                        <span class="pd-disc-noti fs-6"></span>
                                                        <span class="menu-arrow"></span>
                                                    </a>
                                                    <div class="collapse @if (request()->segment(2) == 'discount-mistake') show @endif"
                                                        id="sidebarproductdiscount">
                                                        <ul>
                                                            @if (Auth::User()->checkMar())
                                                                <li>
                                                                    <a
                                                                        href="{{ url('/sales-document/discount-mistake/productdiscount-list_personal') }}">
                                                                        รายการส่วนบุคคล
                                                                        <span class="personal-noti fs-6"></span>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @if (Auth::User()->checkApproveMar())
                                                                <li>
                                                                    <a
                                                                        href="{{ url('/sales-document/discount-mistake/manager-approve/product-discount-repair') }}">
                                                                        อนุมัติคำขอ
                                                                        <span class="mar-app-noti fs-6"></span>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @if (Auth::User()->checkSecretary())
                                                                <li>
                                                                    <a
                                                                        href="{{ url('/sales-document/discount-mistake/secretary-approve/product-discount-repair') }}">
                                                                        อนุมัติคำขอ
                                                                        <span class="sec-app-noti fs-6"></span>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @if (Auth::User()->accounting())
                                                                <li>
                                                                    <a
                                                                        href="{{ url('sales-document/discount-mistake/report') }}">
                                                                        รายงาน
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </li>
                                                @if (Auth::User()->checkMarandHeadSec() || Auth::User()->accounting())
                                                    <li>
                                                        <a href="#sidebarproductdecorate" data-bs-toggle="collapse">
                                                            ขอตกแต่งหน้าร้าน
                                                            <span class="p-dec-noti fs-6"></span>
                                                            <span class="menu-arrow"></span>
                                                        </a>
                                                        <div class="collapse @if (request()->segment(2) == 'product-decorate') show @endif"
                                                            id="sidebarproductdecorate">
                                                            <ul>
                                                                @if (Auth::User()->checkMar())
                                                                    <li>
                                                                        <a
                                                                            href="{{ url('sales-document/product-decorate/list-personal') }}">
                                                                            รายการส่วนบุคคล
                                                                            <span class="p-dec-peernoti fs-6"></span>
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                                @if (Auth::User()->checkApproveMar())
                                                                    <li>
                                                                        <a
                                                                            href="{{ url('sales-document/product-decorate/manager-approve') }}">
                                                                            อนุมัติคำขอ
                                                                            <span class="p-dec-mnnoti fs-6"></span>
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                                @if (Auth::User()->checkSecretary())
                                                                    <li>
                                                                        <a
                                                                            href="{{ url('sales-document/product-decorate/secretary-approve') }}">
                                                                            อนุมัติคำขอ
                                                                            <span class="p-dec-secnoti fs-6"></span>
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                                @if (Auth::User()->accounting())
                                                                    <li>
                                                                        <a
                                                                            href="{{ url('sales-document/product-decorate/report') }}">
                                                                            รายงาน
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    </li>
                                                @endif
                                                <li>
                                                    <a href="#sidebarsepecialdiscount" data-bs-toggle="collapse">
                                                        ส่วนลดงานล็อตใหญ่
                                                        <span class="sp-dis-noti fs-6"></span>
                                                        <span class="menu-arrow"></span>
                                                    </a>
                                                    <div class="collapse @if (request()->segment(2) == 'special-discount') show @endif"
                                                        id="sidebarsepecialdiscount">
                                                        <ul>
                                                            @if (Auth::User()->checkMar())
                                                                <li>
                                                                    <a
                                                                        href="{{ url('sales-document/special-discount/list-personal') }}">รายการส่วนบุคคล
                                                                        <span class="sp-perdis-noti fs-6"></span>
                                                                    </a>

                                                                </li>
                                                            @endif
                                                            @if (Auth::User()->checkApproveMar())
                                                                <li>
                                                                    <a
                                                                        href="{{ url('sales-document/special-discount/manager-approve') }}">
                                                                        อนุมัติคำขอ
                                                                        <span class="sp-mndis-noti fs-6"></span>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @if (Auth::User()->checkSecretary())
                                                                <li>
                                                                    <a
                                                                        href="{{ url('sales-document/special-discount/secretary-approve') }}">
                                                                        อนุมัติคำขอ
                                                                        <span class="sp-secdis-noti fs-6"></span>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @if (Auth::User()->accounting())
                                                                <li>
                                                                    <a
                                                                        href="{{ url('sales-document/special-discount/report') }}">
                                                                        รายงาน
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        @if (in_array(request()->segment(1), array_merge($public_route, ['product'])))
                            <li class="@if (request()->segment(1) == 'product') menuitem-active @endif">
                                <a href="#sidebarProduct" data-bs-toggle="collapse">
                                    <i class="fe-tag"></i>
                                    <span> Product </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse @if (request()->segment(1) == 'product') show @endif"
                                    id="sidebarProduct">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ url('/product/search') }}" title="PRODUCT">ค้นหาสินค้า</a>
                                        </li>
                                        <li>
                                            <a href="{{ url('/product/category-search') }}"
                                                title="PRODUCT GROUP">จัดกลุ่มสินค้า</a>
                                        </li>
                                        @if (Auth::User()->appGenerateBarcode())
                                            {{-- <li>
                                        <a href="{{url('/product/barcode-upload')}}" title="UPLOAD BARCODE" >อัปโหลดไฟล์บาร์โค้ดสินค้า</a>
                                    </li> --}}
                                            <li>
                                                <a href="{{ url('/product/barcode-new') }}"
                                                    title="GENERATE BARCODE">สร้างบาร์โค้ดสินค้า</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/product/barcode-list') }}"
                                                    title="BARCODE DOCUMENT">รายการบาร์โค้ดสินค้าที่สร้าง</a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{ url('/product/request-label') }}"
                                                title="REQUEST LABEL">ร้องขอสติ๊กเกอร์บาร์โค้ด</a>
                                        </li>
                                        <li>
                                            <a href="{{ url('/product/stock') }}" title="STOCK">สต๊อกสินค้า</a>
                                        </li>
                                        <li>
                                            <a href="#sidebarodoo-1" data-bs-toggle="collapse">
                                                Odoo <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse @if (request()->segment(1) == 'leave' && request()->segment(2) == 'approve') show @endif"
                                                id="sidebarodoo-1">
                                                <ul class="nav-second-odoo">
                                                    <li>
                                                        <a href="{{ url('/product/odoo/stocks') }}"
                                                            title="STOCK">สต๊อกสินค้า</a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ url('/product/odoo/none/stocks') }}"
                                                            title="STOCK">สินค้าไม่พบในคลัง</a>
                                                    </li>
                                                    @if (Auth::User()->roleAdmin())
                                                        <li>
                                                            <a href="{{ url('/product/odoo/import/stocks') }}"
                                                                title="UPLOAD FILE">อัพโหลดไฟล์สต๊อก</a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (Auth::User()->appShopOnline())
                            @if (in_array(request()->segment(1), array_merge($public_route, ['orders'])))
                                <li class="@if (request()->segment(1) == 'orders') menuitem-active @endif d-none">
                                    <a href="#sidebarOrders" data-bs-toggle="collapse">
                                        <i class="fas fa-file-download"></i>
                                        <span> Orders </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse @if (request()->segment(1) == 'orders') show @endif"
                                        id="sidebarOrders">
                                        <ul class="nav-second-level">
                                            {{-- <li>
                                            <a href="{{url('/orders/download')}}" title="ORDER NUMBER" >ดาวน์โหลดคำสั่งซื้อ</a>
                                        </li> --}}
                                            <li>
                                                <a href="{{ url('/orders/form') }}"
                                                    title="ORDER NUMBER">ดาวน์โหลดคำสั่งซื้อ</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif
                            @if (in_array(request()->segment(1), array_merge($public_route, ['backend-eshop'])))
                                <li class="@if (request()->segment(1) == 'backend-eshop') menuitem-active @endif d-none">
                                    <a href="#sidebarBackendEShop" data-bs-toggle="collapse">
                                        <i class="fab fa-shopify"></i>
                                        <span> Backend E-Shop </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse @if (request()->segment(1) == 'backend-eshop') show @endif"
                                        id="sidebarBackendEShop">
                                        <ul class="nav-second-level">
                                            {{-- <li>
                                            <a href="{{url('/backend-eshop/image-products')}}" title="IMAGE PRODUCTS" >รูปภาพสินค้า</a>
                                        </li> --}}
                                            <li>
                                                <a href="{{ url('/backend-eshop/download-products') }}"
                                                    title="DOWNLOAD PRODUCTS">ดาวน์โหลดรายการสินค้า</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        @if (Auth::User()->isDeptSaleOnline())
                            @if (in_array(request()->segment(1), array_merge($public_route, ['middleware'])))
                                <li class="@if (request()->segment(1) == 'middleware') menuitem-active @endif">
                                    <a href="#sidebarMiddleware" data-bs-toggle="collapse">
                                        <i class="fe-server"></i>
                                        <span> Middleware </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse @if (request()->segment(1) == 'middleware') show @endif"
                                        id="sidebarMiddleware">
                                        <ul class="nav-second-level">
                                            <li>
                                                <a href="{{ url('/middleware/product-online') }}"
                                                    title="PRODUCT ONLINE">จัดกลุ่มสินค้าออนไลน์</a>
                                            </li>
                                            <li class="@if (request()->segment(2) == 'orders') menuitem-active @endif">
                                                <a href="#sidebarMiddlewareOrders" data-bs-toggle="collapse">
                                                    คำสั่งซื้อออนไลน์ <span class="menu-arrow"></span>
                                                </a>
                                                <div class="collapse @if (request()->segment(2) == 'orders') ) show @endif"
                                                    id="sidebarMiddlewareOrders">
                                                    <ul class="nav-second-level">
                                                        <li>
                                                            <a href="{{ url('/middleware/orders') }}"
                                                                title="ORDER MENAGEMENT">จัดการคำสั่งซื้อ</a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ url('/middleware/orders/report') }}"
                                                                title="ORDER REPORT">รายงานคำสั่งซื้อ</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            <li class="@if (request()->segment(2) == 'financial') menuitem-active @endif">
                                                <a href="#sidebarMiddlewareFinancial" data-bs-toggle="collapse">
                                                    บัญชี <span class="menu-arrow"></span>
                                                </a>
                                                <div class="collapse @if (request()->segment(2) == 'financial') ) show @endif"
                                                    id="sidebarMiddlewareFinancial">
                                                    <ul class="nav-second-level">
                                                        <li>
                                                            <a href="{{ url('/middleware/financial/transaction') }}"
                                                                title="TRANSACTION">รับชำระ</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        @if (Auth::User()->appShipping())
                            @if (in_array(request()->segment(1), array_merge($public_route, ['shipping'])))
                                <li class="@if (request()->segment(1) == 'shipping') menuitem-active @endif">
                                    <a href="#sidebarShipping" data-bs-toggle="collapse">
                                        <i class="fe-printer"></i>
                                        <span> Shipping </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse @if (request()->segment(1) == 'shipping') show @endif"
                                        id="sidebarShipping">
                                        <ul class="nav-second-level">
                                            <li>
                                                <a href="{{ url('/shipping/print') }}"
                                                    title="SHIPPING PRINT">พิมพ์ใบปะหน้าพัสดุ</a>
                                            </li>
                                            <li class="@if (request()->segment(2) == 'search-history') menuitem-active @endif">
                                                <a href="{{ url('/shipping/history') }}"
                                                    title="SHIPPING HISTORY">ประวัติใบปะหน้าพัสดุ</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        @if (Auth::User()->appShippingCheckout())
                            @if (in_array(request()->segment(1), array_merge($public_route, ['checkout'])))
                                <li class="@if (request()->segment(1) == 'checkout') menuitem-active @endif">
                                    <a href="#sidebarCheckout" data-bs-toggle="collapse">
                                        <i class="fe-check-square"></i>
                                        <span> Checkout </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse @if (request()->segment(1) == 'checkout') show @endif"
                                        id="sidebarCheckout">
                                        <ul class="nav-second-level">
                                            <li>
                                                <a href="{{ url('/checkout/shipment') }}"
                                                    title="CHECKOUT SHIPMENT">เช็คเอาท์การจัดส่ง</a>
                                            </li>
                                            <li class="@if (request()->segment(2) == 'search-shipment-history') menuitem-active @endif">
                                                <a href="{{ url('/checkout/shipment-history') }}"
                                                    title="CHECKOUT SHIPMENT HISTORY">ประวัติเช็คเอาท์การจัดส่ง</a>
                                            </li>
                                            @if (Auth::User()->manageShipping())
                                                <li class="@if (request()->segment(2) == 'ship-com-add' || request()->segment(2) == 'ship-com-edit') menuitem-active @endif">
                                                    <a href="{{ url('/checkout/ship-com-manage') }}"
                                                        title="SHIPPING COMPANY MANAGE">จัดการขนส่ง</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        @if (Auth::User()->appOrganization())
                            @if (in_array(request()->segment(1), array_merge($public_route, ['organization', 'holidays'])))
                                <li class="@if (in_array(request()->segment(1), ['organization', 'holidays'])) menuitem-active @endif">
                                    <a href="#sidebarOrganization" data-bs-toggle="collapse">
                                        <i class="fe-users"></i>
                                        <span> Organization </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse @if (in_array(request()->segment(2), [
                                            'employees',
                                            'department',
                                            'position',
                                            'organizational-chart',
                                            'authorization',
                                            'authorization-manual',
                                        ]) || in_array(request()->segment(1), ['holidays'])) show @endif"
                                        id="sidebarOrganization">
                                        <ul class="nav-second-level">
                                            <li class="@if (in_array(request()->segment(3), ['q'])) menuitem-active @endif">
                                                <a href="{{ url('/organization/employees') }}"
                                                    title="EMPLOYEES">ข้อมูลพนักงาน</a>
                                            </li>
                                            @if (Auth::User()->manageEmployee() || Auth::User()->hrReadonly())
                                                <li>
                                                    <a href="{{ url('/organization/department') }}"
                                                        title="DEPARTMENT">ข้อมูลหน่วยงาน</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('/organization/position') }}"
                                                        title="POSITION">ข้อมูลตำแหน่งงาน</a>
                                                </li>
                                            @endif
                                            <li>
                                                <a href="{{ url('/organization/sales-area') }}"
                                                    title="SALES AREA">ข้อมูลพื้นที่การขาย</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/organization/organizational-chart') }}"
                                                    title="Organizational Chart">แผนผังองค์กร</a>
                                            </li>
                                            @if (Auth::User()->manageEmployee())
                                                <li>
                                                    <a href="{{ url('/organization/authorization') }}"
                                                        title="AUTHORIZATION">สิทธิ์การอนุมัติ (ตามหน่วยงาน)</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('/organization/authorization-manual') }}"
                                                        title="AUTHORIZATION">สิทธิ์การอนุมัติ (รายบุคคล)</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('/holidays') }}"
                                                        title="HOLIDAYS">วันหยุดประจำปี</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        @if (in_array(request()->segment(1), array_merge($public_route, ['store'])))
                            <li class="@if (request()->segment(1) == 'checkstock') menuitem-active @endif">
                                <a href="#sidebarSheckstock" data-bs-toggle="collapse">
                                    <i class="fe-database"></i>
                                    <span> Checkstock </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse @if (request()->segment(1) == 'checkstock') show @endif"
                                    id="sidebarSheckstock">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ url('/store/checkstock') }}"
                                                title="CHECKSTOCK">เช็คสต๊อกสินค้า</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (Auth::User()->appRepair())
                            @if (in_array(request()->segment(1), array_merge($public_route, ['repair'])))
                                <li class="@if (request()->segment(1) == 'repair') menuitem-active @endif">
                                    <a href="#sidebarRepair" data-bs-toggle="collapse">
                                        <i class="fe-alert-octagon"></i>
                                        <span> Maintenance</span>
                                        <span class="repair-all-noti fs-6"></span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse @if (request()->segment(1) == 'repair') show @endif"
                                        id="sidebarRepair">
                                        <ul class="nav-second-level">
                                            <li>
                                                <a href="{{ url('/repair/repair') }}" title="REPAIR">รายการส่วนบุคคล
                                                    <span class="repair_success_noti"></span></a>
                                            </li>
                                            <li>
                                                @if (Auth::User()->roleAdmin() || Auth::User()->isManagerHelper())
                                                    <a href="{{ url('/repair/approve') }}"
                                                        title="APPROVE">อนุมัติรายการ
                                                        <span class="repair_wait_noti"></span></a>
                                                @endif
                                            </li>
                                            <li>
                                                @if (Auth::User()->manageMaintenance())
                                                    <a href="{{ url('/repair/action') }}"
                                                        title="ACTION">จัดการงานซ่อม
                                                        <span class="repair-action-noti"></span>
                                                    </a>
                                                @endif
                                            </li>
                                            <li class="@if (request()->segment(2) == 'repair-action') menuitem-active @endif">
                                                @if (Auth::User()->manageMaintenance())
                                                    <a href="#sidebarRepair-2" data-bs-toggle="collapse">รายงานสรุป
                                                        <span class="menu-arrow"></span>
                                                    </a>

                                                    <div class="collapse @if (request()->segment(2) == 'repair-action') ) show @endif"
                                                        id="sidebarRepair-2">
                                                        <ul class="nav-second-level">
                                                            <li>
                                                                <a href="{{ url('/repair/dashboard/dept') }}"
                                                                    title="DASHBOARDONLY">สรุปงานซ่อมรายแผนก</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('/repair/dashboard/all') }}"
                                                                    title="DASHBOARDALL">สรุปงานซ่อมภาพรวม</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('/repair/withdraw') }}"
                                                                    title="ใบเบิกอุปกรณ์">รายงานใบเบิกอุปกรณ์</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        @if (Auth::User()->appAutomotive())
                            @if (in_array(request()->segment(1), array_merge($public_route, ['automotive'])))
                                <li class="@if (request()->segment(1) == 'automotive') menuitem-active @endif">
                                    <a href="#sidebarAutomotive" data-bs-toggle="collapse">
                                        <i class="fe-truck"></i>
                                        <span>Automotive</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse @if (request()->segment(1) == 'automotive') show @endif"
                                        id="sidebarAutomotive">
                                        <ul class="nav-second-level">
                                            <li>
                                                <a href="{{ url('/automotive/automotive') }}"
                                                    title="AUTOMOTIVE">ข้อมูลรถ</span></a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/automotive/main') }}"
                                                    title="ADD TYPE">เพิ่มประเภท</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif

                        @if (Auth::User()->roleAdmin())
                            <li class="menu-title">ADMINISTRATOR</li>
                            <li class="@if (request()->segment(1) == 'admin') menuitem-active @endif">
                                <a href="#sidebarAdmin" data-bs-toggle="collapse">
                                    <i class="fe-settings"></i>
                                    <span> System Management </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse @if (request()->segment(1) == 'admin') show @endif"
                                    id="sidebarAdmin">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ url('/admin/dashboard') }}" title="ADMIN DASHBOARD">Admin
                                                Dashboard</a>
                                        </li>
                                        <li class="@if (request()->segment(2) == 'application') menuitem-active @endif">
                                            <a href="#sidebarAdminApp" data-bs-toggle="collapse">
                                                ระบบงาน <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse @if (request()->segment(2) == 'application') ) show @endif"
                                                id="sidebarAdminApp">
                                                <ul class="nav-second-level">
                                                    <li>
                                                        <a href="{{ url('/admin/application') }}"
                                                            title="APPLICATION LIST">จัดการระบบงาน </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ url('/admin/application/permission') }}"
                                                            title="APPLICATION PERMISSION">จัดการสิทธิ์ระบบงาน </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="@if (request()->segment(2) == 'user-manage' ||
                                                request()->segment(2) == 'register' ||
                                                request()->segment(2) == 'user-edit') menuitem-active @endif">
                                            <a href="#sidebarAdminUser" data-bs-toggle="collapse">
                                                ผู้ใช้งาน <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse @if (request()->segment(2) == 'user-manage' ||
                                                    request()->segment(2) == 'register' ||
                                                    request()->segment(2) == 'user-edit') show @endif"
                                                id="sidebarAdminUser">
                                                <ul class="nav-second-level">
                                                    <li>
                                                        <a href="{{ url('/admin/user-manage') }}"
                                                            title="USER MANAGE">จัดการผู้ใช้งาน </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ url('/admin/register') }}"
                                                            title="USER ADD">เพิ่มผู้ใช้งาน </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="@if (request()->segment(2) == 'roles' || request()->segment(2) == 'permissions') menuitem-active @endif">
                                            <a href="#sidebarAdminPermission" data-bs-toggle="collapse">
                                                บทบาทและสิทธิ์การใช้งาน <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse @if (request()->segment(2) == 'roles' || request()->segment(2) == 'permissions') show @endif"
                                                id="sidebarAdminPermission">
                                                <ul class="nav-second-level">
                                                    <li>
                                                        <a href="{{ url('/admin/roles') }}" title="ROLES">บทบาท </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ url('/admin/permissions') }}"
                                                            title="PERMISSIONS">สิทธิ์การใช้งาน </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="@if (request()->segment(2) == 'fix-permissions') menuitem-active @endif">
                                            <a href="#sidebarAdminFixPermission" data-bs-toggle="collapse">
                                                สิทธิ์การใช้งานเฉพาะทาง <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse @if (request()->segment(2) == 'fix-permissions') show @endif"
                                                id="sidebarAdminFixPermission">
                                                <ul class="nav-second-level">
                                                    <li>
                                                        <a href="{{ url('/admin/fix-permissions') }}"
                                                            title="FIX PERMISSIONS">สิทธิ์การใช้งาน </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="@if (request()->segment(2) == 'notifications') menuitem-active @endif">
                                            <a href="#sidebarAdminNotification" data-bs-toggle="collapse">
                                                การแจ้งเตือน <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse @if (request()->segment(2) == 'notifications') show @endif"
                                                id="sidebarAdminNotification">
                                                <ul class="nav-second-level">
                                                    <li>
                                                        <a href="{{ url('/admin/notifications') }}"
                                                            title="NOTIFICATION">จัดการการแจ้งเตือน </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="@if (request()->segment(2) == 'eplatform') menuitem-active @endif">
                                            <a href="#sidebarAdminEPlatform" data-bs-toggle="collapse">
                                                E-Commerce <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse @if (request()->segment(2) == 'eplatform') show @endif"
                                                id="sidebarAdminEPlatform">
                                                <ul class="nav-second-level">
                                                    <li>
                                                        <a href="{{ url('/admin/eplatform/list') }}"
                                                            title="E-PLATFORM LIST">E-Platform List </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ url('/admin/eplatform/shop') }}"
                                                            title="E-SHOP LIST">E-Shop List </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="@if (request()->segment(2) == 'token') menuitem-active @endif">
                                            <a href="#sidebarAdminToken" data-bs-toggle="collapse">
                                                Token <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse @if (request()->segment(2) == 'token') show @endif"
                                                id="sidebarAdminToken">
                                                <ul class="nav-second-level">
                                                    <li>
                                                        <a href="{{ url('/admin/token/lazada') }}"
                                                            title="LAZADA TOKEN">Lazada </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ url('/admin/token/shopee') }}"
                                                            title="SHOPEE TOKEN">Shopee </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ url('/admin/token/nocnoc') }}"
                                                            title="NOCNOC TOKEN">NocNoc </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ url('/admin/token/tiktok') }}"
                                                            title="TOKTOK TOKEN">TikTok </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif
                    @endif
                @else
                    <li>
                        <a href="{{ route('login') }}">
                            <i class="fe-log-in"></i>
                            <span> เข้าสู่ระบบ </span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <!-- End Sidebar -->
        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -left -->
</div>
<!-- Left Sidebar End -->
