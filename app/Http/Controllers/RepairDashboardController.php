<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Repair;
use Carbon\Carbon;

class RepairDashboardController extends Controller
{
    public function dashboardAll(){
        $users = auth()->user();
        $_chart = self::charts(date('Y'));
        $order_dept = self::deptFormat($users->dept_id);
        $_detail = self::dashboardDetail(date('Y'), date('m'));

        $_year = DB::table('repair')->groupBy(DB::raw("YEAR(created_at)"))->first(['created_at']);
        $_month = DB::table('repair')->groupBy(DB::raw("MONTH(created_at)"))->get(['created_at']);
        // dd($_month);
        return view('repair.dashboard.index', [
            '_chart' => $_chart, 
            '_year' => $_year,
            '_month' => $_month,
            '_detail' => $_detail,
        ]);
    }

    public function charts($year){
        $_dept = ['A03050100', 'A03050200', 'A03060100', 'A01100100'];
        $order_dept = self::deptFormat(auth()->user()->dept_id);

        for ($i=0; $i < count($_dept); $i++) { 
            $_chart = [];
            if (Auth::User()->roleAdmin() || auth()->user()->emp_id == 500383) {
                for ($j=1; $j <= 12; $j++) { 
                    $data_chart = DB::table('repair')
                        ->whereYear('created_at', '=', $year)
                        ->whereMonth('created_at', '=', $j)
                        ->where('order_dept', $_dept[$i])
                        ->where('status', '<>', 'ยกเลิกโดยผู้แจ้ง' )
                        ->where('status', '<>', 'ยกเลิกโดยหัวหน้า' )
                        ->groupBy(DB::raw("MONTH(created_at)"))
                        ->count();
                    array_push($_chart, $data_chart);
                }
            }else{
                for ($j=1; $j <= 12; $j++) { 
                    $data_chart = DB::table('repair')
                        ->whereYear('created_at', '=', $year)
                        ->whereMonth('created_at', '=', $j)
                        ->where('order_dept', 'LIKE', $order_dept.'%')
                        ->where('order_dept', $_dept[$i])
                        ->where('status', '<>', 'ยกเลิกโดยผู้แจ้ง' )
                        ->where('status', '<>', 'ยกเลิกโดยหัวหน้า' )
                        ->groupBy(DB::raw("MONTH(created_at)"))
                        ->count();
                    array_push($_chart, $data_chart);
                }
            }
            if($_dept[$i] == "A03050100") {
                $a = $_chart;
            }
            else if($_dept[$i] == "A03050200") {
                $b = $_chart;
            }
            else if($_dept[$i] == "A03060100") {
                $c = $_chart;
            }
            else if($_dept[$i] == "A01100100") {
                $d = $_chart;
            }
        }
        return [$a, $b, $c, $d];
    }

    public function dashboardDetail($year, $month) {
        $_all = [];
        $_dept = ['A03050100', 'A03050200', 'A03060100', 'A01100100'];
        $_status2 = [['รออนุมัติ','หัวหน้าอนุมัติ'], ['ดำเนินการ'], ['รอตรวจสอบ', 'ผ่านการตรวจสอบ'], ['เสร็จสิ้น']];
        $_status_en2 = ['wait', 'process', 'check','success'];
        $order_dept = self::deptFormat(auth()->user()->dept_id);

        if (Auth::User()->roleAdmin() || auth()->user()->emp_id == 500383 ) {
            for ($i=0; $i < count($_dept); $i++) { 
                for ($x=0; $x < count($_status2); $x++) {
                    $data_all = DB::table('repair')->where('order_dept', $_dept[$i])
                        ->whereIn('status', $_status2[$x])
                        ->whereYear('created_at', '=', $year)
                        ->whereMonth('created_at', '=', $month)
                        ->count();
                    
                    $_all[$_dept[$i]][$_status_en2[$x]] = $data_all . '';
                }
            }
        }
        else {
            for ($i=0; $i < count($_dept); $i++) { 
                if (str_contains($_dept[$i], $order_dept)) {
                    for ($x=0; $x < count($_status2); $x++) { 
                        $data_all = DB::table('repair')->where('order_dept', $_dept[$i])
                            ->where('order_dept', 'LIKE', $order_dept.'%')
                            ->whereIn('status', $_status2[$x])
                            ->whereYear('created_at', '=', $year)
                            ->whereMonth('created_at', '=', $month)
                            ->count();
                        
                        $_all[$_dept[$i]][$_status_en2[$x]] = $data_all . '';
                    }
                }
                
            }
        }
        $res = array(
            'data' => $_all, 
        );
        return $res;
    }

    public function dashboardYear(Request $request) {
        
        if ($request->ajax()) {
            $d = explode("/", $request->dash_year); 
            $dash_year = $d[1];
            $dash_month = $d[0];

            $_chart = self::charts($dash_year);            
            $_month_year = self::dashboardDetail($dash_year, $dash_month);            
            return compact('_chart', '_month_year');
        }
    }

    public function dashboardDept()
    {
        $users = auth()->user();
        $order_dept = self::deptFormat($users->dept_id);
        $dept_select = Department::where('dept_id', 'LIKE', $order_dept.'%')->get();
        return view('repair.dashboard.dashboard_dept', compact('dept_select'));
    }

