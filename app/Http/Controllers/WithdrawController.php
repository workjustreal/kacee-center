<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Withdraw;
use App\Models\Repair;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class WithdrawController extends Controller
{
    public function indexId($oid, Request $request){
        // dd($request->a);
        $repairs = Repair::where('order_id', '=', $oid)->first('status');
        $count_data = Withdraw::where('repair_order_id', '=', $oid)->count();
        return view('withdraw.withdraw-list', compact('oid', 'repairs', 'count_data','request'));
    }

    public function index(){
        $order_dept = self::searchDept();
        $dept_select = Department::where('dept_id', 'LIKE', $order_dept.'%')->get(); 
        return view('withdraw.withdraw', compact('dept_select'));
    }

    public function create($oid){
        // $items = Withdraw::where('repair_order_id', '=', $oid)->get();
        // return view('withdraw.withdraw-create', compact('oid', 'items'));
        return view('withdraw.withdraw-create', compact('oid'));
    }

    public function edit($id) {
        $item = Withdraw::where('withdraw_id', '=', $id)->first();
        $oid = $item->repair_order_id;
        return view('withdraw.withdraw-edit', compact('item', 'oid'));
    }

    // **************************** search *******************************

    public function searchWithdraw(Request $request) {
        if ($request->ajax()) {
            $records = [];
            $emp = auth()->user();
            $records = Withdraw::leftJoin('employee', 'employee.emp_id', '=', 'withdraw.emp_id')
                ->where('repair_order_id', '=', $request->oid)
                ->select(['withdraw.*', 'employee.name', 'employee.nickname'])
                ->get();
            $totalRecords = count($records);
            $rows = [];
            foreach ($records as $rec) {
                $day = Carbon::parse($rec->withdraw_date)->format('d');
                $month = Carbon::parse($rec->withdraw_date)->locale('th_TH')->isoFormat('MMM');
                $year = Carbon::parse($rec->withdraw_date)->format('Y') + 543;

                if ($rec->nickname) { $fullname = $rec->name.'( ' . $rec->nickname . ' )';} else { $fullname = $rec->name; }
                if ($rec->status_inventory == 0) { $inventory = 'Stock'; } else { $inventory = 'New'; }

                $action = self::manageEditor($rec->withdraw_id, $rec->emp_id, $emp->emp_id, $request->ostatus);
                $rows[] = array(
                    "products_name" => $rec->products_name,
                    "prices" => number_format($rec->prices),
                    "qty" => $rec->qty,
                    "total_prices" => number_format($rec->total_prices),
                    "status_inventory" => $inventory,
                    "emp_id" => $fullname,
                    "withdraw_date" =>  $day.' '.$month.' '.$year,
                    "comment" => $rec->comment,
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

    public function searchWithdrawAll(Request $request) {
        if ($request->ajax()) {
            $emp = auth()->user();
            $date = date('Y-m-d');
            $records = Withdraw::where('withdraw_id', '<>', '')
                ->where(function ($query) use ($request, $date) {
                    if ($request->dept_category != "") { $query->where('dept_id', '=', $request->dept_category); }
                    if ($request->doc_date != "") { 
                        $query->whereYear('withdraw_date', date('Y', strtotime($request->doc_date)))
                              ->whereMonth('withdraw_date', date('m', strtotime($request->doc_date)));
                    } else { 
                        $query->whereYear('withdraw_date', date('Y', strtotime($date)))
                              ->whereMonth('withdraw_date', date('m', strtotime($date)));
                    }
                }) 
                ->selectRaw('
                    repair_order_id,
                    dept_id,
                    GROUP_CONCAT(DISTINCT emp_id) as emp_id, 
                    GROUP_CONCAT(DISTINCT withdraw_date) as withdraw_date,
                    count(repair_order_id) as repair_order_count,
                    sum(qty) as qty, 
                    sum(total_prices) as total_prices
                ')
                ->groupBy('repair_order_id')
                ->get();
                
            $totalRecords = count($records);
            $rows = [];
            foreach ($records as $rec) {
                $data_dept = Auth::User()->findDepartment($rec->dept_id);
                $empId = self::findEmployee($rec->emp_id);
                // dd($data_emp);
                $action = '<a class="action-icon" href="'.url('repair/withdraw-list', $rec->repair_order_id).'?a=1" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>';
                $rows[] = array(
                    "repair_order_id" => $rec->repair_order_id,
                    "dept_id" => $data_dept->dept_name,
                    "repair_order_count" => $rec->repair_order_count,
                    "qty" => $rec->qty,
                    "total_prices" => number_format($rec->total_prices),
                    "emp_id" => $empId,
                    "withdraw_date" =>  $rec->withdraw_date,
                    "action" =>  $action
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

    // **************************** manage *******************************

    public function manageEditor($id, $uid, $emp_id, $ostatus){
        $result = '';
        if ($ostatus == 'ดำเนินการ') {
            if (Auth::User()->isLeader() || Auth::User()->roleAdmin() || $uid == $emp_id) {
                $result = '<a class="action-icon text-warning" href="'.url('repair/withdraw-edit', $id).'" title="แก้ไข"><i class="fas fa-edit"></i></a>
                <a class="action-icon text-danger" onclick="deleteConfirmation('.$id.')" title="ลบ"><i class="far fa-trash-alt"></i></a>';
            }
        }
        elseif ($ostatus == 'รอตรวจสอบ') {
            if (Auth::User()->isLeader() || Auth::User()->roleAdmin()) {
                $result = '<a class="action-icon text-warning" href="'.url('repair/withdraw-edit', $id).'" title="แก้ไข"><i class="fas fa-edit"></i></a>
                <a class="action-icon text-danger" onclick="deleteConfirmation('.$id.')" title="ลบ"><i class="far fa-trash-alt"></i></a>';
            }
        }
        return $result;
    }

    public function searchDept(){
        $order_dept = '';
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
        return $order_dept;
    }

    public function findEmployee($emp_id){
        $emp_id = explode(',', $emp_id);
        $empDatas = Employee::whereIn('emp_id', $emp_id)->get();
        $rows = [];
        foreach ($empDatas as $item) {
            if ($item->nickname) { 
                $fullname = $item->name.'( ' . $item->nickname . ' )';
            } else { 
                $fullname = $item->name; 
            }
            $rows[] = $fullname;
        }
        $row = implode(',', $rows);
        return $row;
    }

    public function printPDF($oid)
    {
        $data = Withdraw::where('repair_order_id', '=', $oid)->get();
        $data_dept = '';
        if (count($data) > 0 ) {
            $data_dept = Auth::User()->findDepartment($data[0]->dept_id);
        }
        $dompdf = App::make('dompdf.wrapper');
        $dompdf->loadView('withdraw.withdraw-pdf', compact('data', 'data_dept'))
            ->setPaper('a4', 'portrait')->setWarnings(false);
        $agent = new Agent();
        if ($agent->isPhone()) {
            return $dompdf->download('ใบเบิกอุปกรณ์แจ้งซ่อม_'.$oid.'.pdf');
        } else {
            return $dompdf->stream('ใบเบิกอุปกรณ์แจ้งซ่อม_'.$oid.'.pdf');
        }

    }

    // ***************************** action ******************************

    public function store(Request $request) {
        $user = auth()->user();
        $dept_id = '';
    
        if (strpos($request->repair_id, 'BC') !== false) { $dept_id = 'A03050200'; }
        else if(strpos($request->repair_id, 'BE') !== false) { $dept_id = 'A03050100'; }
        else if(strpos($request->repair_id, 'MT') !== false) { $dept_id = 'A03060100'; }
        else if(strpos($request->repair_id, 'IT') !== false) { $dept_id = 'A01100100'; }
    
        // INSERT Data
        $repair = new Withdraw();
        $repair->repair_order_id = $request->repair_id;
        $repair->products_name = $request->products_name;
        $repair->qty = $request->qty;
        $repair->prices = $request->prices;
        $repair->total_prices = $request->total_prices;
        $repair->comment = $request->comment;
        $repair->withdraw_date = $request->withdraw_date;
        $repair->dept_id = $dept_id;
        $repair->emp_id = $user->emp_id;
        $repair->status = "รอตรวจสอบ";
        $repair->status_inventory = $request->status_inventory;
        $repair->save();
    
        alert()->success('เพิ่มข้อมูลเรียบร้อย');
        return redirect()->route('withdraw.withdraw-list', ['id' => $request->repair_id]);
    }


    public function update(Request $request) {
        // dd($request);
        $user = auth()->user();
        $updateData = [
            'prices' => $request->prices,
            'total_prices' => $request->total_prices,
            'comment' => $request->comment,
            'products_name' => $request->products_name,
            'qty' => $request->qty,
            'status_inventory' => $request->status_inventory,
            'approve_id' => $user->emp_id,
            'status' => 'ผ่านการตรวจสอบ',
        ];
        $updatedRows = Withdraw::where('withdraw_id', $request->id)->update($updateData);
        alert()->success('อัพเดทข้อมูลเรียบร้อย');
        return redirect()->route('withdraw.withdraw-list', ['id' => $request->oid]);
    }

    public function destroy($id) {
        $withdrawItem = Withdraw::where('withdraw_id', $id);
        if(!$withdrawItem) {
            return response()->json(['status' => 'error', 'message' => 'Item not found'], 404);
        }
        $withdrawItem->delete();
        return response()->json(['status' => 'success', 'message' => 'Item deleted successfully']);
    }
    

}
