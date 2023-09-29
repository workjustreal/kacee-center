<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestSpecialDiscount as Special;
use App\Models\RequestSpecialLog as Log;
use App\Models\RequestSpecialNoti as Noti;
use App\Models\EXCustomer;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;
use Image;
use DB;
use Auth;

class SpecialDiscountController extends Controller
{
    // ***doc status***
    // status 0 = เลขาไม่อนุมัติ
    // status 1 = หัวหน้าอนุมัติ
    // status 2 = ดำเนินการ
    // status 3 = หัวหน้าไม่อนุมัติ
    // status 9 = เลขาอนุมัติ หรือ เสร็จสิ้น

    public function __construct()
    {
        ini_set('memory_limit', '1024M');
    }
    // view listpersonal
    public function index(Request $request)
    {
        $date = $request->doc_date;
        $doc_status = $request->doc_status;
        $area = Employee::where('emp_id', '=', auth()->user()->emp_id)->first('area_code');
        if ($date == "") {
            $special = Special::from('request_special_discount as r')->leftJoin('request_special_log as l', 'l.doc_id', '=', 'r.doc_id')
                ->where('r.area_code', '=', $area->area_code)
                ->where('r.doc_status', 'LIKE', $doc_status . '%')
                ->orderBy('r.created_at', 'DESC')
                ->groupBy('r.doc_id')
                ->selectRaw('r.*,l.emp_id as logemp,
                    CONCAT(
                    "[",
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            "description", l.description,
                            "comment", l.comment,
                            "date", l.created_at,
                            "logemp", l.emp_id
                        )
                        order by l.created_at
                    ),
                    "]"
                    ) AS log_list
                ')->get();
        } else {
            $_date = explode("/", $date);
            $start_date = date($_date[1] . "-" . $_date[0] . "-01 00:00:00");
            $end_date = date("Y-m-t", strtotime($start_date)) . " 23:59:59";
            $special = Special::from('request_special_discount as r')->leftJoin('request_special_log as l', 'l.doc_id', '=', 'r.doc_id')
                ->where('r.area_code', '=', $area->area_code)
                ->where('r.doc_status', 'LIKE', $doc_status . '%')
                ->whereBetween('r.created_at', [$start_date, $end_date])
                ->groupBy('r.doc_id')
                ->selectRaw('r.*,l.emp_id as logemp,
                    CONCAT(
                    "[",
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            "description", l.description,
                            "comment", l.comment,
                            "date", l.created_at,
                            "logemp", l.emp_id
                        )
                        order by l.created_at
                    ),
                    "]"
                    ) AS log_list
                ')->get();
        }
        $request->flash();
        return view('sales-document.special-discount.list-personal', compact('special'));
    }

    // form request
    public function request(Request $request)
    {
        $request->session()->forget('special_img');
        return view('sales-document.special-discount.request');
    }

    // create request
    public function createRequest(Request $request)
    {
        $customer = EXCustomer::where('cuscod', '=', $request->customer_code)->get();
        $dept_id = Employee::where('emp_id', '=', auth()->user()->emp_id)->first('dept_id');
        $area = Employee::where('emp_id', '=', auth()->user()->emp_id)->first('area_code');
        $_level = Department::where('dept_id', auth()->user()->dept_id)->first();

        // generate documnet id
        $gen = "SPC" . date("ym");
        $rundoc = Special::whereRaw('SUBSTRING(doc_id, 1, 7) = "' . $gen . '"')->orderBy('created_at', 'DESC')->first('doc_id');
        if ($rundoc) {
            $running_id = str_pad(intval(substr($rundoc->doc_id, 7, 3) + 1), 3, "0", STR_PAD_LEFT);
        } else {
            $running_id = "001";
        }
        $doc_id = $gen . $running_id;
        // setting file
        $request->validate([
            'file' => 'mimes:txt,csv,xlx,xls,xlsx,pdf,docx|max:4048'
        ]);
        if ($request->file) {
            $file = date("Ymdhis") . '.' . $request->file->extension();
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/file/';
            $request->file->move($file_path, $file);
        } else {
            $file = '';
        }
        //get image name
        $img_name  = session()->get('special_img');

        if ($customer->isNotEmpty() && $request->customer_name != '') {
            $special = new Special();
            $special->doc_id = $doc_id;
            $special->customer_code = $request->customer_code;
            $special->customer_name = $request->customer_name;
            $special->customer_status = $request->customer_status;
            $special->limit = $request->limit;
            $special->employee = $request->staf;
            $special->emp_id = auth()->user()->emp_id;
            $special->dept_id = $dept_id->dept_id;
            $special->description = nl2br(e($request->product_detail));
            $special->file = $file;
            $special->image = json_encode($img_name, JSON_FORCE_OBJECT);
            $special->area_code = $area->area_code;

            if ($_level->level == "1" || $_level->level == "2") {
                $special->doc_status = 1;
                $special->mn_approve = auth()->user()->emp_id;
                $special->mn_approve_date = now();
                $special->save();

                $des_c1 = ["สร้างคำขอ", "ManagerApprove"];
                foreach ($des_c1 as $des1) {
                    $log = new Log();
                    $log->doc_id = $doc_id;
                    $log->emp_id = auth()->user()->emp_id;
                    $log->description = $des1;
                    $log->save();
                }
            } else {
                $description = "สร้างคำขอ";
                $special->doc_status = 2;
                $special->save();

                // save log
                $log = new Log();
                $log->doc_id = $doc_id;
                $log->emp_id = auth()->user()->emp_id;
                $log->description = $description;
                $log->save();
            }

            $noti = new Noti();
            $noti->doc_id = $doc_id;
            $noti->save();
            $request->session()->forget('special_img');
            alert()->success('สร้างคำขอเรียบร้อย');
            $request->flash();
            return redirect('/sales-document/special-discount/list-personal');
        } else {
            $request->session()->forget('special_img');
            alert()->error('ไม่พบลูกค้านี้ในรายชื่อ');
            $request->flash();
            return back();
        }
    }

    // clear image request
    public function clearImage(Request $request)
    {
        $_img = session()->get('special_img');
        if (session()->has('special_img')) {
            if ($_img) {
                $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/special/';
                // delete imgage
                foreach ($_img as $delete) {
                    File::delete($destinationPath . $delete);
                }
            }
        }
        $request->session()->forget('special_img');
    }

    // remove image one by one
    public function removeImg(Request $request)
    {
        $_img = session()->pull('special_img', []); // Second argument is a default value
        if (($key = array_search($request->name, $_img)) !== false) {
            unset($_img[$key]);
        }
        session()->put('special_img', $_img);

        if ($request->name) {
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/special/';
            // delete imgage
            File::delete($destinationPath . $request->name);
        }
    }

    // upload image request
    public function imageRequest(Request $request)
    {
        $image = $request->file('file');
        if ($image) {
            $filename = $image->getClientOriginalName();
            session()->push('special_img',  $filename);
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/special/';
            $img = Image::make($image->path());
            $img->save($destinationPath . $filename);
            $_img = session()->get('special_img');
            return response()->json(['success' => $filename, 'session' => $_img]);
        }
    }

    // search customer
    public function autoSearch(Request $request)
    {
        $search = $request->get('search');
        $result = DB::table('ex_customer')->leftJoin('ex_seller', 'ex_seller.slmcod', '=', 'ex_customer.slmcod')
            // ->where('ex_customer.cuscod', 'LIKE', '%' . $search . '%')
            ->where('ex_customer.cuscod', 'LIKE',  $search . '%')
            ->orWhere('ex_customer.cusnam', 'LIKE',  $search . '%')
            ->orderBy('cuscod', 'ASC')
            ->take(10)
            ->get(['ex_customer.cuscod', 'ex_customer.prenam', 'ex_customer.cusnam', 'ex_customer.paycond', 'ex_customer.crline', 'ex_customer.slmcod', 'ex_seller.slmnam']);
        return response()->json($result);
    }

    // view Request
    public function preView(Request $request, $action, $doc_id, $status)
    {
        $request->session()->forget('special_img');
        $wimage = Special::where('doc_id', '=', $doc_id)->where('image', '<>', 'null')->where('image', '<>', '{}')->get('image');
        $wfile = Special::where(
            'doc_id',
            '=',
            $doc_id
        )->where('file', '<>', '')->get('file');
        $admin_manager = self::adminManager(auth()->user()->emp_id);
        $result = Special::from('request_special_discount as r')
            ->leftJoin('employee as e', 'e.emp_id', '=', 'r.emp_id')
            ->where('r.doc_id', '=', $doc_id)
            ->get(
                [
                    'e.name',
                    'e.surname',
                    'e.nickname',
                    'r.*',
                ]
            );

        //read Notification
        if ($action == 'personal_read') {
            $noti = Noti::where('doc_id', '=', $doc_id)->first();
            $noti->personal_read = "read";
            $noti->update();
        } else if ($action == 'manager_read') {
            $noti = Noti::where('doc_id', '=', $doc_id)->first();
            $noti->manager_read = "read";
            $noti->update();
        } else if ($action == 'secretary_action') {
            $noti = Noti::where('doc_id', '=', $doc_id)->first();
            $noti->manager_read = "read";
            $noti->personal_read = "read";
            $noti->secretary_action = "read";
            $noti->update();
        }

        if ($result->isNotEmpty() && $result[0]->doc_status == $status) {
            foreach ($result as $val) {
                // manger approve
                if ($val->mn_approve != "" && $val->doc_status == 1 || $val->doc_status == 9 || $val->doc_status == 0) {
                    $mn_approve = "completed";
                    $secretary_dot = '<span class="active-dot dot"></span>';
                    $mn_date = $val->updated_at;
                    $mn_dot = '';
                } else if ($val->mn_approve != "" && $val->doc_status == 3) {
                    $mn_approve = "";
                    $secretary_dot = '';
                    $mn_date = $val->updated_at;
                    $mn_dot = '<span class="active-dot dot"></span>';
                } else {
                    $mn_approve = "";
                    $secretary_dot = '';
                    $mn_date = '';
                    $mn_dot = '<span class="active-dot dot"></span>';
                }
                if ($val->sec_approve != "" && $val->doc_status == 9) {
                    $st_approve = 'completed';
                    $succes_dot = '<span class="active-dot dot"></span>';
                    $st_date = $val->updated_at;
                    $secretary_dot = '';
                } else if ($val->sec_approve != "" && $val->doc_status == 0) {
                    $st_approve = '';
                    $succes_dot = '';
                    $st_date = $val->updated_at;
                    $secretary_dot = '<span class="active-dot dot"></span>';
                } else {
                    $st_approve = '';
                    $succes_dot = '';
                    $st_date = '';
                }
                $log = Log::from('request_special_log as l')->leftJoin('employee as e', 'e.emp_id', '=', 'l.emp_id')->where('l.doc_id', '=', $doc_id)
                    ->get([
                        'l.emp_id',
                        'l.description',
                        'l.comment',
                        'l.updated_at',
                        'e.name',
                        'e.surname',
                        'e.nickname'
                    ]);
                // ส่วนเลขาอนุมัติ
                $name_secapp = [];
                $thaiDateHelper = app()->make('\App\Services\ThaiDateHelperService');
                foreach ($log as $logact) {
                    if ($logact->description == 'SecretaryApprove') {
                        $name_secapp[] = "$logact->name" . " " . "$logact->surname" . " " . "(" . $logact->nickname . ")" . '<small class="text-muted"> <i class="mdi mdi-clock-outline"></i>' . $thaiDateHelper->shortDateFormat($logact->updated_at) . "</small>";
                    } elseif ($logact->description == 'Secretary DisApprove') {
                        $name_secapp[] = $logact->name . ' ' . $logact->surname . ' ' . '(' . $logact->nickname . ')';
                    } elseif ($logact->description == 'Secretary Comment') {
                        $name_secapp[] = "$logact->name" . " " . "$logact->surname" . " " . "(" . $logact->nickname . ")" . '<small class="text-muted"> <i class="mdi mdi-clock-outline"></i>' . $thaiDateHelper->shortDateFormat($logact->updated_at) . "</small>";
                    }
                }
                if ($val->mn_approve == "") {
                    $mn_app = "";
                    $mn_date_log = "";
                } else {
                    $mn_app = Employee::where(
                        'emp_id',
                        '=',
                        $val->mn_approve
                    )->first(['name', 'surname', 'nickname']);
                    $mn_date_log = $val->mn_approve_date;
                }
                if ($val->sec_approve == "") {
                    $sec_app = "";
                    $sec_date_log = "";
                } else {
                    $sec_app = Employee::where('emp_id', '=', $val->sec_approve)->first(['name', 'surname', 'nickname']);
                    $sec_date_log = $val->sec_approve_date;
                }
            }
            return view('sales-document.special-discount.preview', compact('result', 'mn_approve', 'mn_dot', 'secretary_dot', 'st_approve', 'succes_dot', 'mn_date', 'st_date', 'status',  'mn_app', 'mn_date', 'log', 'sec_app', 'sec_date_log', 'mn_date_log', 'wimage', 'wfile', 'admin_manager', 'name_secapp'));
        } else {
            return back();
        }
    }

    // check addmin manager
    public function adminManager($emp)
    {
        // $admin = ['620274', '630040', '640172', '640195', '660039', '660100'];
        $manager = [
            '580073', '530107', '510186', '630324', '610284', '630056', '660182'
        ];
        $admin_manager = array_search($emp, $manager);
        if (Auth::User()->isAdmin() || is_int($admin_manager)) {
            return 1;
        } else {
            return false;
        }
    }

    // check department permission
    public function deptFormat($dept)
    {
        $res = '';
        $_data = Department::where('dept_id', $dept)->first();
        if ($_data->level == 1) {
            $res = '';
        } elseif ($_data->level == 2) {
            $_res = substr($dept, 0, 5);
            if ($_res == 'A0204') {
                $res = substr($dept, 0, 5);
            } elseif ($_res == 'A0203') {
                $res = substr($dept, 0, 5);
            } elseif ($_res == 'A0205') {
                $res = substr($dept, 0, 5);
            } else {
                $res = substr($dept, 0, 4);
            }
        } else {
            $res = $dept;
        }
        return $res;
    }

    public function wApprove($position)
    {
        $admin = substr(auth()->user()->dept_id, 0, 5);
        if ($position == "mn") {
            if ($admin == "A0110") {
                $req = Special::where('doc_status', '=', 2)->orderBy('updated_at', 'DESC')
                    ->get();
            } else {
                $_dept = self::deptFormat(auth()->user()->dept_id);
                $req = Special::where('doc_status', '=', 2)
                    // ->where('dept_id', 'LIKE', $_dept . '%')
                    ->where(function ($query) use ($admin, $_dept) {
                        if ($admin == 'A0202' || $admin == 'A0201') {
                            $query->where('dept_id', 'LIKE', 'A0201' . '%');
                            $query->orWhere('dept_id', 'LIKE', 'A0202' . '%');
                        } else {
                            $query->where('dept_id', 'LIKE', $_dept . '%');
                        }
                    })
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            }
        } else if ($position == "sec") {
            $req = Special::where('doc_status', '=', 1)->orderBy('updated_at', 'DESC')
                ->get();
        }
        return $req;
    }

    // list approve
    public function listManagerApprove(Request $request)
    {
        $wapprove = "";
        $date = $request->doc_date;
        $doc_status = $request->doc_status;
        $admin = substr(auth()->user()->dept_id, 0, 5);
        $admin_manager = self::adminManager(auth()->user()->emp_id);
        $_dept = self::deptFormat(auth()->user()->dept_id);
        if ($date == "") {
            if ($admin == "A0110") {
                $special = Special::from('request_special_discount as r')->leftJoin('request_special_log as l', 'l.doc_id', '=', 'r.doc_id')
                    ->where('r.doc_status', 'LIKE', $doc_status . '%')
                    ->orderBy('r.created_at', 'DESC')
                    ->groupBy('r.doc_id')
                    ->selectRaw('r.*,l.emp_id as logemp,
                    CONCAT(
                    "[",
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            "description", l.description,
                            "comment", l.comment,
                            "date", l.created_at,
                            "logemp", l.emp_id

                        )
                        order by l.created_at
                    ),
                    "]"
                    ) AS log_list
                ')->get();
            } else {
                $special = Special::from('request_special_discount as r')->leftJoin('request_special_log as l', 'l.doc_id', '=', 'r.doc_id')
                    ->where('r.doc_status', 'LIKE', $doc_status . '%')
                    // ->where('r.dept_id', 'LIKE', $_dept . '%')
                    ->where(function ($query) use ($admin, $_dept) {
                        if ($admin == 'A0202' || $admin == 'A0201') {
                            $query->where('r.dept_id', 'LIKE', 'A0201' . '%');
                            $query->orWhere('r.dept_id', 'LIKE', 'A0202' . '%');
                        } else {
                            $query->where('dept_id', 'LIKE', $_dept . '%');
                        }
                    })
                    ->orderBy('r.created_at', 'DESC')
                    ->groupBy('r.doc_id')
                    ->selectRaw('r.*,l.emp_id as logemp,
                    CONCAT(
                    "[",
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            "description", l.description,
                            "comment", l.comment,
                            "date", l.created_at,
                            "logemp", l.emp_id
                        )
                        order by l.created_at
                    ),
                    "]"
                    ) AS log_list
                ')->get();
            }
        } else {
            $_date = explode("/", $date);
            $start_date = date($_date[1] . "-" . $_date[0] . "-01 00:00:00");
            $end_date = date("Y-m-t", strtotime($start_date)) . " 23:59:59";
            if ($admin == "A0110") {
                $special = Special::from('request_special_discount as r')->leftJoin('request_special_log as l', 'l.doc_id', '=', 'r.doc_id')
                    ->where('r.doc_status', 'LIKE', $doc_status . '%')
                    ->whereBetween('r.created_at', [$start_date, $end_date])
                    ->orderBy('r.created_at', 'DESC')
                    ->groupBy('r.doc_id')
                    ->selectRaw('r.*,l.emp_id as logemp,
                    CONCAT(
                    "[",
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            "description", l.description,
                            "comment", l.comment,
                            "date", l.created_at,
                            "logemp", l.emp_id
                        )
                        order by l.created_at
                    ),
                    "]"
                    ) AS log_list
                ')->get();
            } else {
                $_dept = self::deptFormat(auth()->user()->dept_id);
                $special = Special::from('request_special_discount as r')->leftJoin('request_special_log as l', 'l.doc_id', '=', 'r.doc_id')
                    ->where('r.doc_status', 'LIKE', $doc_status . '%')
                    ->where('r.dept_id', 'LIKE', $_dept . '%')
                    ->whereBetween('r.created_at', [$start_date, $end_date])
                    ->orderBy('r.created_at', 'DESC')
                    ->groupBy('r.doc_id')
                    ->selectRaw('r.*,l.emp_id as logemp,
                    CONCAT(
                    "[",
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            "description", l.description,
                            "comment", l.comment,
                            "date", l.created_at,
                            "logemp", l.emp_id
                        )
                        order by l.created_at
                    ),
                    "]"
                    ) AS log_list
                ')->get();
            }
        }
        $wapprove = self::wApprove("mn");
        $request->flash();
        return view('sales-document.special-discount.approve-manager', compact('special', 'wapprove', 'admin_manager'));
    }

    // secretary approve
    public function listSecretaryApprove(Request $request)
    {
        $_status = ['0', '1', '9'];
        $date = $request->doc_date;
        $doc_status = $request->doc_status;
        if ($date == "") {
            $special = Special::from('request_special_discount as r')->leftJoin('request_special_log as l', 'l.doc_id', '=', 'r.doc_id')
                ->whereIn('r.doc_status', $_status)
                ->where('r.doc_status', 'LIKE', $doc_status . '%')
                ->orderBy('r.updated_at', 'DESC')
                ->groupBy('r.doc_id')
                ->selectRaw('r.*,l.emp_id as logemp,l.updated_at as printDate,
                    CONCAT(
                    "[",
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            "description", l.description,
                            "comment", l.comment,
                            "date", l.created_at,
                            "logemp", l.emp_id
                        )
                        order by l.created_at
                    ),
                    "]"
                    ) AS log_list
                ')
                ->get();
        } else {
            $_date = explode("/", $date);
            $start_date = date($_date[1] . "-" . $_date[0] . "-01 00:00:00");
            $end_date = date("Y-m-t", strtotime($start_date)) . " 23:59:59";
            $special = Special::from('request_special_discount as r')->leftJoin('request_special_log as l', 'l.doc_id', '=', 'r.doc_id')
                ->whereIn('r.doc_status', $_status)
                ->where('r.doc_status', 'LIKE', $doc_status . '%')
                ->orderBy('r.updated_at', 'DESC')
                ->whereBetween('r.created_at', [$start_date, $end_date])
                ->groupBy('r.doc_id')
                ->selectRaw('r.*,l.emp_id as logemp,l.updated_at as printDate,
                    CONCAT(
                    "[",
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            "description", l.description,
                            "comment", l.comment,
                            "date", l.created_at,
                            "logemp", l.emp_id
                        )
                        order by l.created_at
                    ),
                    "]"
                    ) AS log_list
                ')
                ->get();
        }
        $wapprove = self::wApprove("sec");
        $request->flash();
        return view('sales-document.special-discount.approve-secretary', compact('special', 'wapprove'));
    }

    // get Request to edit
    public function getEdit(Request $request, $doc_id, $id, $status)
    {
        $request->session()->forget('special_img');
        $noedit = "";
        $img = "";
        $result = Special::where('doc_id', '=', $doc_id)->get();
        $images = Special::where('doc_id', '=', $doc_id)->where('image', '<>', 'null')->first('image');
        $file = Special::where('doc_id', '=', $doc_id)->where('file', '<>', '')->first('file');
        $log = Log::from('request_special_log as r')->leftJoin('employee as e', 'e.emp_id', '=', 'r.emp_id')
            ->where('r.doc_id', '=', $doc_id)
            ->get([
                'e.name',
                'e.surname',
                'e.nickname',
                'r.*',
            ]);
        if ($images) {
            $img = json_decode($images->image);
        }
        foreach ($log as $chkedit) {
            if ($chkedit->description == 'แก้ไขเพื่อขออีกครั้ง') {
                if (
                    $result[0]->doc_status == 3 || $result[0]->doc_status == 0
                ) {
                    $noedit = "true";
                }
            }
        }
        if ($noedit) {
            alert()->error('ไม่อนุญาติให้แก้ไข', 'คุณเคยแก้ไขไปก่อนหน้านี้แล้ว')->autoClose(5000);
            return redirect('/sales-document/special-discount/list-personal');
        } else {
            return view('sales-document.special-discount.edit-request', compact('result', 'status', 'log', 'doc_id', 'img', 'file'));
        }
    }

    // update Request
    public function updateRequest(Request $request, $doc_id, $id, $status)
    {
        $customer = EXCustomer::where('cuscod', '=', $request->customer_code)->get();
        $logagain = Log::where('doc_id', '=', $doc_id)->where('description', '=', 'แก้ไขเพื่อขออีกครั้ง')->first();
        $old_img = $request->old_images;
        $comment = "";
        if ($logagain) {
            $logup = Log::find($logagain->id);
            $logup->comment = $request->againedit;
            $logup->update();
        }
        if ($status == 2) {
            $log_des = "แก้ไขคำขอ";
        } else {
            Noti::where('doc_id', $doc_id)->update([
                'personal_read' => null,
                'manager_read' => null,
                'secretary_action' => null,
                // Update other attributes as needed
            ]);
            $log_des = "แก้ไขเพื่อขออีกครั้ง";
            $comment = $request->again;
        }

        if (session()->has('special_img')) {
            if ($old_img) {
                $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/special/';
                // delete imgage
                foreach ($old_img as $old_img) {
                    File::delete($destinationPath . $old_img);
                }
            }
            $img_name = session()->get('special_img');
        } else {
            $img_name = '';
        }

        if ($request->file) {
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/file/';
            // delete file
            File::delete($file_path . $request->old_file);

            //settig file
            $request->validate([
                'file' => 'mimes:csv,txt,xlx,xls,xlsx,pdf,docx|max:4048'

            ]);
            $file = date("Ymdhis") . '.' . $request->file->extension();
            $request->file->move($file_path, $file);
        }

        if ($customer->isNotEmpty()) {
            $special = Special::find($id);
            $special->customer_code = $request->customer_code;
            $special->customer_name = $request->customer_name;
            $special->customer_status = $request->customer_status;
            $special->limit = $request->limit;
            $special->employee = $request->staf;
            $special->description = nl2br(e($request->description));

            if ($img_name != '') {
                $special->image = json_encode($img_name, JSON_FORCE_OBJECT);
            }
            if ($request->file) {
                $special->file = $file;
            }
            $special->updated_at = now();
            if ($status == 3 || $status == 0) {
                $special->doc_status = 2;
            }
            $special->update();
            self::saveLog($doc_id, $log_des, $comment);

            $request->session()->forget('special_img');
            alert()->success('แก้ไขคำขอเรียบร้อย');
            return redirect('/sales-document/special-discount/list-personal');
        } else {
            alert()->error('ไม่พบลูกค้านี้ในรายชื่อ');
            return back();
        }
    }

    // save log
    public function saveLog($doc_id, $log_des, $log_comment)
    {
        $log = new Log();
        $log->doc_id = $doc_id;
        $log->emp_id = auth()->user()->emp_id;
        $log->description = $log_des;
        $log->comment = $log_comment;
        $log->save();
    }

    // delete request
    public function deleteRequest(Request $request)
    {
        $wimg = Special::where('id', '=', $request->id)->where('image', '<>', 'null')->first('image');
        $wfile = Special::where('id', '=', $request->id)->where('file', '<>', '')->first('file');
        if ($wimg) {
            $img = json_decode($wimg->image);
        }
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/special/';
        $file_path = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/file/';

        // delete file
        if ($wfile) {
            File::delete($file_path . $wfile->file);
        }

        // delete imgage
        if ($wimg) {
            foreach ($img as $img) {
                File::delete($destinationPath . $img);
            }
        }

        Special::where('id', '=', $request->id)->delete();
        Log::where('doc_id', '=', $request->doc_id)->delete();
        Noti::where('doc_id', '=', $request->doc_id)->delete();
        return response()->json(['success' => true, "message" => "ลบคำขอเรียบร้อย"]);
    }

    // view manager approve
    public function previewManagerApprove(Request $request, $doc_id, $id)
    {
        $_img = session()->get('special_img');
        $img_name = $_img;
        $old_img = $request->old_image;

        if (session()->has('special_img')) {
            if ($old_img) {
                $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/special/';
                // delete imgage
                foreach ($old_img as $old_img) {
                    File::delete($destinationPath . $old_img);
                }
            }

            $img_name = json_encode($_img, JSON_FORCE_OBJECT);
        } else {
            $img_name = '';
        }
        if ($request->file) {
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/file/';
            // delete file
            File::delete($file_path . $request->old_file);

            //settig file
            $request->validate([
                'file' => 'mimes:csv,txt,xlx,xls,xlsx,pdf,docx|max:4048'
            ]);
            $file = date("Ymdhis") . '.' . $request->file->extension();
            $request->file->move($file_path, $file);
        } else {
            $file = '';
        }

        if ($request->approve == "yes") {
            $special = Special::find($id);
            $special->doc_status = 1;
            $special->mn_approve = auth()->user()->emp_id;
            $special->mn_approve_date = now();
            $special->updated_at = now();
            if ($img_name != '') {
                $special->image_mn = $img_name;
            }
            if ($file != '') {
                $special->file_mn = $file;
            }
            $special->update();
            $log_des = "ManagerApprove";
            $log_comment = $request->comment;
            self::saveLog(
                $doc_id,
                $log_des,
                $log_comment
            );
            return redirect('sales-document/special-discount/manager-approve');
        } elseif ($request->approve == "no") {
            $special = Special::find($id);
            $special->doc_status = 3;
            $special->mn_approve = auth()->user()->emp_id;
            $special->mn_approve_date = now();
            $special->updated_at = now();
            if ($img_name != '') {
                $special->image_mn = $img_name;
            }
            if ($file != '') {
                $special->file_mn = $file;
            }
            $special->update();
            $log_des = "Manager DisApprove";
            $log_comment = $request->comment;
            self::saveLog($doc_id, $log_des, $log_comment);
            return redirect('sales-document/special-discount/manager-approve');
        }
        $request->session()->forget('special_img');
    }

    // secretary approve request
    public function secretaryApprove(Request $request)
    {
        if ($request->approve == "yes") {
            $special = Special::find($request->id);
            $special->doc_status = 9;
            $special->sec_approve = auth()->user()->emp_id;
            $special->sec_approve_date = now();
            $special->updated_at = now();
            $special->update();
            $log_des = "SecretaryApprove";
            $log_comment = $request->comment;
            $message = 'อนุมัติเรียบร้อย';
        } elseif ($request->approve == "no") {
            $special = Special::find($request->id);
            $special->doc_status = 0;
            $special->sec_approve = auth()->user()->emp_id;
            $special->sec_approve_date = now();
            $special->updated_at = now();
            $special->update();
            $log_des = "Secretary DisApprove";
            $log_comment = $request->comment;
            $message = 'ไม่อนุมัติเรียบร้อย';
        } else {
            $log_des = "Secretary Comment";
            $log_comment = $request->comment;
            $message = 'เพิ่มเติม';
        }
        self::saveLog($request->doc_id, $log_des, $log_comment);
        $request->session()->forget('special_img');
        return response()->json(['success' => true, "message" => $message]);
    }

    public function print($doc_id)
    {
        $data = Special::from('request_special_discount as r')->leftJoin('request_special_log as l', 'l.doc_id', '=', 'r.doc_id')
            ->where('r.doc_id', '=', $doc_id)
            ->groupBy('r.doc_id')
            ->selectRaw('r.*,
                    CONCAT(
                    "[",
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            "description", l.description,
                            "comment", l.comment
                        )
                    ),
                    "]"
                    ) AS log_list
                ')->get();
        $dompdf = App::make('dompdf.wrapper');
        $dompdf->loadView('sales-document.special-discount.pdf.pdf-special', compact('data'))
            ->setPaper('a4')
            ->setWarnings(false);

        return $dompdf->stream($doc_id . '_' . $data[0]->emp_id . '_' . $data[0]->customer_code . '.pdf');
    }

    // print save log
    public function logPrint(Request $request)
    {
        $log_des = 'พิมพ์เอกสาร';
        $log_comment = '';
        self::saveLog($request->doc_id, $log_des, $log_comment);
        return response()->json(['success' => 'saveprint']);
    }

    public function report(Request $request)
    {
        $date = $request->doc_date;
        $doc_status = $request->doc_status;
        if ($date == "") {
            $dis_re = Special::from('request_special_discount as r')->leftJoin('request_special_log as l', 'l.doc_id', '=', 'r.doc_id')
                ->where('r.doc_status', 'LIKE', $doc_status . '%')
                ->orderBy('r.created_at', 'DESC')
                ->groupBy('r.doc_id')
                ->selectRaw('r.*,
                    CONCAT(
                    "[",
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            "description", l.description,
                            "comment", l.comment,
                            "date", l.created_at,
                            "logemp", l.emp_id
                        )
                        order by l.created_at
                    ),
                    "]"
                    ) AS log_list
                ')->get();
        } else {
            $_date = explode(
                "/",
                $date
            );
            $start_date = date($_date[1] . "-" . $_date[0] . "-01 00:00:00");
            $end_date = date("Y-m-t", strtotime($start_date)) . " 23:59:59";
            $dis_re = Special::from('request_special_discount as r')->leftJoin('request_special_log as l', 'l.doc_id', '=', 'r.doc_id')
                // ->where('r.emp_id', '=', auth()->user()->emp_id)
                ->where('r.doc_status', 'LIKE', $doc_status . '%')
                ->whereBetween('r.created_at', [$start_date, $end_date])
                ->groupBy('r.doc_id')
                ->selectRaw('r.*,
                    CONCAT(
                    "[",
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            "description", l.description,
                            "comment", l.comment,
                            "date", l.created_at,
                            "logemp", l.emp_id
                        )
                        order by l.created_at
                    ),
                    "]"
                    ) AS log_list
                ')->get();
        }
        $request->flash();
        return view('sales-document.special-discount.report', compact('dis_re'));
    }

    public function reportPreview(Request $request, $action, $doc_id, $status)
    {
        $request->session()->forget('request_img');
        $wimage = Special::where('doc_id', '=', $doc_id)->where('image', '<>', 'null')->get('image');
        $wfile = Special::where('doc_id', '=', $doc_id)->where('file', '<>', '')->get('file');
        $admin_manager = self::adminManager(auth()->user()->emp_id);
        $result = Special::from('request_special_discount as r')
            ->leftJoin('employee as e', 'e.emp_id', '=', 'r.emp_id')
            ->where('r.doc_id', '=', $doc_id)
            ->get(
                [
                    'e.name',
                    'e.surname',
                    'e.nickname',
                    'r.*',
                ]
            );
        if ($result->isNotEmpty()) {
            foreach ($result as $val) {
                // manger approve
                if ($val->mn_approve != "" && $val->doc_status == 1 || $val->doc_status == 9 || $val->doc_status == 0) {
                    $mn_approve = "completed";
                    $secretary_dot = '<span class="active-dot dot"></span>';
                    $mn_date = $val->updated_at;
                    $mn_dot = '';
                } else if ($val->mn_approve != "" && $val->doc_status == 3) {
                    $mn_approve = "";
                    $secretary_dot = '';
                    $mn_date = $val->updated_at;
                    $mn_dot = '<span class="active-dot dot"></span>';
                } else {
                    $mn_approve = "";
                    $secretary_dot = '';
                    $mn_date = '';
                    $mn_dot = '<span class="active-dot dot"></span>';
                }
                if (
                    $val->sec_approve != "" && $val->doc_status == 9
                ) {
                    $st_approve = 'completed';
                    $succes_dot = '<span class="active-dot dot"></span>';
                    $st_date = $val->updated_at;
                    $secretary_dot = '';
                } else if ($val->sec_approve != "" && $val->doc_status == 0) {
                    $st_approve = '';
                    $succes_dot = '';
                    $st_date = $val->updated_at;
                    $secretary_dot = '<span class="active-dot dot"></span>';
                } else {
                    $st_approve = '';
                    $succes_dot = '';
                    $st_date = '';
                }
                $log = Log::from('request_special_log as l')->leftJoin('employee as e', 'e.emp_id', '=', 'l.emp_id')->where('l.doc_id', '=', $doc_id)
                    ->get([
                        'l.emp_id',
                        'l.description',
                        'l.comment',
                        'l.updated_at',
                        'e.name',
                        'e.surname',
                        'e.nickname'
                    ]);

                if ($val->mn_approve == "") {
                    $mn_app = "";
                    $mn_date_log = "";
                } else {
                    $mn_app = Employee::where(
                        'emp_id',
                        '=',
                        $val->mn_approve
                    )->first(['name', 'surname', 'nickname']);
                    $mn_date_log = $val->mn_approve_date;
                }
                if ($val->sec_approve == "") {
                    $sec_app = "";
                    $sec_date_log = "";
                } else {
                    $sec_app = Employee::where('emp_id', '=', $val->sec_approve)->first(['name', 'surname', 'nickname']);
                    $sec_date_log = $val->sec_approve_date;
                }
            }
        }
        if ($action == 'personal_read') {
            $noti = Noti::where('doc_id', '=', $doc_id)->first();
            $noti->personal_read = "read";
            $noti->update();
        } else if ($action == 'manager_read') {
            $noti = Noti::where('doc_id', '=', $doc_id)->first();
            $noti->manager_read = "read";
            $noti->update();
        } else if ($action == 'secretary_action') {
            $noti = Noti::where('doc_id', '=', $doc_id)->first();
            $noti->manager_read = "read";
            $noti->personal_read = "read";
            $noti->secretary_action = "read";
            $noti->update();
        }
        return view('sales-document.special-discount.preview', compact('result', 'mn_approve', 'mn_dot', 'secretary_dot', 'st_approve', 'succes_dot', 'mn_date', 'st_date', 'status',  'mn_app', 'mn_date', 'log', 'sec_app', 'sec_date_log', 'mn_date_log', 'wimage', 'wfile', 'admin_manager'));
    }
}
