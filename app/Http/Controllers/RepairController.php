<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuthorizationManual;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Withdraw;
use App\Models\Repair;
use App\Models\User;
use Carbon\Carbon;
use Image;
use File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RepairController extends Controller
{
    // ******************************  PAGE  ***************************************** //

    public function index(){
        $repair = DB::table('repair')->get();
        return view('repair.dashboard')->with('repair', $repair);
    }

    public function form($request){ 
        $car = DB::table('car_main')->get();
        $repair_type = DB::table('repair_type')->orderBy('name', 'ASC')->get();
        $location = DB::table('repair_location')->groupBy('location')->get(['location']);
        $empname_parent = Employee::where('dept_id', '=', $request)->get();
        return view('repair.form', compact('request', 'empname_parent', 'car', 'location', 'repair_type'));
    }
    
    public function form_edit($id){ 
        $repairs = Repair::where('order_id', '=', $id)->first();
        $car = DB::table('car_main')->get();
        $repair_type = DB::table('repair_type')->orderBy('name', 'ASC')->get();
        $location = DB::table('repair_location')->groupBy('location')->get(['location']);
        $_location = "";
        $_other = "";
        $_class = "";
        $_address = "";
        if ($repairs->order_dept != "A03050200") {
            if (str_contains($repairs->order_address, 'อาคาร')) {
                $_replace = str_replace(":","", $repairs->order_address);
                $_spilt = explode(" ชั้นที่", $_replace);
                if (strpos($_replace, "สถานที่") === false) {
                    $_spilt2 = [$_spilt[1], NULL];
                } else {
                    $_spilt2 = explode("สถานที่", $_spilt[1]);
                }
                $_location = str_replace("อาคาร", "", $_spilt[0]);
                $_class = str_replace(" ", "", $_spilt2[0]);
                $_address = str_replace(" ", "", $_spilt2[1]);
            } else {
                $_location = "other";
                $_other = $repairs->order_address;
            }
        } 
        return view('repair.form_edit', compact('repairs', '_location', '_other', '_address', '_class', 'car', 'location', 'repair_type'));
    }

    public function approve_form_edit($id){ 
        $datas = self::allSelected($id);
        return view('repair.approve_form_edit', $datas);
    }
    
    public function approve(){
        $repair = DB::table('repair')->get();
        return view('repair.approve')->with('repair', $repair);
    }

    public function action(){
        $deptDatas = Department::where('dept_id', '=', auth()->user()->dept_id)->first();;
        if ( $deptDatas->level == 1 ) {
            $order_dept = substr(auth()->user()->dept_id , 0, 3);
        }
        elseif ($deptDatas->level == 2 ){
            $order_dept = substr(auth()->user()->dept_id , 0, 5);
        }
        else{
            $order_dept = auth()->user()->dept_id;
        }

        $status_appove = Repair::where('status', '=', 'หัวหน้าอนุมัติ')
            ->where('order_dept', 'LIKE', $order_dept.'%')
            ->count();
        $status_check = Repair::where('status', '=', 'รอตรวจสอบ')
            ->where('order_dept', 'LIKE', $order_dept.'%')
            ->count();

        if (Auth::User()->isLeader()) {
            $status_action = Repair::where('status', '=', 'ดำเนินการ')
            ->where('order_dept', 'LIKE', $order_dept.'%')
            ->count();
        }else {
            $status_action = Repair::where('status', '=', 'ดำเนินการ')
            ->where('order_dept', 'LIKE', $order_dept.'%')
            ->where('technician_name', 'LIKE', '%'.auth()->user()->emp_id.'%')
            ->count();
        }

        $dept_select = Department::where('dept_id', 'LIKE', $order_dept.'%')->get(); 
        return view('repair.action', compact('dept_select', 'status_appove', 'status_action', 'status_check'));
            // ->with('repair', $repair);
    } 

    public function allSelected($id){
        $repairs = Repair::where('order_id', '=', $id)->first();
        $tech_name =  json_decode($repairs->technician_name, true);
        $tech_detail =  json_decode($repairs->technician_detail, true);
        $ap_detail =  json_decode($repairs->approve_detail, true);
        $user_detail =  json_decode($repairs->user_comment, true);

        $whereCondition = [
            ['dept_id', '=',  $repairs->order_dept],
            ['emp_status', '=', 1],
        ];
        
        $car = DB::table('car_main')->get();
        $repair_type = DB::table('repair_type')->get();
        $empname_parent = Employee::where($whereCondition)->get();
        $dept_parent = Department::where('dept_id', '=', $repairs->dept_id)->first();
        $order_dept = Department::where('dept_id', '=', $repairs->order_dept)->first();
        $status = self::get_leave_status($repairs->status);

        return compact('repairs', 'repair_type', 'dept_parent', 'order_dept', 'empname_parent', 'tech_name', 'tech_detail', 'ap_detail', 'user_detail', 'car', 'status');
    }

    public function show($id){
        $datas = self::allSelected($id);
        
        return view('repair.detail', $datas);
    }

    public function approve_form($id){ 
        $datas = self::allSelected($id);
        return view('repair.approve_form', $datas);
    }

    public function report_form($id){ 
        $datas = self::allSelected($id);
        return view('repair.report_form', $datas);
    }

    public function check_form($id){ 
        $datas = self::allSelected($id);
        return view('repair.check_form', $datas);
    }

 
// ******************************  Search Table  ***************************************** //

    public function autoSearch(Request $request){
        $search = $request->get('search');
        $location = $request->get('location') ? $request->get('location') : '';
        $result = DB::table('repair_location')->where('location', $location)
            ->where(function ($query) use ($search) {
                $query->where('address', 'like', '%'.$search.'%')->orWhere('class', 'like', '%'.$search.'%');
            })
            ->orderBy('class', 'ASC')
            ->orderBy('location_id', 'ASC')
            // ->take(10)
            ->get(['class', 'address']);

        // dd($result);
        return response()->json($result);
    }

    //------------------------ Once page -----------------------------
    public function searchOnce(Request $request){
        if ($request->ajax()) {
            $doc_date = self::dateMonthFormat($request->doc_date);
            $_doc_date = self::dateBetweenFormat($request->doc_date);

            $data = Repair::where('id', '<>', '');
            $records = $data->select('*')
                ->whereBetween('order_date',[ $_doc_date['date_start'] , $_doc_date['date_end'] ])
                ->where(function ($query) use ($request) {
                    if ($request->status_category != "") { $query->where('status', '=', $request->status_category); }
                    if ($request->dept_category != "") { $query->where('order_dept', '=', $request->dept_category); }
                }) 
                ->where('user_id', '=', auth()->user()->emp_id)
                ->orderBy('id', 'DESC')
                ->get();

            $totalRecords = count($records);
            $rows = [];

            foreach ($records as $rec) {
                $day = Carbon::parse($rec->order_date)->format('d');
                $month = Carbon::parse($rec->order_date)->locale('th_TH')->isoFormat('MMMM');
                $year = Carbon::parse($rec->order_date)->format('Y') + 543;

                $status = self::manageNewStatus($rec->status);
                $action = self::manageEditor($rec->status, $rec->order_id, "Once", '');
                $techData = (array)json_decode($rec->technician_name, true);
                $depts = Department::where('dept_id', '=', $rec->order_dept)->first();
                $depts_user = Department::where('dept_id', '=', $rec->dept_id)->first();

                $user = Auth::User()->findEmployee( $rec->user_id);
                if ($user->nickname) {
                    $nickname = ' ( ' . $user->nickname . ' )';
                } else {
                    $nickname = '';
                }
                $rows[] = array(
                    "order_id" => $rec->order_id,
                    "order_dept" => $depts->dept_name,
                    "order_type" => $rec->order_type,
                    "order_address" => $rec->order_address,
                    "order_tool" => $rec->order_tool,
                    "order_date" =>  $day.' '.$month.' '.$year,
                    "status" => $status,
                    "action" => $action,
                );
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    //----------------------- Approve page -------------------------- 
    public function searchApprove(Request $request){
        $users = auth()->user();
        $dept_id = self::deptFormat($users->dept_id);
        if ($request->ajax()) {
            $records = [];
            $users = self::getAuthorizeUserLevel2(); 
            $data = Repair::where('id', '<>', '');
            if (Auth::User()->roleAdmin()) {
                $records = $data->select('*')->whereIn('status', ['รออนุมัติ', 'ผ่านการตรวจสอบ'])
                    ->orderBy('id', 'DESC')
                    ->get();
            }
            else{
                if (Auth::User()->isManager()) {
                    $records = $data->select('*')->whereIn('status', ['รออนุมัติ', 'ผ่านการตรวจสอบ'])
                        ->whereIn('user_id', $users)
                        ->orderBy('id', 'DESC')
                        ->get();
                } 
                else if(Auth::User()->isManagerHelper()) {
                    $records = $data->select('*')->whereIn('status', ['รออนุมัติ', 'ผ่านการตรวจสอบ'])
                    ->where('dept_id', 'LIKE', $dept_id.'%')
                    ->orderBy('id', 'DESC')
                    ->get();
                }
            }

            $totalRecords = count($records);
            $rows = [];
            
            foreach ($records as $rec) {
                $day = Carbon::parse($rec->order_date)->format('d');
                $month = Carbon::parse($rec->order_date)->locale('th_TH')->isoFormat('MMMM');
                $year = Carbon::parse($rec->order_date)->format('Y') + 543;
                $status = self::manageNewStatus($rec->status);
                $depts = Department::where('dept_id', '=', $rec->order_dept)->first();
                $depts_user = Department::where('dept_id', '=', $rec->dept_id)->first();

                $user = Auth::User()->findEmployee( $rec->user_id);
                if ($user->nickname) {$nickname = ' ( ' . $user->nickname . ' )';}
                else {$nickname = '';}
                $action = self::manageEditor($rec->status, $rec->order_id, "Leader", 1);

                $rows[] = array(
                    "order_id" => $rec->order_id,
                    "order_dept" => $depts->dept_name,
                    "order_type" => $rec->order_type,
                    "user_id" => $user->name.' '.$user->surname.' '.$nickname,
                    // "dept_id" => $rec->dept_id,
                    "dept_id" => $depts_user->dept_name,
                    "order_date" =>  $day.' '.$month.' '.$year,
                    "status" => $status,
                    "action" => $action,
                );
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);dd($request->page);
        }
    }
    
    public function search(Request $request){
        if ($request->ajax()) {
            $emp = auth()->user();
            $dept_id = self::deptFormat($emp->dept_id);
            $doc_date = self::dateMonthFormat($request->doc_date);
            $_doc_date = self::dateBetweenFormat($request->doc_date);
            $users = self::getAuthorizeUserLevel2();
            array_push($users, $emp->emp_id);
            
            $records = [];
            $data = Repair::where('id', '<>', '')->where('status', '<>', 'รออนุมัติ');
            // $totalRecords = $data->select('count(*) as allcount')->count();
            if (Auth::User()->roleAdmin()) {
                $records = $data->select('*')
                ->whereBetween('order_date',[ $_doc_date['date_start'] , $_doc_date['date_end'] ])
                ->where(function ($query) use ($request) {
                    if ($request->status_category != "") { $query->where('status', $request->status_category); }
                    if ($request->dept_category != "") { $query->where('order_dept', '=', $request->dept_category); }
                })
                ->orderBy('id', 'DESC')
                ->get();
            }else{
                if (Auth::User()->isManager()) {
                    $records = $data->select('*')
                        ->whereIn('user_id', $users)
                        ->whereBetween('order_date',[ $_doc_date['date_start'] , $_doc_date['date_end'] ])
                        ->where(function ($query) use ($request) {
                            if ($request->status_category != "") { $query->where('status', '=', $request->status_category); }
                            if ($request->dept_category != "") { $query->where('order_dept', '=', $request->dept_category); }
                        })
                        ->orderBy('id', 'DESC')
                        ->get();
                } 
                else if(Auth::User()->isManagerHelper()) {
                    $records = $data->select('*')
                        ->where('dept_id', 'LIKE', $dept_id.'%')
                        ->whereBetween('order_date',[ $_doc_date['date_start'] , $_doc_date['date_end'] ])
                        ->where(function ($query) use ($request) {
                            if ($request->status_category != "") { $query->where('status', '=', $request->status_category); }
                            if ($request->dept_category != "") { $query->where('order_dept', '=', $request->dept_category); }
                        })
                        ->orderBy('id', 'DESC')
                        ->get();
                }
            }

            $totalRecords = count($records);
            
            $rows = [];
            foreach ($records as $rec) {
                $day = Carbon::parse($rec->order_date)->format('d');
                $month = Carbon::parse($rec->order_date)->locale('th_TH')->isoFormat('MMMM');
                $year = Carbon::parse($rec->order_date)->format('Y') + 543;

                $status = self::manageNewStatus($rec->status);
                $depts = Department::where('dept_id', '=', $rec->order_dept)->first();
                $depts_user = Department::where('dept_id', '=', $rec->dept_id)->first();

                $user = Auth::User()->findEmployee( $rec->user_id);
                if ($user->nickname) {
                    $nickname = ' ( ' . $user->nickname . ' )';
                } else {
                    $nickname = '';
                }

                $action = self::manageEditor($rec->status, $rec->order_id, "Leader", '');
                $rows[] = array(
                    "order_id" => $rec->order_id,
                    "order_dept" => $depts->dept_name,
                    "order_type" => $rec->order_type,
                    "user_id" => $user->name.' '.$user->surname.' '.$nickname,
                    "dept_id" => $depts_user->dept_name,
                    "order_date" =>  $day.' '.$month.' '.$year,
                    "status" => $status,
                    "action" => $action,
                );
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);dd($request->page);
        }
    }

    // ----------------------- Action page -----------------------------
    public function searchAction(Request $request){
        if ($request->ajax()) {
            $start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->endOfMonth()->format('Y-m-d');

            $doc_date = self::dateMonthFormat($request->doc_date);
            $order_dept = self::deptFormat(auth()->user()->dept_id);
            $_doc_date = self::dateBetweenFormat($request->doc_date);
            $users = auth()->user();
  
            $data = Repair::where('id', '<>', '');
            // $totalRecords = $data->select('count(*) as allcount')->count();
            $records = [];

            if (Auth::User()->roleAdmin() || auth()->user()->emp_id == 500383) {
                $records = $data->select('*')
                    ->where(function ($query) use ($request, $_doc_date, $start_date, $end_date) {
                        if ($request->status_ul == "") {
                            if ($request->doc_date != "") { $query->whereBetween('order_date',[ $_doc_date['date_start'] , $_doc_date['date_end'] ]); }
                            else{$query->whereBetween('order_date', [$start_date, $end_date]);}
                        }else{
                            if ($request->doc_date != "") { $query->whereBetween('order_date',[ $_doc_date['date_start'] , $_doc_date['date_end'] ]); }
                            else{ $query->whereDate('order_date', '<=', $_doc_date['date_end'] ); }
                        }
                    })
                    ->where(function ($query) use ($request) {
                        if ($request->status_ul != "") { $query->where('status', '=', $request->status_ul); }
                        elseif ($request->status_category != "") { $query->where('status', '=', $request->status_category); }
                        if ($request->dept_category != "") { $query->where('order_dept', '=', $request->dept_category); }
                    })
                    ->orderBy('id', 'DESC')
                    ->get();
            }
            else {
                $records = $data->select('*')
                    ->where('order_dept', 'LIKE', $order_dept.'%')
                    ->where(function ($query) use ($request, $_doc_date, $start_date, $end_date) {
                        if ($request->status_ul == "") {
                            if ($request->doc_date != "") { $query->whereBetween('order_date',[ $_doc_date['date_start'] , $_doc_date['date_end'] ]); }
                            else{$query->whereBetween('order_date', [$start_date, $end_date]);}
                        }else{
                            if ($request->doc_date != "") { $query->whereBetween('order_date',[ $_doc_date['date_start'] , $_doc_date['date_end'] ]); }
                            else{ $query->whereDate('order_date', '<=', $_doc_date['date_end'] ); }
                        }
                    })
                    ->where(function ($query) use ($request) {
                        if ($request->status_ul != "") { $query->where('status', '=', $request->status_ul);}
                        elseif ($request->status_category != "") { $query->where('status', '=', $request->status_category); }
                        if ($request->dept_category != "") { $query->where('order_dept', '=', $request->dept_category); }
                    })
                    ->orderBy('id', 'DESC')
                    ->get();
            }

            $totalRecords = count($records);
        
            $rows = [];
            foreach ($records as $rec) {
                $tName ='';
                $techData = (array)json_decode($rec->technician_name, true);
                foreach ($techData as $techName ) {$tName .= $techName['emp_id'].' ';}
                $day = Carbon::parse($rec->order_date)->format('d');
                $month = Carbon::parse($rec->order_date)->locale('th_TH')->isoFormat('MMMM');
                $year = Carbon::parse($rec->order_date)->format('Y') + 543;

                $status = self::manageNewStatus($rec->status);
                $withdraw = self::manageWithdraw($rec->status, $rec->order_id);
                $action = self::manageEditor($request->status_ul, $rec->order_id, "Action", $tName);
                
                $depts = Department::where('dept_id', '=', $rec->order_dept)->first();
                $depts_user = Department::where('dept_id', '=', $rec->dept_id)->first();
                $user = Auth::User()->findEmployee( $rec->user_id );

                if ($user->nickname) {$nickname = ' ( ' . $user->nickname . ' )';} 
                else { $nickname = ''; }

                $rows[] = array(
                    "order_id" => $rec->order_id,
                    "order_dept" => $depts->dept_name,
                    "order_type" => $rec->order_type,
                    "user_id" => $user->name.' '.$user->surname.' '.$nickname,
                    "dept_id" => $depts_user->dept_name,
                    "order_date" =>  $day.' '.$month.' '.$year,
                    "withdraw" => $withdraw,
                    "status" => $status,
                    "action" => $action,
                );
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

// ******************************  Format  ***************************************** //
    
    public function deptFormat($dept){
        $res = '';
        $_data = Department::where('dept_id', $dept)->first();
        if ( $_data->level == 1 ) {
            $res = substr($dept , 0, 3);
        }
        elseif ($_data->level == 2 ){
            $res = substr($dept , 0, 5);
        }
        else{
            $res = $dept;
        }
        return $res;
    }

    public function dateMonthFormat($date){
        $date_month = '';
        if ($date == ""){
            $date_month = date('Y-m');
        }
        else if ($date != "") {
            $d = explode("/", $date); 
            $date_month =  date('Y-m', strtotime($d[1]."-".$d[0]));
        }
        return $date_month;
    }

    public function dateBetweenFormat($date){
        $date_start = date('Y-m-01');
        $date_end = date('Y-m-t');

        if ($date != "") {
            if (str_contains($date, "ถึง")) {
                $d = explode("ถึง", $date);
                $dStart = explode("/", trim($d[0]) ); 
                $dEnd = explode("/", trim($d[1]) ); 

                $date_start = date('Y-m-d', strtotime($dStart[2]."-".$dStart[1]."-".$dStart[0]));
                $date_end = date('Y-m-d', strtotime($dEnd[2]."-".$dEnd[1]."-".($dEnd[0]) ));
            } 
            else{
                $dStart = explode("/", $date); 
                $date_start = date('Y-m-d', strtotime($dStart[2]."-".$dStart[1]."-".$dStart[0]));
                $date_end = date('Y-m-d', strtotime($dStart[2]."-".$dStart[1]."-".($dStart[0])));
            }
        }

        $result = array(
            'date_start' => $date_start,
            'date_end' => $date_end
        );
        return $result;
    }

// ******************************  Manage  ***************************************** //

    public static function getAuthorizeUserLevel2(){
        // เรียกข้อมูลพนักงานที่อยู่ภายใต้สิทธิ์การอนุมัติของเรา
        $auth = auth()->user();
        $emp = [];
        $data = AuthorizationManual::where('auth2', '=', $auth->emp_id)->orderBy('emp_id', 'asc')->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $value) {
                $emp[] = $value->emp_id;
            }
        }

        $data = DB::table('authorization as a')->leftJoin('employee as e', 'a.dept_id', '=', 'e.dept_id')
            ->where('a.auth2', '=', $auth->emp_id)
            ->orderBy('a.dept_id', 'asc')
            ->get(['e.emp_id']);
        if ($data->isNotEmpty()) {
            foreach ($data as $value) {
                $emp[] = $value->emp_id;
            }
        }

        $dept = Department::where('dept_id', $auth->dept_id)->first();
        if ($dept->level <= 2) {
            $data = DB::table('authorization as a')->leftJoin('employee as e', 'a.dept_id', '=', 'e.dept_id')
            ->where('a.auth', '=', $auth->emp_id)
            ->orWhere('a.auth2', '=', $auth->emp_id)
            ->orderBy('a.dept_id', 'asc')
                ->get(['e.emp_id']);
            if ($data->isNotEmpty()) {
                foreach ($data as $value) {
                    $emp[] = $value->emp_id;
                }
            }
        }

        if ($auth->emp_id == "500383") {
            // พี่จิ๊บ
            $data = DB::table('employee')->where('dept_id', 'LIKE', 'A03%')->orWhere('dept_id', 'LIKE', 'A0110%')->get(['emp_id']);
            if ($data->isNotEmpty()) {
                foreach ($data as $value) {
                    $emp[] = $value->emp_id;
                }
            }
        }
        elseif ($auth->emp_id == "580073") {
            // พี่เพียว
            $data = DB::table('employee')->where('dept_id', 'LIKE', 'A02%')->get(['emp_id']);
            if ($data->isNotEmpty()) {
                foreach ($data as $value) {
                    $emp[] = $value->emp_id;
                }
            }
        }
        return array_unique($emp);
    }

    public function get_leave_status($id){
        $status = array(
            [
                "id" => "E",
                "name" => "รออนุมัติ",
                "text" => "white",
                "color" => "warning",
                "bg" => "bg-warning",
                "badge" => "badge-soft-warning rounded-pill",
            ],
            [
                "id" => "A1",
                "name" => "หัวหน้าอนุมัติ",
                "text" => "white",
                "color" => "info",
                "bg" => "bg-info",
                "badge" => "badge-soft-info rounded-pill",
            ],
            [
                "id" => "A2",
                "name" => "ดำเนินการ",
                "text" => "white",
                "color" => "secondary",
                "bg" => "bg-secondary",
                "badge" => "badge-soft-secondary rounded-pill",
            ],
            [
                "id" => "S",
                "name" => "รอตรวจสอบ",
                "text" => "white",
                "color" => "primary",
                "bg" => "bg-primary",
                "badge" => "badge-soft-primary rounded-pill",
            ],
            [
                "id" => "C",
                "name" => "ผ่านการตรวจสอบ",
                "text" => "white",
                "color" => "blue",
                "bg" => "bg-blue",
                "badge" => "badge-soft-blue rounded-pill",
            ],
            [
                "id" => "S",
                "name" => "เสร็จสิ้น",
                "text" => "white",
                "color" => "success",
                "bg" => "bg-success",
                "badge" => "badge-soft-success rounded-pill",
            ],
        );
        if ($id == null) {
            return $status;
        } else {
            $status[] = [
                "id" => "C1",
                "name" => "ยกเลิกโดยผู้แจ้ง",
                "text" => "white",
                "color" => "danger",
                "bg" => "bg-danger",
                "badge" => "badge-soft-danger rounded-pill",
            ];
            $status[] = [
                "id" => "C2",
                "name" => "ยกเลิกโดยหัวหน้า",
                "text" => "white",
                "color" => "danger",
                "bg" => "bg-danger",
                "badge" => "badge-soft-danger rounded-pill",
            ];
            $status[] = [
                "id" => "C3",
                "name" => "ยกเลิกโดยผู้รับงาน",
                "text" => "white",
                "color" => "danger",
                "bg" => "bg-danger",
                "badge" => "badge-soft-danger rounded-pill",
            ];
            $key = array_search($id, array_column($status, 'name'));
            return $status[$key];
        }
    }

    public function manageNewStatus($status){
        $res = '';
        switch ($status) {
            case 'รออนุมัติ':
                $res = '<span class="badge bg-warning">รออนุมัติ</span>';
                break;
            case 'หัวหน้าอนุมัติ':
                $res = '<span class="badge bg-info">หัวหน้าอนุมัติ</span>';
                break;
            case 'ดำเนินการ':
                $res = '<span class="badge bg-secondary">ดำเนินการ</span>';
                break;
            case 'รอตรวจสอบ':
                $res = '<span class="badge bg-primary">รอตรวจสอบ</span>';
                break;
            case 'ผ่านการตรวจสอบ':
                $res = '<span class="badge bg-blue">ผ่านการตรวจสอบ</span>';
                break;
            case 'เสร็จสิ้น':
                $res = '<span class="badge bg-success">เสร็จสิ้น</span>';
                break;
            case 'ยกเลิกโดยผู้แจ้ง':
                $res = '<span class="badge bg-danger">ยกเลิก</span>';
                break;
            case 'ยกเลิกโดยหัวหน้า':
                $res = '<span class="badge bg-danger">ยกเลิก</span>';
                break;
            case 'ยกเลิกโดยผู้รับงาน':
                $res = '<span class="badge bg-danger">ยกเลิก</span>';
                break;
            

        }
        return $res;
    }

    public function manageEditor($status, $id, $where, $tName){
        $detail = '';
        $s_div = '<div>';
        $e_div = '</div>';

        // Manage Button
        if ($where == 'Once') {
            $detail .= '<a class="action-icon" href="'.url('repair/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>';
  
            if ($status == 'รออนุมัติ') { 
                $detail .= '<a class="action-icon" href="'.url('repair/repair-form-edit', $id).'" title="แก้ไข"><i class="mdi mdi-brightness-5"></i></a>
                <a class="action-icon" href="javascript:void(0);" onclick="deleteLeaveRecordConfirmation(\''.$id.'\')" title="ยกเลิก"><i class="mdi mdi-backspace-outline"></i></a>';
            }
            else if ( $status == 'หัวหน้าอนุมัติ' ){
                if ( Auth::User()->isLeader() ) {
                    $detail .= '<a class="action-icon" href="'.url('repair/repair-form-edit', $id).'" title="แก้ไข"><i class="mdi mdi-brightness-5"></i></a>
                    <a class="action-icon" href="javascript:void(0);" onclick="deleteLeaveRecordConfirmation(\''.$id.'\')" title="ยกเลิก"><i class="mdi mdi-backspace-outline"></i></a>';
                }
            }
            else if ($status == 'ผ่านการตรวจสอบ') {
                $detail .= '<a class="action-icon" href="'.url('repair/check_form', $id).'" title="ตรวจรับงาน"><i class="fe-check"></i></a>';
            }
        }
        else if ( $where == 'Leader' ){
            $detail .= '<a class="action-icon" href="'.url('repair/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>';

            if ($status == 'รออนุมัติ') {
                $detail .= '<a class="action-icon" href="'.url('repair/approve_form', $id).'" title="อนุมัติงาน"><i class="mdi mdi-checkbox-marked-outline"></i></a>
                <a class="action-icon" href="javascript:void(0);" onclick="deleteLeaveRecordConfirmation(\''.$id.'\')" title="ยกเลิก"><i class="mdi mdi-backspace-outline"></i></a>';
            }
            else if ($status == 'ผ่านการตรวจสอบ' && $tName == 1) {
                $detail .= '<a class="action-icon" href="'.url('repair/check_form', $id).'" title="ตรวจรับงาน"><i class="mdi mdi-checkbox-marked-outline"></i></a>';
            }
            
        }
        else if ( $where == 'Action' ){
            $detail .= '<a class="action-icon" href="'.url('repair/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>';
            if ( $status == 'หัวหน้าอนุมัติ' ){
                if ( Auth::User()->isLeader() || Auth::User()->roleAdmin()) {
                    $detail .= '<a class="action-icon" href="'.url('repair/approve_form', $id).'" title="รับงาน"><i class="fe-check"></i></a>
                    <a class="action-icon" href="#" data-bs-toggle="modal" data-bs-target="#cancelModal" onclick="cancelModal(\''.$id.'\')" title="ยกเลิก"><i class="mdi mdi-backspace-outline"></i></a>
                    ';

                    // <a class="action-icon" href="javascript:void(0);" onclick="deleteLeaveRecordConfirmation(\''.$id.'\')" title="ยกเลิก"><i class="mdi mdi-backspace-outline"></i></a>
                }
            }
            else if ($status == 'ดำเนินการ') {
                if ( Auth::User()->roleAdmin() || Auth::User()->isLeader() ) {
                    $detail .= '<a class="action-icon" href="'.url('repair/approve_form_edit', $id).'" title="แก้ไข"><i class="mdi mdi-brightness-5"></i></a>
                    <a class="action-icon" href="'.url('repair/report_form', $id).'" title="รายงาน"><i class="mdi mdi-check-box-multiple-outline"></i></a>';
                }else{
                    if ( str_contains( $tName, auth()->user()->emp_id )) {
                        $detail .= '<a class="action-icon" href="'.url('repair/report_form', $id).'" title="รายงาน"><i class="mdi mdi-check-box-multiple-outline"></i></a>';
                    }
                }
            }
            else if ( $status == 'รอตรวจสอบ' ){
                if ( Auth::User()->isLeader() || Auth::User()->roleAdmin()) {
                    $detail .= '<a class="action-icon" href="'.url('repair/check_form', $id).'" title="ตรวจสอบ"><i class="mdi mdi-briefcase-check-outline"></i></a>';
                }
            }
            
        }

        $result = $s_div.$detail.$e_div;
        return $result;
    }

    public function manageWithdraw($status_ul, $id){
        $_count = Withdraw::where('repair_order_id', $id)->count(); 
        $_count = ($_count)? $_count : 'สร้างใบเบิก';
        $valid_statuses = ['ดำเนินการ', 'รอตรวจสอบ', 'ผ่านการตรวจสอบ', 'เสร็จสิ้น'];

        if (in_array($status_ul, $valid_statuses)) {
            $count = '<a href="'.url('repair/withdraw-list', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-clipboard-list-outline"></i> '.$_count.'</a>';
        } else {
            $count = '<a class="action-icon" title="ดูรายละเอียด"><i class="mdi mdi-clipboard-list-outline"></i></a>';
        }

        return $count;
    }

// *******************************  Insert Update  **************************************** //

    public function store(Request $request){
        $user = auth()->user();
        if ($request->status == 'INS') {
            $autoIncId = 1;
            $gen_dept_id = "";
            $tool = '';
            $status = '';

            // $currentYear = date('Y'); // gets current year
            // $data = Repair::whereYear('order_date', $currentYear)->first();            

            $data = Repair::where('id', '<>', '')->first();
            if ($data) {
                $categoryId = Repair::orderByDesc('id')->first();
                // $categoryId = Repair::whereYear('order_date', $currentYear)->orderByDesc('id')->count();
                $autoIncId = $categoryId + 1;
            } 
            else{
                $autoIncId = 1;
            }

            switch ($request->order_dept) {
                case 'A03050100':
                    $gen_dept_id = "BE".date('ym')."000".$autoIncId;  
                    $tool = $request->order_tool;
                    break;
                case 'A03050200':
                    $gen_dept_id = "BC".date('ym')."000".$autoIncId; 
                    foreach ($request->order_tool as $t) {
                        $tool.= $t.',';
                    }
                    break;
                case 'A03060100':
                    $gen_dept_id = "MT".date('ym')."000".$autoIncId; 
                    $tool = $request->order_tool;
                    break;
                case 'A01100100':
                    $gen_dept_id = "IT".date('ym')."000".$autoIncId; 
                    $tool = $request->order_tool;
                    break;
            }

            if($request->order_type == 'other' && $request->order_other != ''){
                $type = $request->order_other;
            }else{
                $type = $request->order_type;
            }

            if($request->order_location == 'other' && $request->address_other != ''){
                $address = $request->address_other;
            }else{
                $address = "อาคาร".$request->order_location .' '. $request->order_address;
            }

            // Setting image
            $img_name = array();
            if ($request->file('order_image')) {
                $i = 1;
                $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/repair/';
                foreach($request->file('order_image') as $image) {
                    $filename = $gen_dept_id.'_'.$i.'.'.$image->extension();
                    $img = Image::make($image->path());
                    $img->resize(500, 500, function ($constraint) {$constraint->aspectRatio();})
                        ->save($destinationPath.$filename);

                    array_push($img_name, $filename);
                    $i++;
                }
            }
            
            // INSERT Data
            $repair = new Repair();
            
            if( Auth::User()->isManagerHelper() ){
                $repair->status = "หัวหน้าอนุมัติ";
                $repair->approve_name = $user->emp_id;
                $repair->approve_date = date('Y-m-d H:i:s');
            }else{
                $repair->status = "รออนุมัติ";
            }
            if ($request->car_id) {$repair->car_id = $request->car_id;}
            if ($request->car_mile) {$repair->car_mile = $request->car_mile;}

            $repair->order_id = $gen_dept_id;
            $repair->order_address = $address;
            $repair->order_type = $type;
            $repair->order_dept = $request->order_dept;
            $repair->order_date = $request->order_date;
            $repair->order_detail = $request->order_detail;
            $repair->order_image = json_encode($img_name, JSON_FORCE_OBJECT);
            $repair->order_tool = $tool;
            $repair->user_id = $user->emp_id;
            $repair->dept_id = $user->dept_id;
            $repair->save();
            alert()->success('เพิ่มข้อมูลเรียบร้อย');
            return redirect('repair/repair');
        }
        
        elseif ($request->status == 'EDIT') {
            $approve_date = '';
            $approve_name = '';
            $tool = '';
            $status = '';
            $img_name = array();
            // Setting image
            if ($request->order_image) {
                $i = 1;
                $img_name = array();
                $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/repair/';
                foreach($request->file('order_image') as $image) {
                    $filename = $request->order_id.'_'.$i.'.'.$image->extension();
                    $img = Image::make($image->path());
                    $img->resize(500, 500, function ($constraint) {$constraint->aspectRatio();})
                        ->save($destinationPath.$filename);

                    array_push($img_name, $filename);
                    $i++;
                }
            }

            if($request->order_dept == 'A03050200'){
                foreach ($request->order_tool as $t) { $tool.= $t.','; }
            }else{
                $tool = $request->order_tool;
            }

            if($request->order_type == 'other' && $request->order_other != ''){
                $order_type = $request->order_other;
            }else{
                $order_type = $request->order_type;
            }

            if($request->order_location == 'other' && $request->address_other != ''){
                $address = $request->address_other;
            }else{
                $address = "อาคาร".$request->order_location .' '. $request->order_address;
            }

            if( Auth::User()->isManagerHelper()){
                if (count($img_name) > 0) {
                    Repair::where('id', '=', $request->id)->update([
                        "order_address" => $address,
                        "order_date" => $request->order_date,
                        "order_detail" => $request->order_detail,
                        "order_image" => json_encode($img_name, JSON_FORCE_OBJECT),
                        "car_id" => $request->car_id,
                        "car_mile" => $request->car_mile,
                        "order_type" => $order_type,
                        "order_tool" => $tool,
                        'approve_date' => date('Y-m-d H:i:s'),
                        'approve_name' => $user->emp_id,
                        "status" => "หัวหน้าอนุมัติ",
                    ]);
                } else {
                    Repair::where('id', '=', $request->id)->update([
                        "order_address" => $address,
                        "order_date" => $request->order_date,
                        "order_detail" => $request->order_detail,
                        "car_id" => $request->car_id,
                        "car_mile" => $request->car_mile,
                        "order_type" => $order_type,
                        "order_tool" => $tool,
                        'approve_date' => date('Y-m-d H:i:s'),
                        'approve_name' => $user->emp_id,
                        "status" => "หัวหน้าอนุมัติ",
                    ]);
                }
                
            }else{
                if (count($img_name) > 0) {
                    Repair::where('id', '=', $request->id)->update([
                        "order_address" => $address,
                        "order_date" => $request->order_date,
                        "order_detail" => $request->order_detail,
                        "order_image" => json_encode($img_name, JSON_FORCE_OBJECT),
                        "car_id" => $request->car_id,
                        "car_mile" => $request->car_mile,
                        "order_type" => $order_type,
                        "order_tool" => $tool,
                        "status" => "รออนุมัติ",
                    ]);
                } else {
                    Repair::where('id', '=', $request->id)->update([
                        "order_address" => $address,
                        "order_date" => $request->order_date,
                        "order_detail" => $request->order_detail,
                        "car_id" => $request->car_id,
                        "car_mile" => $request->car_mile,
                        "order_type" => $order_type,
                        "order_tool" => $tool,
                        "status" => "รออนุมัติ",
                    ]);
                }
            }

            alert()->success('แก้ไขข้อมูลเรียบร้อย');
            return redirect('repair/repair');
        }
    }
    
    public function approve_update(Request $request){
        // dd($request->approve_date,);
        Repair::where('id', '=', $request->id)->update([
            'approve_date' => $request->approve_date,
            'approve_name' => auth()->user()->emp_id,
            "status" => "หัวหน้าอนุมัติ",
        ]);

        alert()->success('อนุมัติรายการเรียบร้อย');
        return redirect('repair/approve');
    }

    public function work_update(Request $request){
        for ($i=0; $i < count($request->technician_name); $i++) { 
            $segments = Str::of($request->technician_name[$i], 6)->split('/[\s,]+/');
            $tech[] = array(
                'emp_id' => $segments[0],  
                'name' => $segments[1],
                'surname' => $segments[2],
            ); 
        }
        $encode_tech = json_encode($tech, JSON_FORCE_OBJECT);
 
        if ($request->order_address) {
            $address = '';
            if($request->order_address == 'other' && $request->order_other != ''){
                $address = $request->order_other;
            }
            else{
                $address = $request->order_address;
            }
            Repair::where('id', '=', $request->id)->update([
                'order_address' => $address,
                'technician_name' => $encode_tech,
                'start_date' => $request->start_date,
                'manager_date' => $request->approve_date,
                'manager_id' => auth()->user()->emp_id,
                'manager_detail' => $request->manager_detail,
                "status" => "ดำเนินการ",
            ]);
        }else{
            Repair::where('id', '=', $request->id)->update([
                'technician_name' => $encode_tech,
                'start_date' => $request->start_date,
                'manager_date' => $request->approve_date,
                'manager_id' => auth()->user()->emp_id,
                'manager_detail' => $request->manager_detail,
                "status" => "ดำเนินการ",
            ]);
        }
        
        alert()->success('บันทึกรายการเรียบร้อย');
        return redirect('repair/action');

    }

    public function report_update(Request $request){
        $repair = Repair::where('id', '=', $request->id)->get();
        $tech = [];

        foreach ($repair as $res) {
            $tech = json_decode($res->technician_detail, true) ;
        }
        if ($request->status == 'รอตรวจสอบ'){
            $techData = array(
                'emp_id' => auth()->user()->emp_id,
                'name' => auth()->user()->name.' '. auth()->user()->surname,
                'start_date' => $request->sdate,  
                'continue_date' => '',
                's_job' => $request->s_job,
                's_tool' => $request->s_tool,
                'detail' => $request->technician_detail,
                'end_date' => $request->end_date,
            ); 
        }elseif ($request->status == 'ดำเนินการ') {
            $techData = array(
                'emp_id' => auth()->user()->emp_id,
                'name' => auth()->user()->name.' '. auth()->user()->surname,
                'start_date' => $request->sdate,  
                'continue_date' => $request->continue_date ,
                's_job' => '',
                's_tool' => '',
                'detail' => ($request->s_comment) ? $request->s_comment.' '.$request->technician_detail : $request->technician_detail,
                'end_date' => '',
            ); 
        }
        if ($tech){
            array_push($tech, $techData);
        }else{
            $tech[] = $techData;
        }
        

        Repair::where('id', '=', $request->id)->update([
            'technician_detail' => json_encode($tech, JSON_FORCE_OBJECT),
            'end_date' => $request->end_date,
            "status" => $request->status,
        ]);

        alert()->success('บันทึกเรียบร้อย');
        return redirect('repair/action');
    }

    public function check_update(Request $request){
        $Data= [];
        $Datas = array(
            'emp_id' => auth()->user()->emp_id,
            'name' => auth()->user()->name.' '. auth()->user()->surname,
            'date' => $request->sdate,  
            'detail' => $request->approve_detail,
        ); 
        $Data[] = $Datas;

        if ($request->status == 'ผ่านการตรวจสอบ') {
            Repair::where('id', '=', $request->id)->update([
                'price' => $request->price,
                'approve_detail' => json_encode($Data, JSON_FORCE_OBJECT),
                "status" => $request->status,
            ]);
            alert()->success('บันทึกเรียบร้อย');
            return redirect('repair/action');
        }
        else if ($request->status == 'เสร็จสิ้น') {
            Repair::where('id', '=', $request->id)->update([
                'user_comment' => json_encode($Data, JSON_FORCE_OBJECT),
                "status" => $request->status,
            ]);
            alert()->success('บันทึกเรียบร้อย');
            if (Auth::User()->isManager()) {
                return redirect('repair/approve');
            }else {
                return redirect('repair/repair');
            }
        }  
        else if ($request->status == 'ดำเนินการ') {
            Repair::where('id', '=', $request->id)->update([
                'approve_detail' => json_encode($Data, JSON_FORCE_OBJECT),
                "status" => $request->status,
            ]);
            alert()->success('บันทึกเรียบร้อย');
            return redirect('repair/action');
        } 
    }

    public function cancel(Request $request){
        $id = $request->id;
        $emp = auth()->user();
        if ($request->page == "profile") {
            Repair::where('order_id', '=', $id)->update([
                "status" => "ยกเลิกโดยผู้แจ้ง"
            ]);
        }
        elseif ($request->page == "approve") {
            Repair::where('order_id', '=', $id)->update([
                'approve_date' => date("Y-m-d"),
                'approve_name' => $emp->emp_id,
                "status" => "ยกเลิกโดยหัวหน้า"
            ]);
        }
        elseif ($request->page == "action") {
            Repair::where('order_id', '=', $id)->update([
                'manager_date' => now(),
                'manager_id' => $emp->emp_id,
                'manager_detail' => $request->manager_detail,
                "status" => "ยกเลิกโดยผู้รับงาน"
            ]);
            alert()->success('ยกเลิกรายการเรียบร้อย');
            return redirect('repair/action');
        }

        return response()->json(['success' => true, 'message' => 'ยกเลิกรายการเรียบร้อย']);

    }

}
