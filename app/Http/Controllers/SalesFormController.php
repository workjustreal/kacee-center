<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesForm;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Jenssegers\Agent\Agent;

class SalesFormController extends Controller
{
    // ********************************** Page ***************************************
    public function index(){
        return view('sales-document.sales-form.index');
    }

    public function indexApprove(){
        return view('sales-document.sales-approve.index');
    }

    public function indexList(){
        return view('sales-document.sales-list.index');
    }

    public function create(){
        return view('sales-document.sales-form.create');
    }

    public function edit($id){
        $datas = SalesForm::where('id', $id)->first();
        return view('sales-document.sales-form.edit', compact('datas'));
    }

    public function show($id){
        $datas = SalesForm::where('id', $id)->first();
        $_pay = self::moneyFormat($datas->pay);
        $status = self::get_status($datas->status);
        $log = DB::table('sales_form_log')->where('sales_form_id', $id)->get();
        return view('sales-document.sales-form.show', compact('datas', '_pay', 'status', 'log'));
    }

    public function print($id)
    {
        $_datas = [];
        $_customer = [];
        $_pay = [];
        $_date = [];
        $_emp_name = [];
        $exp = explode(",", $id);
        for ($i=0; $i < count($exp); $i++) { 
            $datas = SalesForm::join('sales_form_log', 'sales_form_log.sales_form_id', '=', 'sales_form.id')
                ->where('sales_form_log.description', '=', 'print')
                ->where('sales_form_log.sales_form_id', '=', $exp[$i])
                ->groupBy('sales_form.id')
                ->orderBy('sales_form_log.id', 'DESC')
                ->select([
                    'sales_form.*',
                    DB::raw('count(sales_form_log.sales_form_id) AS log_print'),
                    'sales_form_log.emp_id as log_emp_id',
                    'sales_form_log.created_at as log_created_at',
                ])
                ->first();

            array_push($_datas, $datas);
        }
        // dd($_datas);

        $dompdf = App::make('dompdf.wrapper');
        $dompdf->loadView('sales-document.sales-form.pdf', compact('_datas'))
            ->setPaper('a4', 'landscape')
            ->setWarnings(false);
        $agent = new Agent();
        if ($agent->isPhone()) {
            return $dompdf->download($datas->gen_id.' '.date('Y-m-d').'.pdf');
        } else {
            return $dompdf->stream('ฟอร์มอนุมัติ_การลงสินค้าให้ลูกค้าKC.pdf');
        }

    }

    public function showPrintLog(Request $request){
        $getdata = SalesForm::where('id', $request->id)->first();
        $log = DB::table('sales_form_log')->where('sales_form_id', $request->id)->where('description', '=', 'print')->get();

        $rows = [];
        foreach ($log as $value) {
            $_emp_id = Auth::User()->findEmployee($value->emp_id);
            $_created_at = date("d/m/Y H:i:s", strtotime("+543 years", strtotime($value->created_at)));

            $rows[] = array(
                'emp_id' => $_emp_id->name,
                'created_at' => $_created_at,
            );
        }
        $datas = array(
            'data' => $getdata,
            'logdata' => $rows,
        );
        return response()->json($datas);
    }

    // ********************************** Manage ***************************************

