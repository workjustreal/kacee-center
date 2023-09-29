<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user_total = User::where('is_login', '=', 1)->orderBy('last_active', 'DESC')->get();
        $user_active = User::leftjoin('department as d', 'users.dept_id', '=', 'd.dept_id')->where('users.is_login', '=', 1)->whereRaw('(UNIX_TIMESTAMP(date_sub(users.last_active,interval -2 hour)) >= UNIX_TIMESTAMP(now()))')->orderBy('users.last_active', 'DESC')->get(['users.*', 'd.dept_name']);
        $user_nonactive = User::where('is_login', '=', 1)->whereRaw('(UNIX_TIMESTAMP(date_sub(last_active,interval -2 hour)) < UNIX_TIMESTAMP(now()) or last_active is null)')->orderBy('last_active', 'DESC')->get();

        $usersActive = self::convert_to_k($user_active->count());
        $usersNonActive = self::convert_to_k($user_nonactive->count());
        $usersActivePercentile = self::convert_to_k(number_format(($user_active->count() / $user_total->count()) * 100, 2));
        $usersTotal = self::convert_to_k($user_total->count());

        $client_device = self::get_device_online();
        $client_os = self::get_os_online();
        $client_browser = self::get_browser_online();

        return view('admin.admin-dashboard', compact('usersActive', 'usersNonActive', 'usersActivePercentile', 'usersTotal', 'client_device', 'client_os', 'client_browser'));
    }

    public function users_active(Request $request)
    {
        if ($request->ajax()) {
            $data = User::leftjoin('department as d', 'users.dept_id', '=', 'd.dept_id')->where('users.is_login', '=', 1)->whereRaw('(UNIX_TIMESTAMP(date_sub(users.last_active,interval -2 hour)) >= UNIX_TIMESTAMP(now()))');

            $totalRecords = $data->count();
            $records = $data->orderBy('users.last_active', 'DESC')->get(['users.*', 'd.dept_name']);
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $rows[] = array(
                    "emp_id" => '<b>'.$rec->emp_id.'</b>',
                    "name" => '<div class="table-user"><img src="'.url('assets/images/users/thumbnail/'.$rec->image).'" onerror="this.onerror=null;this.src=\''.url('assets/images/users/thumbnail/user-1.jpg').'\';" alt="table-user" class="me-1 rounded-circle">' . $rec->name . ' ' . $rec->surname . '</div>',
                    "dept" => $rec->dept_name,
                    "device" => self::get_device_content($rec->last_login_client, true),
                    "os" => self::get_os_content($rec->last_login_client, true),
                    "browser" => self::get_browser_content($rec->last_login_client, true),
                    "ip_address" => $rec->last_login_ip,
                    "active" => $rec->last_active,
                );
                $n++;
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function convert_to_k($value)
    {
        if ($value >= 1000) {
            return (object)["value"=>number_format($value, 0), "convert"=>round($value/1000, 1), "unit"=>"k"];
        } else {
            return (object)["value"=>$value, "convert"=>$value, "unit"=>""];
        }
    }

    public function get_device_online()
    {
        $isDes = 0;
        $isTab = 0;
        $isMob = 0;
        $user_active = User::where('is_login', '=', 1)->whereRaw('(UNIX_TIMESTAMP(date_sub(last_active,interval -2 hour)) >= UNIX_TIMESTAMP(now()))')->orderBy('last_active', 'DESC')->get();
        foreach ($user_active as $list) {
            $isMob += is_numeric(strpos(strtolower(self::get_device_content($list->last_login_client)), "mobile"));
            $isTab += is_numeric(strpos(strtolower(self::get_device_content($list->last_login_client)), "tablet"));
            $isDes += !is_numeric(strpos(strtolower(self::get_device_content($list->last_login_client)), "mobile")) && !is_numeric(strpos(strtolower(self::get_device_content($list->last_login_client)), "tablet"));
        }
        return (object)["desktop"=>$isDes, "tablet"=>$isTab, "mobile"=>$isMob];
    }

    public function get_os_online()
    {
        $isWin = 0;
        $isAndroid = 0;
        $isIos = 0;
        $user_active = User::where('is_login', '=', 1)->whereRaw('(UNIX_TIMESTAMP(date_sub(last_active,interval -2 hour)) >= UNIX_TIMESTAMP(now()))')->orderBy('last_active', 'DESC')->get();
        foreach ($user_active as $list) {
            $isWin += is_numeric(strpos(strtolower(self::get_os_content($list->last_login_client)), "windows"));
            $isAndroid += is_numeric(strpos(strtolower(self::get_os_content($list->last_login_client)), "android"));
            $isIos += is_numeric(strpos(strtolower(self::get_os_content($list->last_login_client)), "iphone"));
            $isIos += is_numeric(strpos(strtolower(self::get_os_content($list->last_login_client)), "ipad"));
        }
        return (object)["windows"=>$isWin, "android"=>$isAndroid, "ios"=>$isIos];
    }

    public function get_browser_online()
    {
        $isChrome = 0;
        $isEdge = 0;
        $isSafari = 0;
        $isFirefox = 0;
        $isOther = 0;
        $user_active = User::where('is_login', '=', 1)->whereRaw('(UNIX_TIMESTAMP(date_sub(last_active,interval -2 hour)) >= UNIX_TIMESTAMP(now()))')->orderBy('last_active', 'DESC')->get();
        foreach ($user_active as $list) {
            $isChrome += is_numeric(strpos(strtolower(self::get_browser_content($list->last_login_client)), "chrome"));
            $isEdge += is_numeric(strpos(strtolower(self::get_browser_content($list->last_login_client)), "edge"));
            $isSafari += is_numeric(strpos(strtolower(self::get_browser_content($list->last_login_client)), "safari"));
            $isFirefox += is_numeric(strpos(strtolower(self::get_browser_content($list->last_login_client)), "firefox"));
            if (!$isChrome && !$isEdge && !$isSafari && !$isFirefox) $isOther++;
        }
        return (object)["chrome"=>$isChrome, "edge"=>$isEdge, "safari"=>$isSafari, "firefox"=>$isFirefox, "other"=>$isOther];
    }

    public function get_device_content($user_agent, $icon=false)
    {
        $isMob = is_numeric(strpos(strtolower($user_agent), "mobile"));
        $isTab = is_numeric(strpos(strtolower($user_agent), "tablet"));
        $isDes = !$isMob && !$isTab;
        if ($isDes) {
            $ic = ($icon == true) ? '<i class="fas fa-desktop"></i> ' : '';
            $device = $ic.'Desktop';
        } else if ($isTab) {
            $ic = ($icon == true) ? '<i class="fas fa-tablet-alt"></i> ' : '';
            $device = $ic.'Tablet';
        } else {
            $ic = ($icon == true) ? '<i class="fas fa-mobile-alt"></i> ' : '';
            $device = $ic.'Mobile';
        }
        return $device;
    }

    public function get_os_content($user_agent, $icon=false)
    {
        $isWin = is_numeric(strpos(strtolower($user_agent), "windows"));
        $isAndroid = is_numeric(strpos(strtolower($user_agent), "android"));
        $isIPhone = is_numeric(strpos(strtolower($user_agent), "iphone"));
        $isIPad = is_numeric(strpos(strtolower($user_agent), "ipad"));
        $isIOS = $isIPhone || $isIPad;
        if ($isWin) {
            $ic = ($icon == true) ? '<i class="fab fa-windows"></i> ' : '';
            $os = $ic.'Windows';
        } else if ($isAndroid) {
            $ic = ($icon == true) ? '<i class="fab fa-android"></i> ' : '';
            $os = $ic.'Android';
        } else if ($isIOS) {
            $ic = ($icon == true) ? '<i class="fab fa-apple"></i> ' : '';
            $os = $ic.'iOS';
        } else {
            $ic = ($icon == true) ? '<i class="far fa-question-circle"></i> ' : '';
            $os = $ic.'Other';
        }
        return $os;
    }

    public function get_browser_content($user_agent, $icon=false)
    {
        $arr_browsers = ["Opera", "Edg", "Chrome", "Safari", "Firefox", "MSIE", "Trident"];
        $user_browser = '';
        foreach ($arr_browsers as $browser) {
            if (strpos($user_agent, $browser) !== false) {
                $user_browser = $browser;
                break;
            }
        }
        switch ($user_browser) {
            case 'MSIE':
                $ic = ($icon == true) ? '<i class="fab fa-internet-explorer"></i> ' : '';
                $user_browser = $ic.'Internet Explorer';
                break;
            case 'Trident':
                $ic = ($icon == true) ? '<i class="fab fa-internet-explorer"></i> ' : '';
                $user_browser = $ic.'Internet Explorer';
                break;
            case 'Edg':
                $ic = ($icon == true) ? '<i class="fab fa-edge"></i> ' : '';
                $user_browser = $ic.'Microsoft Edge';
                break;
            case 'Opera':
                $ic = ($icon == true) ? '<i class="fab fa-opera"></i> ' : '';
                $user_browser = $ic.'Opera';
                break;
            case 'Chrome':
                $ic = ($icon == true) ? '<i class="fab fa-chrome"></i> ' : '';
                $user_browser = $ic.'Google Chrome';
                break;
            case 'Safari':
                $ic = ($icon == true) ? '<i class="fab fa-safari"></i> ' : '';
                $user_browser = $ic.'Safari';
                break;
            case 'Firefox':
                $ic = ($icon == true) ? '<i class="fab fa-firefox-browser"></i> ' : '';
                $user_browser = $ic.'Firefox';
                break;
            default:
                $ic = ($icon == true) ? '<i class="fas fa-globe"></i> ' : '';
                $user_browser = $ic.'Other';
                break;
        }
        return $user_browser;
    }
}