<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Optional;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    const admin = 1;
    const user = 0;

    const roleAdmin = 1;
    const roleUser = 2;
    const roleHR = 3;
    const roleSales = 4;
    const roleSecretary = 5;
    const roleIT = 6;

    const isLogin = 1;
    const isFlag = 1;

    protected $fillable = [
        'emp_id',
        'name',
        'surname',
        'image',
        'email',
        'password',
        'is_admin',
        'is_role',
        'is_login',
        'is_flag',
        'dept_id',
        'last_login_at',
        'last_login_ip',
        'last_login_client',
        'last_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime', 'is_admin',
    ];

    public function isAdmin()
    {
        return $this->is_admin === self::admin;
    }

    public function isUser()
    {
        return $this->is_admin === self::user;
    }

    // ###################################################### USER LEVEL ############################################################

    public function isManager()
    {
        $user = auth()->user();
        $emp = self::findEmployee($user->emp_id);
        if ($emp) {
            if ($emp->position_id > 0 && $emp->position_id <= 200) { //ผู้จัดการฝ่าย ขึ้นไป
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    public function isManagerHelper()
    {
        $user = auth()->user();
        $emp = self::findEmployee($user->emp_id);
        if ($emp) {
            if ($emp->position_id > 0 && $emp->position_id <= 201) { //ผู้ช่วยผู้จัดการฝ่าย ขึ้นไป
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    public function isManagerDept()
    {
        $user = auth()->user();
        $emp = self::findEmployee($user->emp_id);
        if ($emp) {
            if ($emp->position_id > 0 && $emp->position_id <= 300) { //ผู้จัดการแผนก ขึ้นไป
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    public function isLeader()
    {
        $user = auth()->user();
        $emp = self::findEmployee($user->emp_id);
        if ($emp) {
            if ($emp->position_id > 0 && $emp->position_id <= 310) { //หัวหน้าแผนก ขึ้นไป
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    public function isNormal()
    {
        $user = auth()->user();
        $emp = self::findEmployee($user->emp_id);
        if ($emp) {
            if ($emp->position_id <= 0 || $emp->position_id > 300) { //หัวหน้าแผนก ลงไป
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    public function isGuest()
    {
        return true;
    }

    // ###################################################### END ############################################################

    // ###################################################### ROLES ############################################################

    public function roleAdmin()
    {
        return $this->is_admin === self::admin || $this->is_role === self::roleAdmin;
    }

    public function roleUser()
    {
        return $this->is_admin === self::admin || $this->is_role === self::roleAdmin || $this->is_role === self::roleUser;
    }

    public function roleHR()
    {
        return $this->is_admin === self::admin || $this->is_role === self::roleAdmin || $this->is_role === self::roleHR;
    }

    public function roleSales()
    {
        return $this->is_admin === self::admin || $this->is_role === self::roleAdmin || $this->is_role === self::roleSales;
    }

    public function roleSecretary()
    {
        return $this->is_admin === self::admin || $this->is_role === self::roleAdmin || $this->is_role === self::roleSecretary;
    }

    public function roleIT()
    {
        return $this->is_admin === self::admin || $this->is_role === self::roleAdmin || $this->is_role === self::roleIT;
    }

    // ###################################################### END ############################################################

    // ###################################################### APP PERMISSION ############################################################

    public function manageMaintenance()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $b = false;
            $_user = auth()->user();

            if ($_user->dept_id == "A03000000") { // พี่จี๊บ  
                $b = true;
            } elseif ($_user->dept_id == 'A03050000' || $_user->dept_id == 'A03060000' || $_user->dept_id == 'A01100000') {
                // ผจก. ฝ่ายอาคารสถานที่ / ฝ่ายบำรุง / ฝ่ายไอทีและโปรแกรมเมอร์
                $b = true;
            } elseif ($_user->dept_id == 'A03050100' || $_user->dept_id == 'A03050200' || $_user->dept_id == 'A03060100' || $_user->dept_id == 'A01100100') {
                // แผนก ไฟฟ้าและวุขา / แผนกยานยนต์ / แผนกซ่อมบำรุง / ฝ่ายไอที
                $b = true;
            } else {
                $b = false;
            }

            return ($b !== false) ? true : false;
        }
    }

    public function showAppTopbar($app_id)
    {
        if ($app_id == 1) {
            return self::appStore();
        } else if ($app_id == 2) {
            return self::appLeave();
        } else if ($app_id == 3) {
            return self::appGenerateBarcode();
        } else if ($app_id == 4) {
            return self::appShipping();
        } else if ($app_id == 5) {
            return self::appShippingCheckout();
        } else if ($app_id == 6) {
            return self::appOrganization();
        } else if ($app_id == 7) {
            return self::appSalesReport();
        } else if ($app_id == 8) {
            return self::appPOS();
        } else if ($app_id == 9) {
            return self::appSalesDocument();
        } else if ($app_id == 10) {
            return self::appAutomotive();
        } else if ($app_id == 11) {
            return self::appRepair();
        } else if ($app_id == 12) {
            return self::appChatbot();
        } else {
            return false;
        }
    }

    public function appShopOnline()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            return true;
        }
    }

    public function appStore()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $app_id = 1;
            return self::callAppPermission($app_id);
        }
    }

    public function appLeave()
    {
        if (self::roleHR()) {
            return true;
        } else {
            $app_id = 2;
            return self::callAppPermission($app_id);
        }
    }

    public function appGenerateBarcode()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $app_id = 3;
            return self::callAppPermission($app_id);
        }
    }

    public function appShipping()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $app_id = 4;
            return self::callAppPermission($app_id);
        }
    }

    public function appShippingCheckout()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $app_id = 5;
            return self::callAppPermission($app_id);
        }
    }

    public function appOrganization()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $app_id = 6;
            return self::callAppPermission($app_id);
        }
    }

    public function appSalesReport()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $app_id = 7;
            return self::callAppPermission($app_id);
        }
    }

    public function appPOS()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $app_id = 8;
            return self::callAppPermission($app_id);
        }
    }

    public function appSalesDocument()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $b = false;
            $_user = auth()->user();
            $_dept_id = substr($_user->dept_id, 0, 3);
            $accounting = array("450198", "550513", "540328");

            if ($_dept_id == "A02") { //เฉพาะส่วนการตลาด
                $b = true;
            }
            if ($_user->dept_id == "A03000000" || $_user->dept_id == "A01000000" || $_user->dept_id == 'A03040000' || $_user->dept_id == 'A01000000' || $_user->dept_id == 'A01070000' || $_user->dept_id == 'A01070100' || ($_user->dept_id == 'A03040200' && self::isLeader())) {
                $b = true;   //เฉพาะแผนกขนส่ง / ผจก. ฝ่ายจัดส่ง / พี่จี๊บ
            }
            if (in_array(auth()->user()->emp_id, $accounting)) {
                $b = true; // บัญชี
            }
            return ($b !== false) ? true : false;
        }
    }
    // ######################## SALES DOCUMENT ##############################
    public function appSalesDocumentMenu1()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $b = false;
            $_user = auth()->user();
            $_dept_id = substr($_user->dept_id, 0, 3);

            if ($_dept_id == "A02") { //เฉพาะส่วนการตลาด
                $b = true;
            }
            return ($b !== false) ? true : false;
        }
    }
    public function appSalesDocumentMenu2()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $b = false;
            $_user = auth()->user();
            $_dept_id = substr($_user->dept_id, 0, 3);

            if ($_dept_id == "A02" && self::isManagerHelper()) { //เฉพาะ ผจก.ส่วนการตลาดขึ้นไป
                $b = true;
            }
            return ($b !== false) ? true : false;
        }
    }
    public function appSalesDocumentMenu3()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $b = false;
            $_user = auth()->user();

            if ($_user->dept_id == "A03000000" || $_user->dept_id == 'A03040000' || ($_user->dept_id == 'A03040200' && self::isLeader())) {
                $b = true;   //เฉพาะหัวหน้าแผนกขนส่ง / ผจก. ฝ่ายจัดส่ง / พี่จี๊บ
            }
            return ($b !== false) ? true : false;
        }
    }
    // ######################## END ##############################

    public function appAutomotive()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $b = false;
            $_user = auth()->user();

            if ($_user->dept_id == "A03000000" || $_user->dept_id == 'A03050000' || ($_user->dept_id == 'A03050200' && self::isLeader())) {
                $b = true;    // พี่จี๊บ / ผจก. ฝ่ายอาคารสถานที่/ เฉพาะหัวหน้าแผนกยานยนต์
            }
            return ($b !== false) ? true : false;
            return false;
        }
    }

    public function appRepair()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            return true;
        }
    }

    public function appChatbot()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $app_id = 12;
            return self::callAppPermission($app_id);
        }
    }

    public function callAppPermission($app_id)
    {
        $user = auth()->user();
        $app = AppPermission::where('app_id', '=', $app_id)->get();
        if ($app->isNotEmpty()) {
            $app = AppPermission::where('app_id', '=', $app_id)->where(function ($query) use ($user) {
                $query->where('dept_id', '=', $user->dept_id)->orWhere('emp_id', '=', $user->emp_id);
            })->get();
            if ($app->isNotEmpty()) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    // ###################################################### END ############################################################

    // ###################################################### FIX PERMISSION ############################################################

    public function manageProductCat()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $user = auth()->user();
            $emp = self::getFixPermissionUser(1, $user->emp_id);
            if ($emp->isNotEmpty()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function manageProductCatOnline()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $user = auth()->user();
            $emp = self::getFixPermissionUser(4, $user->emp_id);
            if ($emp->isNotEmpty()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function manageShipping()
    {
        if (self::roleAdmin()) {
            return true;
        } else {
            $user = auth()->user();
            $emp = self::getFixPermissionUser(2, $user->emp_id);
            if ($emp->isNotEmpty()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function hrReadonly()
    {
        $user = auth()->user();
        $emp = self::getFixPermissionUser(3, $user->emp_id);
        if ($emp->isNotEmpty()) {
            return true;
        } else {
            return false;
        }
    }

    public function printerLabelDeptPer()
    {
        if (self::isAdmin()) {
            return true;
        } else {
            $user = auth()->user();
            if ($user->dept_id == "A01070100" || $user->dept_id == "A02050100") {
                return true;
            }
        }
        return false;
    }

    // ############# บันทึกวันทำงานฝ่ายขาย #############
    public function isAccessLeaveRacord()
    {
        // บันทึกและดู
        $user = auth()->user();
        $emp = self::findEmployee($user->emp_id);
        if (self::roleAdmin() || self::roleHR() || (substr($user->dept_id, 0, 3) == "A02" && in_array($emp->position_id, ['120', '200', '201']))) {
            return true;
        } else {
            return false;
        }
    }

    public function isViewLeaveRacord()
    {
        // ดูอย่างเดียว
        $user = auth()->user();
        if (substr($user->dept_id, 0, 3) == "A02") {
            return true;
        } else {
            return false;
        }
    }
    // ############# END ##############

    // ###################################################### END ############################################################

    // ###################################################### DEPT PERMISSION ############################################################

    public function isDeptSaleOnline()
    {
        // ฝ่ายขายออนไลน์
        $user = auth()->user();
        if (self::roleAdmin() || substr($user->dept_id, 0, 5) == "A0204" || in_array($user->emp_id, ['500043', '620183', '540328', '400010'])) {
            return true;
        } else {
            return false;
        }
    }

    // ###################################################### END ############################################################

    public function manageLeave()
    {
        if (self::roleHR() && !self::hrReadonly()) {
            return true;
        } else {
            $user = auth()->user();
            $app_id = 2;
            $app = AppPermission::where('app_id', '=', $app_id)
                ->where(function ($query) use ($user) {
                    $query->orWhere('emp_id', '=', $user->emp_id);
                    $query->orWhere('dept_id', '=', $user->dept_id);
                })->first();
            if ($app) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function approveLeave()
    {
        if (self::isAdmin()) {
            return true;
        } else {
            $user = auth()->user();
            $app = AuthorizationManual::where('auth', '=', $user->emp_id)->orWhere('auth2', '=', $user->emp_id)->first();
            if ($app) {
                return true;
            } else {
                $app = Authorization::where('auth', '=', $user->emp_id)->orWhere('auth2', '=', $user->emp_id)->first();
                if ($app) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public function approveLeaderLeave()
    {
        if (self::isAdmin()) {
            return true;
        } else {
            $user = auth()->user();
            $app = AuthorizationManual::where('auth', '=', $user->emp_id)->first();
            if ($app) {
                return true;
            } else {
                $app = Authorization::where('auth', '=', $user->emp_id)->first();
                if ($app) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public function approveManagerLeave()
    {
        if (self::isAdmin()) {
            return true;
        } else {
            $user = auth()->user();
            $app = AuthorizationManual::where('auth2', '=', $user->emp_id)->first();
            if ($app) {
                return true;
            } else {
                $app = Authorization::where('auth2', '=', $user->emp_id)->first();
                if ($app) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public function manageEvent()
    {
        if (self::roleHR() || self::roleSales() || self::roleSecretary() || self::roleIT()) {
            return true;
        } else {
            return false;
        }
    }

    public function manageEmployee()
    {
        if (self::roleHR() && !self::hrReadonly()) {
            return true;
        } else {
            return false;
        }
    }

    public function isProfile()
    {
        $user = auth()->user();
        $emp = Employee::where('emp_id', '=', $user->emp_id)->first();
        if ($emp) {
            return true;
        } else {
            return false;
        }
    }

    public function isAppCenter()
    {
        if (auth()->user()->is_login == self::isLogin) {
            return true;
        } else {
            return false;
        }
    }

    public function isAccountVerified()
    {
        if (Auth::isLoggedInByMasterPass()) {
            return true;
        } else {
            if (auth()->user()->is_flag == self::isFlag) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function findEmployee($emp_id)
    {
        $emp = Employee::where('emp_id', '=', $emp_id)->first();
        return $emp;
    }

    public function findDepartment($dept_id)
    {
        $dept = Department::where('dept_id', '=', $dept_id)->first();
        return $dept;
    }

    public function getFixPermissionUser($id, $emp_id)
    {
        $emp = FixPermissionUser::where('permission_id', '=', $id)->where('emp_id', '=', $emp_id)->get();
        return $emp;
    }

    // เฉพาะ ส่วนขายและการตลาด
    public function checkApproveMar()
    {
        $mar = [
            "A02000000",
            "A02010000",
            "A02020000",
            "A01100200",
            "A01100000",
            "A02030000",
            "A02040000",
            "A02050000"
        ];
        $user = auth()->user();
        $emp = Employee::where('emp_id', '=', $user->emp_id)->whereIn('dept_id', $mar)->get();
        if ($emp->isNotEmpty()) {
            return true;
        } else {
            return false;
        }
    }

    // เฉพาะ เลขา
    public function checkHeadSecretary()
    {
        $admin = substr(auth()->user()->dept_id, 0, 5);
        if ($admin == "A0110") {
            return true;
        } else if (auth()->user()->emp_id == "500089") {
            return true;
        } else {
            return false;
        }
    }
    public function checkSecretary()
    {
        $admin = substr(auth()->user()->dept_id, 0, 5);
        if ($admin == "A0110") {
            return true;
        } else if (auth()->user()->emp_id == "500089") {
            return true;
        } else if ($admin == "A0107") {
            return true;
        } else {
            return false;
        }
    }

    // เฉพาะ ฝ่ายขาย
    public function checkMar()
    {
        $mar = substr(auth()->user()->dept_id, 0, 4);
        if ($mar == 'A020') {
            return true;
        } else if ($mar == 'A011') {
            return true;
        } else {
            return false;
        }
    }

    // เฉพาะ เลขา ฝ่ายขาย
    public function checkMarandHeadSec()
    {
        $mar = substr(auth()->user()->dept_id, 0, 4);
        $sec = substr(auth()->user()->dept_id, 0, 5);
        if ($mar == 'A020') {
            return true;
        } else if ($mar == 'A011') {
            return true;
        } else if (auth()->user()->emp_id == "500089") {
            return true;
        } else {
            return false;
        }
    }

    public function checkMarandSec()
    {
        $mar = substr(auth()->user()->dept_id, 0, 4);
        $sec = substr(auth()->user()->dept_id, 0, 5);
        if ($mar == 'A020') {
            return true;
        } else if ($mar == 'A011') {
            return true;
        } else if (auth()->user()->emp_id == "500089") {
            return true;
        } else if ($sec == "A0107") {
            return true;
        } else {
            return false;
        }
    }

    public function adminManager()
    {
        // $admin = ['620274', '630040', '640172', '640195', '660039', '660100'];
        $manager = ['580073', '530107', '510186', '660182'];
        $admin_manager = array_search(auth()->user()->emp_id, $manager);
        if ($admin_manager === false) {
            return false;
        } else {
            return true;
        }
    }

    // notification
    public function noti_CheckApproveMar()
    {
        $mar = [
            "A02000000",
            "A02010000",
            "A02020000",
            "A02030000",
            "A02040000",
            "A02050000"
        ];
        $user = auth()->user();
        $emp = Employee::where('emp_id', '=', $user->emp_id)->whereIn('dept_id', $mar)->get();
        if ($emp->isNotEmpty()) {
            return true;
        } else {
            return false;
        }
    }

    public function noti_CheckHeadSecretary()
    {
        if (auth()->user()->emp_id == "500089") {
            return true;
        } else {
            return false;
        }
    }

    public function noti_CheckSecretary()
    {
        if (auth()->user()->emp_id == "500089") {
            return true;
        } else if (auth()->user()->dept_id == "A01070000") {
            return true;
        } else if (auth()->user()->dept_id == "A01070100") {
            return true;
        } else {
            return false;
        }
    }

    public function noti_CheckMar()
    {
        $mar = substr(auth()->user()->dept_id, 0, 4);
        if ($mar == 'A020') {
            return true;
        } else {
            return false;
        }
    }

    public function accounting()
    {
        $accounting = array("450198", "550513", "540328", "400010");
        if (auth()->user()->dept_id == 'A01100200') {
            return true;
        } elseif (auth()->user()->dept_id == 'A01100000') {
            return true;
        } elseif (in_array(auth()->user()->emp_id, $accounting)) {
            return true;
        } else {
            return false;
        }
    }
}