    public function manageEditor($id, $status, $page){
        switch ($page) {         
            case 'INDEX':
                switch ($status) {
                    case 'รออนุมัติ':
                        // <a class="action-icon" href="javascript:void(0);" onclick="deleteCarConfirmation(\''.$id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                        $result = '<a class="action-icon" href="'.url('sales-document/sales-form/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>
                            <a class="action-icon" href="'.url('sales-document/sales-form/edit', $id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                            <a class="action-icon" href="javascript:void(0);" onclick="cancelConfirmation(\''.$id.'\', \''."USER".'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>
                        ';
                        break;
                    case 'เสร็จสิ้น':
                        $result = '<a class="action-icon" href="'.url('sales-document/sales-form/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>';
                        break;
                    case 'ยกเลิกโดยผู้แจ้ง':
                        $result = '<a class="action-icon" href="'.url('sales-document/sales-form/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>';
                        break;
                    case 'ยกเลิกโดยผู้อนุมัติ':
                        $result = '<a class="action-icon" href="'.url('sales-document/sales-form/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>';
                        break;
                    case 'ยกเลิกโดยผู้ดูแลระบบ':
                        $result = '<a class="action-icon" href="'.url('sales-document/sales-form/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>';
                        break;
                    
                    default:
                        $result = '<a class="action-icon" href="'.url('sales-document/sales-form/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>';
                        if (Auth::User()->isManager() || Auth::User()->roleAdmin()) {
                            $result .= '<a class="action-icon" href="'.url('sales-document/sales-form/edit', $id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                                <a class="action-icon" href="javascript:void(0);" onclick="cancelConfirmation(\''.$id.'\', \''."USER".'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>
                            ';
                        }
                        break;
                }
                break;
            case 'APPROVE':
                switch ($status) {
                    case 'รออนุมัติ':
                        // <a class="action-icon" href="javascript:void(0);" onclick="deleteCarConfirmation(\''.$id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                        $result = '<a class="action-icon" href="'.url('sales-document/sales-form/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>
                            <a class="action-icon" href="'.url('sales-document/sales-form/edit', $id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                            <a class="action-icon" href="javascript:void(0);" onclick="cancelConfirmation(\''.$id.'\', \''.$page.'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>
                        ';
                        break;
                }
                break;
            case 'HISTORY' :
                switch ($status) {
                    case 'รออนุมัติ':
                        // <a class="action-icon" href="javascript:void(0);" onclick="deleteCarConfirmation(\''.$id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                        $result = '<a class="action-icon" href="'.url('sales-document/sales-form/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>
                            <a class="action-icon" href="'.url('sales-document/sales-form/edit', $id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                            <a class="action-icon" href="javascript:void(0);" onclick="cancelConfirmation(\''.$id.'\', \''.$page.'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>
                        ';
                        break;
                    default:
                        $result = '<a class="action-icon" href="'.url('sales-document/sales-form/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>';
                        break;
                }
                break;
            case 'LIST' :
                $result = '<a class="action-icon" href="'.url('sales-document/sales-form/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>';
                if (Auth::User()->roleAdmin()) {
                    $result .= '
                    <a class="action-icon" href="javascript:void(0);" onclick="cancelConfirmation(\''.$id.'\', \''.$page.'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>
                ';
                }
                break;
            default :
                $result = '<a class="action-icon" href="'.url('sales-document/sales-form/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>';
                break;
        }
        return $result;
    }

    public function manageStatus($status){
        switch ($status) {
            case 'รออนุมัติ':
                $res = '<span class="badge bg-blue">รอรับทราบ</span>';
                break;
            case 'อนุมัติ':
                $res = '<span class="badge bg-blue">อนุมัติ</span>';
                break;
            case 'เสร็จสิ้น':
                $res = '<span class="badge bg-success">เสร็จสิ้น</span>';
                break;
            case 'ยกเลิกโดยผู้แจ้ง':
                $res = '<span class="badge bg-danger">ยกเลิก</span>';
                break;
            case 'ยกเลิกโดยผู้อนุมัติ':
                $res = '<span class="badge bg-danger">ยกเลิก</span>';
                break;
            case 'ยกเลิกโดยผู้ดูแลระบบ':
                $res = '<span class="badge bg-danger">ยกเลิก</span>';
                break;
            default:
                $res = '';
                break;
        }
        return $res;
    }

    public function get_status($name){
        $status = array(
            [
                "id" => "E",
                "name" => "รออนุมัติ",
                "text" => "white",
                "color" => "blue",
                "bg" => "bg-blue",
                "badge" => "badge-soft-blue rounded-pill",
            ],
            [
                "id" => "A1",
                "name" => "อนุมัติ",
                "text" => "white",
                "color" => "primary",
                "bg" => "bg-primary",
                "badge" => "badge-soft-primary rounded-pill",
            ],
            [
                "id" => "S1",
                "name" => "เสร็จสิ้น",
                "text" => "white",
                "color" => "success",
                "bg" => "bg-success",
                "badge" => "badge-soft-success rounded-pill",
            ],
        );
        if ($name == null) {
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
                "name" => "ยกเลิกโดยผู้อนุมัติ",
                "text" => "white",
                "color" => "danger",
                "bg" => "bg-danger",
                "badge" => "badge-soft-danger rounded-pill",
            ];
            $status[] = [
                "id" => "C3",
                "name" => "ยกเลิกโดยผู้ดูแลระบบ",
                "text" => "white",
                "color" => "danger",
                "bg" => "bg-danger",
                "badge" => "badge-soft-danger rounded-pill",
            ];
            $key = array_search($name, array_column($status, 'name'));
            return $status[$key];
        }
    }

    public function count_print($id, $sum){
        $count = '<a class="action-icon" href="#" data-bs-toggle="modal" data-bs-target="#logPrintModal" onclick="logPrint(\''.$id.'\')" title="ดูรายละเอียด"> 
                <i class="mdi mdi-clipboard-list-outline"></i></a>';
        $sum .= $count;
        return $sum;
    }
    
    // ********************************** Search ***************************************

    public function createAutocomplete(Request $request){
        $search = $request->get('search');
        $result = DB::table('ex_customer')->where('cuscod', 'LIKE', '%'.$search.'%')
            ->orderBy('cuscod', 'ASC')
            ->take(10)
            ->get(['cuscod', 'prenam', 'cusnam']);
        return response()->json($result);
    }

    // MENU 1
    public function search(Request $request){
        if ($request->ajax()) {   
            
            $user = auth()->user();   
            $_doc_date = self::dateMonthFormat($request->doc_date);
            $data = SalesForm::where('id', '<>', '')->where('emp_id', $user->emp_id);
            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('*')
                ->where('created_at', 'LIKE', '%'.$_doc_date.'%' ) 
                ->where(function ($query) use ($request) {
                    if ($request->status_category) { 
                        if ($request->status_category == 'ยกเลิก') {
                            $query->whereIn('status', ['ยกเลิกโดยผู้แจ้ง','ยกเลิกโดยผู้อนุมัติ'] );
                        } else {
                            $query->where('status', '=', $request->status_category);
                        }
                    }
                })
                ->orderBy('id', 'DESC')
                ->get();
            
            $rows = [];
            foreach ($records as $rec) {
                $_date = self::dateFormat($rec->created_at);
                $_pay = self::moneyFormat($rec->pay);
                $_status = self::manageStatus($rec->status);
                $_action = self::manageEditor($rec->id, $rec->status, 'INDEX');
                $rows[] = array(
                    "gen_id" => $rec->gen_id,
                    "customer_code" => $rec->customer_code,
                    "customer_name" => $rec->customer_name,
                    "invoice" => $rec->invoice,
                    "pay" => $_pay,
                    "created_at" => $_date,
                    "status" => $_status,
                    "action" => $_action
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

    // MENU 2
    // public function searchWait(Request $request){
    //     if ($request->ajax()) {
    //         $_user = auth()->user();   
    //         $_dept = self::deptFormat($_user->dept_id);
            
    //         $data = SalesForm::where('id', '<>', '');
    //         $totalRecords = $data->select('count(*) as allcount')->count();
    //         $records = $data->select('*')
    //             ->where('emp_dept_id', 'LIKE', $_dept.'%')
    //             ->where('status', 'รออนุมัติ')
    //             ->orderBy('id', 'DESC')
    //             ->get();
            
    //         $rows = [];
    //         foreach ($records as $rec) {
    //             $_emp_name = self::empNameFormat($rec->emp_id);
    //             $_date = self::dateFormat($rec->created_at);
    //             $_pay = self::moneyFormat($rec->pay);
    //             $_status = self::manageStatus($rec->status);
    //             $_action = self::manageEditor($rec->id, $rec->status, 'APPROVE');
    //             $rows[] = array(
    //                 "ID" => $rec->id,
    //                 "gen_id" => $rec->gen_id,
    //                 "customer_code" => $rec->customer_code,
    //                 "customer_name" => $rec->customer_name,
    //                 "invoice" => $rec->invoice,
    //                 "pay" => $_pay,
    //                 "emp_id" => $_emp_name,
    //                 "created_at" => $_date,
    //                 "status" => $_status,
    //                 "action" => $_action
    //             );
    //         }

    //         $response = array(
    //             "total" => $totalRecords,
    //             "totalNotFiltered" => $totalRecords,
    //             "rows" => $rows,
    //         );
    //         return response()->json($response);
    //     }
    // }

    public function searchAll(Request $request){
        if ($request->ajax()) {
            $_user = auth()->user(); 
            $_dept = self::deptFormat($_user->dept_id);
            $_doc_date = self::dateMonthFormat($request->doc_date);

            $data = SalesForm::where('id', '<>', '');
            $totalRecords = $data->select('count(*) as allcount')->count();

            if (Auth::User()->roleAdmin()) {
                $records = $data->select('*')
                ->where('created_at', 'LIKE', '%'.$_doc_date.'%' )
                ->where(function ($query) use ($request) {
                    if ($request->status_category) { 
                        if ($request->status_category == 'ยกเลิก') {
                            $query->whereIn('status', ['ยกเลิกโดยผู้แจ้ง','ยกเลิกโดยผู้อนุมัติ'] );
                        } else {
                            $query->where('status', '=', $request->status_category);
                        }
                    }
                })
                ->orderBy('id', 'DESC')
                ->get();
            }else{
                $records = $data->select('*')
                ->where('emp_dept_id', 'LIKE', $_dept.'%')
                ->where('created_at', 'LIKE', '%'.$_doc_date.'%' )
                ->where(function ($query) use ($request) {
                    if ($request->status_category) { 
                        if ($request->status_category == 'ยกเลิก') {
                            $query->whereIn('status', ['ยกเลิกโดยผู้แจ้ง','ยกเลิกโดยผู้อนุมัติ'] );
                        } else {
                            $query->where('status', '=', $request->status_category);
                        }
                    }
                })
                ->orderBy('id', 'DESC')
                ->get();
            }

            $rows = [];
            foreach ($records as $rec) {
                $_emp_name = self::empNameFormat($rec->emp_id);
                $_emp_dept_id = self::deptNameFormat($rec->emp_dept_id);
                $_date = self::dateFormat($rec->created_at);
                $_pay = self::moneyFormat($rec->pay);
                $_status = self::manageStatus($rec->status);
                $_action = self::manageEditor($rec->id, $rec->status, 'HISTORY');
                $rows[] = array(
                    "gen_id" => $rec->gen_id,
                    "customer_code" => $rec->customer_code,
                    "customer_name" => $rec->customer_name,
                    "invoice" => $rec->invoice,
                    "emp_dept_id" => $_emp_dept_id,
                    "emp_id" => $_emp_name,
                    "created_at" => $_date,
                    "status" => $_status,
                    "action" => $_action
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

    // MENU 3
    public function searchListAcknowledge(Request $request){
        if ($request->ajax()) {
            $_user = auth()->user(); 
            $_dept = self::deptFormat($_user->dept_id);
            $data = SalesForm::where('id', '<>', '');
            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('*')
                ->where('status', '=', 'รออนุมัติ')
                ->orderBy('id', 'DESC')
                ->get();
            
            $rows = [];
            foreach ($records as $rec) {
                $_emp_name = self::empNameFormat($rec->emp_id);
                $_emp_dept_id = self::deptNameFormat($rec->emp_dept_id);
                $_date = self::dateFormat($rec->created_at);
                $_pay = self::moneyFormat($rec->pay);
                $_status = self::manageStatus($rec->status);
                $_action = self::manageEditor($rec->id, $rec->status, 'LIST');
                $rows[] = array(
                    "ID" => $rec->id,
                    "gen_id" => $rec->gen_id,
                    "customer_code" => $rec->customer_code,
                    "customer_name" => $rec->customer_name,
                    "invoice" => $rec->invoice,
                    "emp_dept_id" => $_emp_dept_id,
                    "emp_id" => $_emp_name,
                    "created_at" => $_date,
                    "status" => $_status,
                    "action" => $_action
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

    public function searchList(Request $request){
        if ($request->ajax()) {
            $_user = auth()->user(); 
            $_dept = self::deptFormat($_user->dept_id);
            $_doc_date = self::dateBetweenFormat($request->doc_date);
            $records = '';
            switch ($request->status_category) {
                case '1':
                    $records = 'having des_list not like "%print%"';
                    break;
                case '2':
                    $records = 'having des_list like "%print%"';
                    break;
                
                default:
                    $records = '';
                    break;
            }
            $data = DB::select('SELECT DISTINCT sales_form.*, group_concat(sales_form_log.description) as des_list, sum(IF(sales_form_log.description = "print", 1, 0)) AS log_print FROM sales_form JOIN sales_form_log ON sales_form.id = sales_form_log.sales_form_id WHERE sales_form.id <> "" AND sales_form.status = "เสร็จสิ้น" AND sales_form.submit_date BETWEEN ? AND ? GROUP BY sales_form.id '.$records.' ORDER BY sales_form.id DESC', [ 
                    $_doc_date['date_start'], 
                    $_doc_date['date_end'],
                ]);
            $totalRecords = count($data);

            $rows = [];
            foreach ($data as $rec) {
                $_emp_name = self::empNameFormat($rec->emp_id);
                $_emp_dept_id = self::deptNameFormat($rec->emp_dept_id);
                $_date = self::dateFormat($rec->created_at);
                $_pay = self::moneyFormat($rec->pay);
                $_status = self::manageStatus($rec->status);
                $_action = self::manageEditor($rec->id, $rec->status, 'LIST');
                $_count_print = self::count_print($rec->id, $rec->log_print);

                $rows[] = array(
                    "ID" => $rec->id,
                    "_STATUS" => $rec->status,
                    "gen_id" => $rec->gen_id,
                    "customer_code" => $rec->customer_code,
                    "customer_name" => $rec->customer_name,
                    "invoice" => $rec->invoice,
                    "emp_dept_id" => $_emp_dept_id,
                    "emp_id" => $_emp_name,
                    "created_at" => $_date,
                    "count_print" => $_count_print,
                    "status" => $_status,
                    "action" => $_action
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

    // ********************************** Format ***************************************

    public function empNameFormat($emp_id){
        $user = Auth::User()->findEmployee( $emp_id );

        if ($user->nickname) {
            $_name = $user->name.' '.$user->surname.' ( ' . $user->nickname . ' )';
        } 
        else { 
            $_name = $user->name.' '.$user->surname; 
        }
        return $_name;
    }

    public function deptNameFormat($dept){
        $_data = Department::where('dept_id', $dept)->first();
        return $_data->dept_name;
    }

    public function dateFormat($date){
        $day = Carbon::parse($date)->format('d');
        $month = Carbon::parse($date)->locale('th_TH')->isoFormat('MMMM');
        $year = Carbon::parse($date)->format('Y') + 543;
        return $day.' '.$month.' '.$year;
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
        $today = date('Y-m-d');
        $date_start = date('Y-m-d', strtotime($today . "-1 day"));
        $date_end = date('Y-m-d', strtotime($today . "+1 day"));
        if ($date != "") {
            if (str_contains($date, "ถึง")) {
                $d = explode("ถึง", $date);
                $dStart = explode("/", trim($d[0]) ); 
                $dEnd = explode("/", trim($d[1]) ); 

                $date_start = date('Y-m-d', strtotime($dStart[2]."-".$dStart[1]."-".$dStart[0]));
                $date_end = date('Y-m-d', strtotime($dEnd[2]."-".$dEnd[1]."-".($dEnd[0] + 1) ));
            } 
            else{
                $dStart = explode("/", $date); 
                $date_start = date('Y-m-d', strtotime($dStart[2]."-".$dStart[1]."-".$dStart[0]));
                $date_end = date('Y-m-d', strtotime($dStart[2]."-".$dStart[1]."-".($dStart[0] + 1)));
            }
        }

        $result = array(
            'date_start' => $date_start,
            'date_end' => $date_end
        );

        return $result;
    }

    public function moneyFormat($amount){
        return number_format($amount);
    }

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

    // ********************************** Actoin ***************************************

    public function store(Request $request){
        $user = auth()->user();
        
        if ($request->SQL == 'INS') {
            $autoIncId = 1;
            $data = SalesForm::where('id', '<>', '')->first();
            if ($data) {
                $categoryId = SalesForm::orderByDesc('id')->first();
                $autoIncId = $categoryId->id + 1;
            } 
            else{
                $autoIncId = 1;
            }
            $gen_dept_id = "SF".date('ym')."000".$autoIncId; 
            
            $_sf = new SalesForm();
            $_sf->gen_id = $gen_dept_id;
            $_sf->customer_code = $request->customer_code;
            $_sf->customer_name = $request->customer_name;
            $_sf->invoice = $request->invoice;
            $_sf->emp_id = $user->emp_id;
            $_sf->emp_dept_id = $user->dept_id;
            $_sf->pay = $request->pay;
            $_sf->comment = $request->comment;
            $_sf->status = "รออนุมัติ";
            $_sf->save();
            self::createLog($autoIncId, "รออนุมัติ", $user->emp_id);

            // if( Auth::User()->isManager() ){
            //     $_sf->status = "อนุมัติ";
            //     $_sf->approve_id = $user->emp_id;
            //     $_sf->approve_date = date('Y-m-d H:i:s');
            //     self::createLog($autoIncId, "อนุมัติ", $user->emp_id);
            // }else{
            //     $_sf->status = "รออนุมัติ";
            //     self::createLog($autoIncId, "รออนุมัติ", $user->emp_id);
            // }

            
            alert()->success('เพิ่มข้อมูลเรียบร้อย');
        }
        elseif ($request->SQL == 'EDIT') {
            SalesForm::where('id', '=', $request->ID)->update([
                "customer_code" => $request->customer_code,
                "customer_name" => $request->customer_name,
                "invoice" => $request->invoice,
                "pay" => $request->pay,
                "comment" => $request->comment,
            ]);

            self::createLog($request->ID, "แก้ไข", $user->emp_id);
            alert()->success('แก้ไขข้อมูลเรียบร้อย');
        }

        return redirect('sales-document/sales-form');
    }

    public function submit(Request $request){
        $user = auth()->user();
        $_id = count($request->id);
        for ($i=0; $i < $_id; $i++) { 
            SalesForm::where('id', $request->id[$i])->update([
                "approve_id" => $user->emp_id,
                "approve_date" => date('Y-m-d H:i:s'),
                "status" => 'อนุมัติ',
            ]);
            self::createLog( $request->id[$i], 'อนุมัติ', $user->emp_id);
        }
        
        return response()->json(['success' => true,'message' => 'อนุมัติรายการเรียบร้อย',]);
    }
    
    public function submitList(Request $request){
        $user = auth()->user();
        $_id = count($request->id);
        for ($i=0; $i < $_id; $i++) { 
            SalesForm::where('id', $request->id[$i])->update([
                "submit_id" => $user->emp_id,
                "submit_date" => date('Y-m-d H:i:s'),
                "status" => 'เสร็จสิ้น',
            ]);
            self::createLog( $request->id[$i], 'เสร็จสิ้น', $user->emp_id);
        }
        
        return response()->json(['success' => true,'message' => 'รับทราบรายการเรียบร้อย',]);
    }

    public function cancel($id, $page){
        if ($id != "") {
            $user = auth()->user();  
            switch ($page) {
                case 'USER':
                    SalesForm::where('id', '=', $id)->update([
                        "status" => 'ยกเลิกโดยผู้แจ้ง',
                    ]);
                    
                    self::createLog($id, 'ยกเลิกโดยผู้แจ้ง', $user->emp_id);
                    return response()->json(['success' => true,'message' => 'ยกเลิกข้อมูลเรียบร้อย',]);
                    break;

                case 'HISTORY':
                    SalesForm::where('id', '=', $id)->update([
                        "approve_id" => $user->emp_id,
                        "approve_date" => date('Y-m-d H:i:s'),
                        "status" => 'ยกเลิกโดยผู้อนุมัติ',
                    ]);

                    self::createLog($id, 'ยกเลิกโดยผู้อนุมัติ', $user->emp_id);
                    return response()->json(['success' => true,'message' => 'ยกเลิกข้อมูลเรียบร้อย',]);
                    break;

                case 'LIST':
                    SalesForm::where('id', '=', $id)->update([
                        "status" => 'ยกเลิกโดยผู้ดูแลระบบ',
                    ]);

                    self::createLog($id, 'ยกเลิกโดยผู้ดูแลระบบ', $user->emp_id);
                    return response()->json(['success' => true,'message' => 'ยกเลิกข้อมูลเรียบร้อย',]);
                    break;
            }
        }
    }

    public function createLog($id, $des, $emp_id){
        DB::table('sales_form_log')->insert([
            'sales_form_id' => $id, 
            'description' => $des,
            'emp_id' => $emp_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function printLog(Request $request){
        $exp = explode(",", $request->id);
        for ($i=0; $i < count($exp); $i++) { 
            DB::table('sales_form_log')->insert([
                'sales_form_id' => $exp[$i], 
                'description' => 'print',
                'emp_id' => auth()->user()->emp_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