    public function dashboardEmp($emp_id, $date, $where)
    {
        $status = '';
        if ($where == "Success") {
            $status = 'งานที่เสร็จสิ้น';
            $records = Repair::where('technician_name', 'LIKE', '%'.$emp_id.'%' )
                ->where('order_date' , 'LIKE', $date.'%')
                ->whereIn('status', ['เสร็จสิ้น', 'ผ่านการตรวจสอบ', 'ยกเลิกโดยผู้รับงาน', 'ยกเลิกโดยหัวหน้า', 'ยกเลิกโดยผู้แจ้ง'])
                ->get(['order_id', 'order_address', 'order_type', 'order_tool', 'order_date', 'status']);
        }
        elseif ($where == "Process") {
            $status = 'งานที่กำลังดำเนินการ';
            $records = Repair::where('technician_name', 'LIKE', '%'.$emp_id.'%' )
                ->where('order_date' , 'LIKE', $date.'%')
                ->whereNotIn('status', ['เสร็จสิ้น', 'ผ่านการตรวจสอบ','ยกเลิกโดยผู้รับงาน', 'ยกเลิกโดยหัวหน้า', 'ยกเลิกโดยผู้แจ้ง'])
                ->get(['order_id', 'order_address', 'order_type', 'order_tool', 'order_date', 'status']);
        }
        else {
            $status = 'งานทั้งหมดของเดือน';
            $records = Repair::where('technician_name', 'LIKE', '%'.$emp_id.'%' )
                ->where('order_date' , 'LIKE', $date.'%')
                ->get(['order_id', 'order_address', 'order_type', 'order_tool', 'order_date', 'status']);
        }
        $countList = count($records);

        

        return view('repair.dashboard.dashboard_emp', compact('emp_id', 'date', 'records', 'countList', 'status'));
    }

    public function searchDashboardDept(Request $request)
    {
        if ($request->ajax()) {
            $rows = [];
            $users = auth()->user();
            $order_dept = self::deptFormat($users->dept_id);
            $_doc_date = $request->doc_date;

            if (Auth::User()->roleAdmin() || auth()->user()->emp_id == 500383) {
                $records = Employee::whereIn('dept_id', ['A01100100', 'A03050100', 'A03050200', 'A03060100'])
                    ->where('emp_status', 1)
                    ->where(function ($query) use ($request) {
                        if ($request->dept_category != "") { $query->where('dept_id', '=', $request->dept_category); }
                    })
                    ->orderBy('dept_id' , 'ASC')
                    ->get(['emp_id', 'title', 'name', 'surname', 'nickname', 'dept_id']);
            }else{
                $records = Employee::whereIn('dept_id', ['A01100100', 'A03050100', 'A03050200', 'A03060100'])
                    ->where('dept_id', 'LIKE', $order_dept.'%' )
                    ->where('emp_status', 1)
                    ->where(function ($query) use ($request) {
                        if ($request->dept_category != "") { $query->where('dept_id', '=', $request->dept_category); }
                    })
                    ->orderBy('dept_id' , 'ASC')
                    ->get(['emp_id', 'title', 'name', 'surname', 'nickname', 'dept_id']);
            }
            
            $totalRecords = count($records);
            $allRepairs = Repair::all();
            $statuses = ['เสร็จสิ้น', 'ผ่านการตรวจสอบ','ยกเลิกโดยผู้รับงาน', 'ยกเลิกโดยหัวหน้า', 'ยกเลิกโดยผู้แจ้ง'];

            foreach ($records as $rec) {
                $repairsForEmployee = $allRepairs->filter(function ($repair) use ($rec, $_doc_date) {
                    return (strpos($repair->technician_name, (string)$rec->emp_id) !== false) && ($repair->created_at->format('Y-m') == $_doc_date);
                });
                
                $countAll = $repairsForEmployee->count();

                $countSuccess = $repairsForEmployee->filter(function ($repair) use ($statuses) {
                    return in_array($repair->status, $statuses);
                })->count();
                
                $countProcess = $repairsForEmployee->filter(function ($repair) use ($statuses) {
                    return !in_array($repair->status, $statuses);
                })->count();

                $depts = Department::where('dept_id', '=', $rec->dept_id)->first();
                $fullname = $rec->title.''.$rec->name.' '.$rec->surname;
                $fullname .= ($rec->nickname) ? ' ('.$rec->nickname.')' : "";

                $inputCountAll = self::manageEditorAll($rec->emp_id, $countAll, $_doc_date, "All");
                $inputCountProcess = self::manageEditorProcess($rec->emp_id, $countProcess, $_doc_date, "Process");
                $inputCountSuccess = self::manageEditorSuccess($rec->emp_id, $countSuccess, $_doc_date, "Success");

                // dd($inputCountAll, $inputCountProcess, $inputCountSuccess);
                $rows[] = array(
                    "dept_id" => $depts->dept_name,
                    "emp_id" => $fullname,
                    "count_orderAll" => $inputCountAll,
                    "count_orderProcess" => $inputCountProcess,
                    "count_orderSuccess" => $inputCountSuccess,
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

    // ******************************  Manage  ***************************************** //

    public function manageEditorAll($emp_id, $count, $date, $where){
        $_count = ($count)? $count.' งาน' : '-';
        $result = '<a class="text-danger" href="'.url('repair/dashboard/detail/'.$emp_id.'/'.$date.'/'.$where.'').'" title="ดูรายละเอียด">'.$_count.'</a>';
        return $result;
    }
    public function manageEditorProcess($emp_id, $count, $date, $where){
        $_count = ($count)? $count.' งาน' : '-';
        $result = '<a href="'.url('repair/dashboard/detail/'.$emp_id.'/'.$date.'/'.$where.'').'" title="ดูรายละเอียด">'.$_count.'</a>';
        return $result;
    }
    public function manageEditorSuccess($emp_id, $count, $date, $where){
        $_count = ($count)? $count.' งาน' : '-';
        $result = '<a href="'.url('repair/dashboard/detail/'.$emp_id.'/'.$date.'/'.$where.'').'" title="ดูรายละเอียด">'.$_count.'</a>';
        return $result;
    }

}
